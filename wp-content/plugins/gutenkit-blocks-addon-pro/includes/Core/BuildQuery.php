<?php
namespace GutenkitPro\Core;

defined( 'ABSPATH' ) || exit;

class BuildQuery {

    use \Gutenkit\Traits\Singleton;

    public $query_args = [];

    public function get_complex_select_value($value, $isMultiple = false) {
        if ($isMultiple && is_array($value)) {
            return array_column($value, 'value');
        }
        return $value['value'] ?? '';
    }

    public function get_tax_query($params) {
        $tax_query = [];

        foreach ($params ?? [] as $value) {
            $tax_query_args = $value['taxQuery'] ?? [];
            $operator = $this->get_complex_select_value($value['taxQueryOperator']) ?: 'IN';
            // Set default values for taxonomy and terms
            $taxonomy = $this->get_complex_select_value($tax_query_args['taxonomy'] ?? 'category');
            $terms = $this->get_complex_select_value($tax_query_args['terms'] ?? [], true);

            // Only add the tax query if terms are not empty
            if (!empty($terms)) {
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',  // Default to 'slug' unless you want to make it configurable
                    'terms' => $terms,
                    'include_children' => isset($tax_query_args['includeChildren']) ? (bool)$tax_query_args['includeChildren'] : false,
                    'operator' => $operator,  // Default to 'IN' for operator
                ];
            }
        }
        return $tax_query;
    }
    

    public function get_meta_query($params) {
        $meta_query = [];
    
        foreach ($params ?? [] as $meta_query_args) {
            $key = $meta_query_args['metaKey'] ?? '';
            $value = $meta_query_args['metaValue'] ?? '';
    
            // Ensure that both metaKey and metaValue are non-empty
            if (!empty($key) && !empty($value)) {
                // Ensure that metaQueryCompare exists and has the necessary keys
                $meta_type = $meta_query_args['metaQueryCompare']['metaType'] ?? 'CHAR';
                $meta_compare = $meta_query_args['metaQueryCompare']['metaCompare'] ?? '=';
    
                // If the compare type is 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN', handle value as an array
                if (in_array($meta_compare, ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'])) {
                    $value_array = array_map('trim', explode(',', $value));
                } else {
                    // Otherwise, handle value as a string
                    $value_array = trim($value);
                }
    
                $meta_query[] = [
                    'key'     => $key,
                    'value'   => $value_array,
                    'type'    => $this->get_complex_select_value($meta_type),
                    'compare' => $this->get_complex_select_value($meta_compare),
                ];
            }
        }
    
        return $meta_query;
    }
    
    
    

    public function get_date_query($params) {
        $date_query = [];
    
        foreach ($params ?? [] as $value) {
            $query_part = [];
    
            // Add only non-empty date parts
            if (!empty($value['year'])) {
                $query_part['year'] = $value['year'];
            }
            if (!empty($value['month'])) {
                $query_part['month'] = $value['month'];
            }
            if (!empty($value['week'])) {
                $query_part['week'] = $value['week'];
            }
            if (!empty($value['day'])) {
                $query_part['day'] = $value['day'];
            }
            if (!empty($value['hour'])) {
                $query_part['hour'] = $value['hour'];
            }
            if (!empty($value['minute'])) {
                $query_part['minute'] = $value['minute'];
            }
            if (!empty($value['second'])) {
                $query_part['second'] = $value['second'];
            }
    
            // Add the comparison operator if it's present, otherwise use '=' as default
            $query_part['compare'] = $this->get_complex_select_value($value['dateCompare']) ?? '=';
    
            // Only add non-empty query parts to the final date query
            if (!empty($query_part)) {
                $date_query[] = $query_part;
            }
        }
    
        return $date_query;
    }
    

    public function posts_query($params, $return_posts = false) {
        $params = !empty($params['query']) ? json_decode($params['query'] ?? '{}', true) : [];
        $default_posts_per_page = get_option('posts_per_page');
        // Check if 'paginationPage' (custom static page) is set, default to 1 if not
        $paginationPage = isset($params['paginationPage']) ? (int) $params['paginationPage'] : 1;

        // Check if 'paged' is set (normal WordPress pagination)
        $paged = get_query_var('paged') ? get_query_var('paged') : (isset($_GET['gkit-query-page']) ? $_GET['gkit-query-page'] : 1);

        // If both 'paginationPage' and 'paged' are set, adjust 'paged' to start from 'paginationPage'
        if ($paginationPage > 1 && $paged == 1) {
            $paged = $paginationPage;
        }

        $query_args = [
            'post_type' => $this->get_complex_select_value($params['postType'] ?? 'post', true),
            'post__in' => $this->get_complex_select_value($params['postsIn'] ?? [], true),
            'post_parent__in' => $this->get_complex_select_value($params['parentIn'] ?? [], true),
            'post_parent' => $params['postParent'] ?? [],
            'post__not_in' => $this->get_complex_select_value($params['postNotIn'] ?? [], true),
            'post_parent__not_in' => $this->get_complex_select_value($params['postNotInParent'] ?? [], true),
            'author' => $params['author'] ?? "",
            'author_name' => $params['authorName'] ?? "",
            'author__in' => $this->get_complex_select_value($params['authorIn'] ?? [], true),
            'author__not_in' => $this->get_complex_select_value($params['authorNotIn'] ?? [], true),
            'post_status' => $this->get_complex_select_value($params['postStatus'] ?? 'publish', true),
            'posts_per_page' => $params['postsPerPage'] ?? $default_posts_per_page,
            'ignore_sticky_posts' => !empty($params['ignoreStickyPosts']),
            'offset' => $params['paginationOffset'] ?? 0,
            'paged' => $paged,
        ];

        $tax_query = $this->get_tax_query($params['taxRepeaterFields'] ?? []);
        if ($tax_query) {
            $query_args['tax_query'] = ['relation' => $params['taxRelation'] ?? 'AND'] + $tax_query;
        }

        $meta_query = $this->get_meta_query($params['metaRepeaterFields'] ?? []);
        if ($meta_query) {
            $query_args['meta_query'] = ['relation' => $params['metaRelation'] ?? 'AND'] + $meta_query;
        }

        $date_query = $this->get_date_query($params['dateRepeaterFields'] ?? []);
        if ($date_query) {
            $query_args['date_query'] = ['relation' => $params['dateRelation'] ?? 'AND'] + $date_query;
        }

        if (!empty($params['attachmentPostType'])) {
            $query_args['post_mime_type'] = $this->get_complex_select_value($params['attachmentPostType'], true);
        }

        if (!empty($params['numberOfComments'])) {
            $query_args['comment_count'] = [
                'value' => $params['numberOfComments'],
                'compare' => $this->get_complex_select_value($params['commentAmountCompare'] ?? '=')
            ];
        }

        if (!empty($params['searchKeyword'])) {
            $query_args['s'] = $params['searchKeyword'];
        }

        if (!empty($params['orderBy']) || !empty($params['order'])) {
            $query_args['orderby'] = $this->get_complex_select_value($params['orderBy']) ?? 'date';
            $query_args['order'] = $this->get_complex_select_value($params['order']) ?? 'DESC';
        }

        if (!empty($params['hasPassword'])) {
            switch ($params['hasPassword']) {
                case "yes":
                    $query_args['has_password'] = true;
                    break;
                case "no":
                    $query_args['has_password'] = false;
                    break;
                case "specific":
                    $query_args['post_password'] = $params['postPassword'] ?? '';
                    break;
            }
        }
        
        $query = new \WP_Query(array_filter($query_args));

        if ($return_posts) {
            $posts = [];
            
            if ($query->have_posts()) {
                foreach ($query->get_posts() as $post) {
                    $post->class_list = get_post_class('', $post);
                    $posts[] = $post;
                }
            }

            return $posts;
        }

        return $query;
    }

    public function tax_query($params) {
        $query_args = [
            'taxonomy' => $this->get_complex_select_value($params['taxonomy'] ?? 'category'),
            'hide_empty' => $params['hideEmpty'] ?? false,
        ];

        return (new \WP_Term_Query($query_args))->get_terms();
    }
}
