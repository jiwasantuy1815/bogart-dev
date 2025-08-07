<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$attributes = $attributes;
$block = $block;;
$content = get_the_content();
?>

<div <?php echo wp_kses_post(Gutenkit\Helpers\Utils::get_dynamic_block_wrapper_attributes($block)) ?>>
	<?php echo wp_kses_post($content); ?>
</div>

