<?php

/**
 * Theme functions and definitions
 *
 * @package Railpro-Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('RP_THEME_VER', '1.0.0' . time());

/**
 * Enqueue styles and scripts
 */
if (!function_exists('enqueue_rp_styles_and_scripts')) {
    add_action('wp_enqueue_scripts', 'enqueue_rp_styles_and_scripts');
    function enqueue_rp_styles_and_scripts()
    {
        wp_enqueue_style('rp-main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), RP_THEME_VER);

        wp_enqueue_script('isotope', get_stylesheet_directory_uri() . '/assets/js/lib/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
        wp_enqueue_script('images-loaded', get_stylesheet_directory_uri() . '/assets/js/lib/imagesloaded.pkgd.min.js', array('jquery'), '5.0.0', true);
        wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/assets/js/lib/fancybox.css', array(), '4.0.0');
        wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/assets/js/lib/fancybox.umd.js', array(), '4.0.0', true);

        wp_enqueue_script('rp-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery', 'isotope', 'images-loaded', 'fancybox'), RP_THEME_VER, true);
        wp_localize_script('rp-main-script', 'ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rp_gallery_nonce')
        ));
    }
}

/* Widgets Load */
require_once get_stylesheet_directory() . '/elementor/widgets-load.php';



//PORTFOLIO DYNAMIC TAXONOMY MAPPING OR RELATED ACF
add_action('elementor/query/portfolio_filter', function ($query) {
    $current_obj = get_queried_object();

    if (! $current_obj) {
        return;
    }

    $query->set('post_type', 'portfolio');

    if (isset($current_obj->taxonomy)) {
        $tax_query = [
            [
                'taxonomy' => 'portfolio-category',
                'field'    => 'slug',
                'terms'    => [$current_obj->slug],
            ]
        ];
        $query->set('tax_query', $tax_query);
    } elseif (isset($current_obj->post_type) && $current_obj->post_type === 'product') {
        $meta_query = [
            [
                'key'     => 'products',
                'value'   => '"' . $current_obj->ID . '"',
                'compare' => 'LIKE'
            ]
        ];
        $query->set('meta_query', $meta_query);
    }
});

//DYNAMIC PORTFOLIO / PRODUCT GALLERY LOAD MORE
add_action('wp_ajax_rp_load_more_gallery', 'rp_load_more_gallery_handler');
add_action('wp_ajax_nopriv_rp_load_more_gallery', 'rp_load_more_gallery_handler');

function rp_load_more_gallery_handler()
{
    check_ajax_referer('rp_gallery_nonce', 'nonce');
    $product_id = intval($_POST['product_id']);
    $offset = intval($_POST['offset']);
    $per_page = intval($_POST['per_page']);
    $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'product';

    $all_images = [];

    if ($source === 'portfolio') {
        if (get_post_type($product_id) === 'portfolio') {
            $portfolio_gallery = get_field('gallery', $product_id);
            $video_url = get_field('video', $product_id, false);
            if (empty($video_url)) {
                $video_url = get_field('video', $product_id);
            }

            if (!empty($video_url) && is_string($video_url)) {
                if (strpos($video_url, '<iframe') !== false) {
                    preg_match('/src="([^"]+)"/', $video_url, $match);
                    if (!empty($match[1])) {
                        $video_url = $match[1];
                    }
                }
                $thumbnail = rp_get_video_thumbnail($video_url);
                if (!$thumbnail) {
                    $thumbnail = get_the_post_thumbnail_url($product_id, 'medium_large');
                }
                if (!$thumbnail && !empty($portfolio_gallery)) {
                    $thumbnail = !empty($portfolio_gallery[0]['sizes']['medium_large']) ? $portfolio_gallery[0]['sizes']['medium_large'] : $portfolio_gallery[0]['url'];
                }
                $all_images[] = [
                    'url'      => $thumbnail,
                    'caption'  => get_the_title($product_id),
                    'full'     => esc_url($video_url),
                    'is_video' => true
                ];
            }

            if (!empty($portfolio_gallery)) {
                foreach ($portfolio_gallery as $img) {
                    if (!empty($img['url'])) {
                        $all_images[] = [
                            'url'     => $img['url'],
                            'caption' => !empty($img['caption']) ? $img['caption'] : get_the_title($product_id),
                            'full'    => !empty($img['sizes']['large']) ? $img['sizes']['large'] : $img['url']
                        ];
                    }
                }
            }
        } else {
            $args = [
                'post_type' => 'portfolio',
                'posts_per_page' => -1,
                'meta_query' => [['key' => 'products', 'value' => '"' . $product_id . '"', 'compare' => 'LIKE']]
            ];

            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $portfolio_id = get_the_ID();
                    $portfolio_gallery = get_field('gallery', $portfolio_id);
                    $video_url = get_field('video', $portfolio_id, false);
                    if (empty($video_url)) {
                        $video_url = get_field('video', $portfolio_id);
                    }

                    if (!empty($video_url) && is_string($video_url)) {
                        if (strpos($video_url, '<iframe') !== false) {
                            preg_match('/src="([^"]+)"/', $video_url, $match);
                            if (!empty($match[1])) {
                                $video_url = $match[1];
                            }
                        }
                        $thumbnail = rp_get_video_thumbnail($video_url);
                        if (!$thumbnail) {
                            $thumbnail = get_the_post_thumbnail_url($portfolio_id, 'medium_large');
                        }
                        if (!$thumbnail && !empty($portfolio_gallery)) {
                            $thumbnail = !empty($portfolio_gallery[0]['sizes']['medium_large']) ? $portfolio_gallery[0]['sizes']['medium_large'] : $portfolio_gallery[0]['url'];
                        }
                        $all_images[] = [
                            'url'      => $thumbnail,
                            'caption'  => get_the_title($portfolio_id),
                            'full'     => esc_url($video_url),
                            'is_video' => true
                        ];
                    }

                    if (!empty($portfolio_gallery)) {
                        foreach ($portfolio_gallery as $img) {
                            if (!empty($img['url'])) {
                                $all_images[] = [
                                    'url'     => $img['url'],
                                    'caption' => !empty($img['caption']) ? $img['caption'] : get_the_title($portfolio_id),
                                    'full'    => !empty($img['sizes']['large']) ? $img['sizes']['large'] : $img['url']
                                ];
                            }
                        }
                    }
                }
                wp_reset_postdata();
            }
        }
    } else {
        $gallery = get_field('product_gallery', $product_id);
        if ($gallery) {
            foreach ($gallery as $img) {
                if (!empty($img['url'])) {
                    $all_images[] = [
                        'url' => $img['url'],
                        'caption' => !empty($img['caption']) ? $img['caption'] : (!empty($img['title']) ? $img['title'] : get_the_title($product_id)),
                        'full' => !empty($img['sizes']['large']) ? $img['sizes']['large'] : $img['url']
                    ];
                }
            }
        }
    }

    $slice = array_slice($all_images, $offset, $per_page);
    if (empty($slice)) wp_send_json_error('No more images');

    ob_start();
    foreach ($slice as $index => $img) :
        $global_index = $offset + $index + 1;
        $is_video = !empty($img['is_video']);
?>
        <div class="rp-gallery-item<?php echo $is_video ? ' rp-video-item' : ''; ?>" data-index="<?php echo $global_index; ?>">
            <div class="rp-item-inner">
                <a href="<?php echo esc_url($img['full']); ?>" data-fancybox="gallery" data-elementor-open-lightbox="no" data-caption="<?php echo esc_attr($img['caption']); ?>">
                    <img src="<?php echo esc_url($img['url']); ?>" alt="">
                    <?php if ($is_video) : ?>
                        <span class="rp-play-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </span>
                    <?php endif; ?>
                    <span class="rp-gallery-number"><?php echo $global_index; ?></span>
                </a>
            </div>
        </div>
    <?php endforeach;
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html, 'remaining' => count($all_images) - ($offset + count($slice))]);
}

//EXTRACT VIDEO THUMBNAIL FROM VIDEO URL
function rp_get_video_thumbnail($video_url)
{
    if (empty($video_url) || !is_string($video_url)) {
        return '';
    }


    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9\-_]+)/', $video_url, $matches)) {
        return 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
    }

    if (preg_match('/\.mp4(\?.*)?$/i', $video_url)) {
        $attachment_id = attachment_url_to_postid($video_url);
        if ($attachment_id) {

            $poster_id = get_post_meta($attachment_id, '_thumbnail_id', true);
            if ($poster_id) {
                $thumb = wp_get_attachment_image_url((int) $poster_id, 'medium_large');
                if ($thumb) return $thumb;
            }

            $meta = wp_get_attachment_metadata($attachment_id);
            if (!empty($meta['image']['url'])) {
                return $meta['image']['url'];
            }
        }
    }

    return '';
}

//DYNAMIC ACF REPEATER METADATA OVERRIDE FOR ELEMENTOR HIDING
add_filter('get_post_metadata', 'rp_filter_post_metadata_for_repeater', 10, 4);
function rp_filter_post_metadata_for_repeater($value, $object_id, $meta_key, $single)
{
    if (in_array($meta_key, ['product_features', 'customize_product'])) {
        remove_filter('get_post_metadata', 'rp_filter_post_metadata_for_repeater', 10);
        $raw_value = get_post_meta($object_id, $meta_key, true);
        add_filter('get_post_metadata', 'rp_filter_post_metadata_for_repeater', 10, 4);

        if (empty($raw_value)) {
            return $value;
        }

        $count = intval($raw_value);
        if ($count > 0) {
            $has_valid_data = false;
            for ($i = 0; $i < $count; $i++) {
                remove_filter('get_post_metadata', 'rp_filter_post_metadata_for_repeater', 10);
                $title = get_post_meta($object_id, $meta_key . '_' . $i . '_title', true);
                $desc = get_post_meta($object_id, $meta_key . '_' . $i . '_description', true);
                $image = get_post_meta($object_id, $meta_key . '_' . $i . '_image', true);
                $button = get_post_meta($object_id, $meta_key . '_' . $i . '_button', true);
                add_filter('get_post_metadata', 'rp_filter_post_metadata_for_repeater', 10, 4);

                if (!empty($title) || !empty($desc) || !empty($image) || !empty($button)) {
                    $has_valid_data = true;
                    break;
                }
            }

            if (!$has_valid_data) {
                return $single ? '' : [];
            }
        }
    }
    return $value;
}

// Shortcode: [portfolio_products_list]
add_shortcode('portfolio_products_list', function ($atts) {
    $post_id = get_the_ID();
    // Get products from ACF field
    $products = get_field('products', $post_id);
    // No products -> render nothing
    if (empty($products) || !is_array($products)) {
        return '';
    }

    $links = [];
    foreach ($products as $product) {
        $prod_id = is_object($product) ? $product->ID : $product;
        $title = get_the_title($prod_id);
        $permalink = get_permalink($prod_id);
        $links[] = '
            <a class="portfolio-product-link" href="' . esc_url($permalink) . '">
                ' . esc_html($title) . '
            </a>
        ';
    }
    ob_start();
    ?>
    <div class="portfolio-products-wrapper">
        <h3 class="portfolio-products-heading">
            Products
        </h3>
        <div class="portfolio-products-list">
            <?php echo implode(', ', $links); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});

//Showcase category link 
add_shortcode('showcase_category_link', function () {
    $terms = [];
    $post_id = get_the_ID();


    if ($post_id && get_post_type($post_id) === 'product') {
        $prod_terms = get_the_terms($post_id, 'product-category');
        if (!$prod_terms || is_wp_error($prod_terms)) {
            $prod_terms = get_the_terms($post_id, 'product_cat');
        }
        if ($prod_terms && !is_wp_error($prod_terms)) {
            $terms = $prod_terms;
        }
    }


    if (empty($terms)) {
        $queried_obj = get_queried_object();
        if ($queried_obj instanceof WP_Term) {
            $terms[] = $queried_obj;
        }
    }

    $cat_slug = '';
    foreach ($terms as $term) {
        while ($term && !is_wp_error($term)) {
            $haystack = strtolower($term->slug . ' ' . $term->name);
            if (strpos($haystack, 'residential') !== false) {
                $cat_slug = 'residential';
                break 2;
            }
            if (strpos($haystack, 'multifamily') !== false) {
                $cat_slug = 'multifamily';
                break 2;
            }
            $term = !empty($term->parent) ? get_term($term->parent, $term->taxonomy) : null;
        }
    }

    $showcase_url = home_url('/showcase/');
    if (!empty($cat_slug)) {
        $showcase_url = add_query_arg('portfolio_cat', $cat_slug, $showcase_url);
    }

    return esc_url($showcase_url);
});

// Hide Scroll to Gallery Button if no gallery photos exist
add_filter('elementor/frontend/widget/should_render', 'rp_maybe_hide_gallery_button', 10, 2);
function rp_maybe_hide_gallery_button($should_render, $widget)
{
    // Only run on frontend to avoid breaking the Elementor Editor interface
    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        return $should_render;
    }

    // Check if the widget is an Elementor button widget
    if ('button' === $widget->get_name()) {
        $settings = $widget->get_settings_for_display();

        $link = isset($settings['link']['url']) ? trim($settings['link']['url']) : '';
        $css_classes = isset($settings['css_class']) ? $settings['css_class'] : '';

        // Target button: link contains #gallery, or has 'scroll-to-gallery' class
        if (strpos($link, '#gallery') !== false || strpos($css_classes, 'scroll-to-gallery') !== false) {
            $post_id = get_queried_object_id();
            if (!$post_id) {
                $post_id = get_the_ID();
            }

            $post_type = get_post_type($post_id);
            $has_gallery = false;

            if ('product' === $post_type) {
                // For product: check product_gallery ACF field only
                $product_gallery = get_field('product_gallery', $post_id);
                if (!empty($product_gallery)) {
                    $has_gallery = true;
                }
            } elseif ('portfolio' === $post_type) {
                // For portfolio: check gallery or video ACF fields
                $portfolio_gallery = get_field('gallery', $post_id);
                $video_url = get_field('video', $post_id);
                if (!empty($portfolio_gallery) || !empty($video_url)) {
                    $has_gallery = true;
                }
            } else {
                // Fallback for other post types
                $product_gallery = get_field('product_gallery', $post_id);
                $portfolio_gallery = get_field('gallery', $post_id);
                $video_url = get_field('video', $post_id);
                if (!empty($product_gallery) || !empty($portfolio_gallery) || !empty($video_url)) {
                    $has_gallery = true;
                }
            }

            // If no gallery photos/videos found, do not render the button
            if (!$has_gallery) {
                return false;
            }
        }
    }
    return $should_render;
}

// // Detect if Animation Addons for Elementor is active
// add_filter( 'body_class', 'rp_detect_animation_addons_plugin' );
// function rp_detect_animation_addons_plugin( $classes ) {
//     $active_plugins = (array) get_option( 'active_plugins', [] );
//     $is_active = false;
//     foreach ( $active_plugins as $plugin ) {
//         if ( strpos( $plugin, 'animation-addons-for-elementor' ) !== false ) {
//             $is_active = true;
//             break;
//         }
//     }
//     if ( is_multisite() && !$is_active ) {
//         $network_active = (array) get_site_option( 'active_sitewide_plugins', [] );
//         foreach ( array_keys( $network_active ) as $plugin ) {
//             if ( strpos( $plugin, 'animation-addons-for-elementor' ) !== false ) {
//                 $is_active = true;
//                 break;
//             }
//         }
//     }

//     if ( $is_active ) {
//         $classes[] = 'animation-addons-active';
//     } else {
//         $classes[] = 'animation-addons-inactive';
//     }
//     return $classes;
// }

// Shortcode hiển thị tiêu đề động cho phần Feature sản phẩm
add_shortcode('rp_product_features_title', 'rp_get_product_features_title');
function rp_get_product_features_title()
{
    $post_id = get_the_ID();
    $custom_title = '';

    if (is_tax() || is_category() || is_tag()) {
        $queried_object = get_queried_object();
        if ($queried_object) {
            $term_id = $queried_object->term_id;
            $taxonomy = $queried_object->taxonomy;
            $custom_title = get_field('product_features_heading', $taxonomy . '_' . $term_id);
        }
    } else {
        $custom_title = get_field('product_features_heading', $post_id);
    }

    if (!empty($custom_title)) {
        return esc_html($custom_title);
    }

    return esc_html('Why Choose This Product');
}

/**
 * Back to Top button — Tech Notes & Building Codes pages only
 */
if (!function_exists('rp_render_back_to_top')) {
    add_action('wp_footer', 'rp_render_back_to_top');
    function rp_render_back_to_top()
    {
        if (!is_page(array('tech-notes', 'building-codes'))) {
            return;
        }
        $icon = get_stylesheet_directory_uri() . '/assets/imgs/return-to-top.png';
    ?>
        <button type="button" id="rp-back-to-top" class="rp-back-to-top" aria-label="Back to top">
            <img src="<?php echo esc_url($icon); ?>" alt="" width="44" height="44">
        </button>
<?php
    }
}
