<?php

namespace GutenkitPro\Config;

defined('ABSPATH') || exit;

/**
 * Register blocks class
 *
 * @since 0.1.0
 * @return void
 */

class Blocks
{

	use \GutenkitPro\Traits\Singleton;

	// class initilizer method
	public function __construct()
	{
		add_filter('gutenkit/blocks/list', array($this, 'set_pro_blocks_list'));
	}

	// set blocks list
	public function set_pro_blocks_list($blocks)
	{
		$blocks_list = array_merge($blocks, $this->get_blocks_list());
		return $blocks_list;
	}

	public function get_blocks_list()
	{
		$blocks = [
			'fancy-animated-text' => array(
				'slug'            => 'fancy-animated-text',
				'title'           => 'Fancy Animated Text',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active'
			),
			'stylish-list' => array(
				'slug'            => 'stylish-list',
				'title'           => 'Stylish List',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'stylish-list-item' => array(
				'slug'            => 'stylish-list-item',
				'title'           => 'Stylish List Item',
				'parent'          => 'stylish-list',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'google-map' => array(
				'slug'            => 'google-map',
				'title'           => 'Google Map',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'creative-button' => array(
				'slug'            => 'creative-button',
				'title'           => 'Creative Button',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'breadcrumb' => array(
				'slug'            => 'breadcrumb',
				'title'           => 'Breadcrumb',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'timeline' => array(
				'slug'            => 'timeline',
				'title'           => 'Timeline',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'image-hover-effect' => array(
				'slug'            => 'image-hover-effect',
				'title'           => 'Image Hover Effect',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'price-menu' => array(
				'slug'            => 'price-menu',
				'title'           => 'Price Menu',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'price-menu-item' => array(
				'slug'            => 'price-menu-item',
				'title'           => 'Price Menu Item',
				'package'         => 'pro',
				'category'        => 'general',
				'parent'          => 'price-menu',
				'status'          => 'active',
			),
			'flip-box' => array(
				'slug'            => 'flip-box',
				'title'           => 'Flip Box',
				'package'         => 'pro',
				'category'  	  => 'general',
				'status'          => 'active',
			),
			'rating' => array(
				'slug'     => 'rating',
				'title'    => 'Rating',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'active',
			),
			'whatsapp' => array(
				'slug'            => 'whatsapp',
				'title'           => 'WhatsApp',
				'package'         => 'pro',
				'category'  => 'general',
				'status' 	=> 'active',
			),
			'reading-progress-bar' => array(
				'slug'     => 'reading-progress-bar',
				'title'    => 'Reading Progress Bar',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'active',
			),
			'advanced-masonry' => array(
				'slug'            => 'advanced-masonry',
				'title'           => 'Advanced Masonry',
				'package'         => 'pro',
				'category'  => 'general',
				'status' => 'active',
			),
			'advanced-masonry-item' => array(
				'slug'            => 'advanced-masonry-item',
				'title'           => 'Advanced Masonry Item',
				'package'         => 'pro',
				'category'        => 'general',
				'parent'          => 'advanced-masonry',
				'status'          => 'active',
			),
			'client-logo' => array(
				'slug'            => 'client-logo',
				'title'           => 'Client Logo',
				'package'         => 'pro',
				'category'  	  => 'general',
				'status'          => 'active'
			),
			'advanced-toggle' => array(
				'slug'            => 'advanced-toggle',
				'title'           => 'Advanced Toggle',
				'package'         => 'pro',
				'category'        => 'general',
				'status'          => 'active',
			),
			'advanced-toggle-item' => array(
				'slug'            => 'advanced-toggle-item',
				'title'           => 'Advanced Toggle Item',
				'package'         => 'pro',
				'category'        => 'general',
				'parent'          => 'advanced-toggle',
				'status'          => 'active',
			),
			'query-builder' => array(
				'slug'            => 'query-builder',
				'title'           => 'Query Builder',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'status'          => 'active',
			),
			'query-template' => array(
				'slug'            => 'query-template',
				'title'           => 'Query Template',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-builder',
				'status'          => 'active',
			),
			'query-pagination' => array(
				'slug'            => 'query-pagination',
				'title'           => 'Query Pagination',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-builder',
				'status'          => 'active'
			),
			'featured-image' => array(
				'slug'            => 'featured-image',
				'title'           => 'Featured Image',
				'parent'          => 'query-template',
				'package'         => 'pro',
				'category'  => 'general',
				'status' => 'active',
			),
			'post-title' => array(
				'slug'            => 'post-title',
				'title'           => 'Post Title',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-template',
				'status'          => 'active',
			),
			'post-info' => array(
				'slug'            => 'post-info',
				'title'           => 'Post Info',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-template',
				'status'          => 'active',
			),
			'post-excerpt' => array(
				'slug'            => 'post-excerpt',
				'title'           => 'Post Excerpt',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-template',
				'status'          => 'active',
			),
			'post-content' => array(
				'slug'            => 'post-content',
				'title'           => 'Post Content',
				'package'         => 'pro',
				'category'        => 'wp-posts',
				'parent'          => 'query-template',
				'status'          => 'active',
			),
			'unfold' => array(
				'slug'            => 'unfold',
				'title'           => 'Unfold',
				'package'         => 'pro',
				'category'  	  => 'general',
				'status'          => 'active'
			),
			'lottie' => array(
				'slug'     => 'lottie',
				'title'    => 'Lottie',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'inactive',
			),
			'chart' => array(
				'slug'     => 'chart',
				'title'    => 'Chart',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'inactive',
			),
			'advanced-search' => array(
				'slug'     => 'advanced-search',
				'title'    => 'Advanced Search',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'inactive',
				'badge'    => ['new'],
			),
			'facebook-feed' => array(
				'slug'     => 'facebook-feed',
				'title'    => 'Facebook Feed',
				'package'  => 'pro',
				'category' => 'feed',
				'status'   => 'inactive',
				'badge'    => ['new'],
			),
			'hotspot' => array(
				'slug'     => 'hotspot',
				'title'    => 'Hotspot',
				'package'  => 'pro',
				'category' => 'general',
				'status'   => 'inactive',
				'badge'    => ['new'],
			),
		];

		return apply_filters('gutenkit/pro/blocks/list', $blocks);
	}
}
