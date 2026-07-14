<?php 
namespace RailproElementorWidgets\Widgets\ProductGallery;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_ProductGallery extends Widget_Base {

    public function get_name() {
        return 'rp-product-gallery';
    }

    public function get_title() {
        return __( 'RP - Product Photo Gallery', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-gallery-masonry';
    }

    public function get_categories() {
        return [ 'railpro' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'railpro' ),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __( 'Images per page', 'railpro' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 12,
            ]
        );

        $this->add_control(
            'gallery_source',
            [
                'label' => __( 'Gallery Source', 'railpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'product' => __( 'Product Gallery', 'railpro' ),
                    'portfolio' => __( 'Portfolio Gallery', 'railpro' ),
                ],
                'default' => 'product',
            ]
        );

        $this->add_control(
            'show_load_more',
            [
                'label' => __( 'Show Load More', 'railpro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'railpro' ),
                'label_off' => __( 'Hide', 'railpro' ),
                'return_value' => 'yes',
                'default' => 'yes',
                 'condition' => [
                 'gallery_source' => 'product', 
        ],
            ]
        );

        $this->add_control(
            'load_more_text',
            [
                'label' => __( 'Load More Text', 'railpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'LOAD MORE', 'railpro' ),
                'condition' => [
                    'show_load_more' => 'yes',
                    'gallery_source' => 'product',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_grid',
            [
                'label' => __( 'Grid Layout', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'railpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
                'options' => [
                    '1' => __( '1 Column', 'railpro' ),
                    '2' => __( '2 Columns', 'railpro' ),
                    '3' => __( '3 Columns', 'railpro' ),
                    '4' => __( '4 Columns', 'railpro' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-grid .rp-gallery-item, {{WRAPPER}} .rp-gallery-grid .rp-gallery-sizer' => 'width: calc(100% / {{VALUE}}) !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => __( 'Gap', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-item' => 'padding: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .rp-gallery-grid' => 'margin: calc({{SIZE}}{{UNIT}} / -2); width: calc(100% + {{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __( 'Border Radius', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_wrapper',
            [
                'label' => __( 'Section', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wrapper_bg_color',
            [
                'label' => __( 'Background', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-product-gallery-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label' => __( 'Padding', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .rp-product-gallery-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_container',
            [
                'label' => __( 'Container', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_max_width',
            [
                'label' => __( 'Width', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range' => [
                    'px' => [ 'min' => 800, 'max' => 2000 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __( 'Padding', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => __( 'Button', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .rp-load-more-btn',
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __( 'Normal', 'railpro' ),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-load-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_line_color',
            [
                'label' => __( 'Line Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-load-more-btn::after' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __( 'Hover', 'railpro' ),
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-load-more-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_line_hover_color',
            [
                'label' => __( 'Line Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-load-more-btn:hover::after' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_number',
            [
                'label' => __( 'Number Labels', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'number_bg_color',
            [
                'label' => __( 'Background Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-number' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'number_text_color',
            [
                'label' => __( 'Text Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-gallery-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'selector' => '{{WRAPPER}} .rp-gallery-number',
            ]
        );

        $this->end_controls_section();
    }
protected function render() {
    $settings = $this->get_settings_for_display();
    $post_id = get_the_ID();
    $initial_count = !empty($settings['posts_per_page']) ? $settings['posts_per_page'] : 12;

    if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        $document = \Elementor\Plugin::$instance->documents->get_current();
        if ( $document ) {
            $preview_id = $document->get_settings( 'preview_id' );
            $post_id = $preview_id ? $preview_id : $document->get_main_id();
        }
    }

    $gallery_source = !empty($settings['gallery_source']) ? $settings['gallery_source'] : 'product';
    $all_images = [];

    if ( $gallery_source === 'portfolio' ) {
        if ( get_post_type($post_id) === 'portfolio' ) {
            $portfolio_gallery = get_field('gallery', $post_id);
            $video_url = get_field('video', $post_id, false);
            if ( empty($video_url) ) {
                $video_url = get_field('video', $post_id);
            }

            if ( !empty($video_url) && is_string($video_url) ) {
                if ( strpos($video_url, '<iframe') !== false ) {
                    preg_match('/src="([^"]+)"/', $video_url, $match);
                    if ( !empty($match[1]) ) {
                        $video_url = $match[1];
                    } else {
                        $video_url = ''; 
                    }
                }
                if ( !empty($video_url) ) {
                    $thumbnail = rp_get_video_thumbnail($video_url);
                    if ( !$thumbnail ) {
                        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium_large') ?: '';
                    }
                    if ( !$thumbnail && !empty($portfolio_gallery) ) {
                        $thumbnail = !empty($portfolio_gallery[0]['sizes']['medium_large']) ? $portfolio_gallery[0]['sizes']['medium_large'] : $portfolio_gallery[0]['url'];
                    }
                    $all_images[] = [
                        'id'       => 'video-' . $post_id,
                        'url'      => $thumbnail ?: '',
                        'caption'  => get_the_title($post_id),
                        'full'     => esc_url($video_url),
                        'is_video' => true
                    ];
                }
            }

            if ( !empty($portfolio_gallery) ) {
                foreach ( $portfolio_gallery as $img ) {
                    if ( !empty($img['url']) ) {
                        $all_images[] = [
                            'id'      => isset($img['id']) ? $img['id'] : '',
                            'url'     => !empty($img['sizes']['medium_large']) ? $img['sizes']['medium_large'] : $img['url'],
                            'caption' => !empty($img['caption']) ? $img['caption'] : get_the_title($post_id),
                            'full'    => $img['url']
                        ];
                    }
                }
            }
        } else {
            $args = [
                'post_type' => 'portfolio',
                'posts_per_page' => -1,
                'meta_query' => [['key' => 'products', 'value' => '"' . $post_id . '"', 'compare' => 'LIKE']]
            ];
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    
                    $portfolio_id = get_the_ID();
                    $portfolio_gallery = get_field('gallery', $portfolio_id);
                    $video_url = get_field('video', $portfolio_id, false);
                    if ( empty($video_url) ) {
                        $video_url = get_field('video', $portfolio_id);
                    }

                    if ( !empty($video_url) && is_string($video_url) ) {
                        if ( strpos($video_url, '<iframe') !== false ) {
                            preg_match('/src="([^"]+)"/', $video_url, $match);
                            if ( !empty($match[1]) ) {
                                $video_url = $match[1];
                            } else {
                                $video_url = '';
                            }
                        }
                        if ( !empty($video_url) ) {
                            $thumbnail = rp_get_video_thumbnail($video_url);
                            if ( !$thumbnail ) {
                                $thumbnail = get_the_post_thumbnail_url($portfolio_id, 'medium_large') ?: '';
                            }
                            if ( !$thumbnail && !empty($portfolio_gallery) ) {
                                $thumbnail = !empty($portfolio_gallery[0]['sizes']['medium_large']) ? $portfolio_gallery[0]['sizes']['medium_large'] : $portfolio_gallery[0]['url'];
                            }
                            $all_images[] = [
                                'id'       => 'video-' . $portfolio_id,
                                'url'      => $thumbnail ?: '',
                                'caption'  => get_the_title($portfolio_id),
                                'full'     => esc_url($video_url),
                                'is_video' => true
                            ];
                        }
                    }

                    if ( !empty($portfolio_gallery) ) {
                        foreach ( $portfolio_gallery as $img ) {
                            if ( !empty($img['url']) ) {
                                $all_images[] = [
                                    'id'      => isset($img['id']) ? $img['id'] : '',
                                    'url'     => !empty($img['sizes']['medium_large']) ? $img['sizes']['medium_large'] : $img['url'],
                                    'caption' => !empty($img['caption']) ? $img['caption'] : get_the_title($portfolio_id),
                                    'full'    => $img['url']
                                ];
                            }
                        }
                    }
                }
                wp_reset_postdata();
            }
        }

        if ( empty($all_images) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $fallback_portfolios = get_posts([
                'post_type'      => 'portfolio',
                'posts_per_page' => 5,
            ]);
            foreach ($fallback_portfolios as $p) {
                $portfolio_id = $p->ID;
                $portfolio_gallery = get_field('gallery', $portfolio_id);
                $video_url = get_field('video', $portfolio_id, false);
                if ( empty($video_url) ) {
                    $video_url = get_field('video', $portfolio_id);
                }

                if ( !empty($video_url) && is_string($video_url) ) {
                    if ( strpos($video_url, '<iframe') !== false ) {
                        preg_match('/src="([^"]+)"/', $video_url, $match);
                        if ( !empty($match[1]) ) {
                            $video_url = $match[1];
                        } else {
                            $video_url = '';
                        }
                    }
                    if ( !empty($video_url) ) {
                        $thumbnail = rp_get_video_thumbnail($video_url);
                        if ( !$thumbnail ) {
                            $thumbnail = get_the_post_thumbnail_url($portfolio_id, 'medium_large') ?: '';
                        }
                        if ( !$thumbnail && !empty($portfolio_gallery) ) {
                            $thumbnail = !empty($portfolio_gallery[0]['sizes']['medium_large']) ? $portfolio_gallery[0]['sizes']['medium_large'] : $portfolio_gallery[0]['url'];
                        }
                        $all_images[] = [
                            'id'       => 'video-' . $portfolio_id,
                            'url'      => $thumbnail ?: '',
                            'caption'  => get_the_title($portfolio_id),
                            'full'     => esc_url($video_url),
                            'is_video' => true
                        ];
                    }
                }

                if ( !empty($portfolio_gallery) ) {
                    foreach ( $portfolio_gallery as $img ) {
                        if ( !empty($img['url']) ) {
                            $all_images[] = [
                                'id'      => isset($img['id']) ? $img['id'] : '',
                                'url'     => !empty($img['sizes']['medium_large']) ? $img['sizes']['medium_large'] : $img['url'],
                                'caption' => !empty($img['caption']) ? $img['caption'] : get_the_title($portfolio_id),
                                'full'    => $img['url']
                            ];
                        }
                    }
                }
                if (!empty($all_images)) break;
            }
        }
    } else {
        $gallery = get_field('product_gallery', $post_id);

        if ( empty($gallery) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $fallback_products = get_posts([
                'post_type'      => 'product',
                'posts_per_page' => 1,
                'meta_key'       => 'product_gallery',
                'fields'         => 'ids'
            ]);
            if ( !empty($fallback_products) ) {
                $post_id = $fallback_products[0];
                $gallery = get_field('product_gallery', $post_id);
            }
        }

        if ( !empty($gallery) ) {
            foreach ( $gallery as $img ) {
                if ( !empty($img['url']) ) {
                    $all_images[] = [
                        'id'      => isset($img['id']) ? $img['id'] : '',
                        'url'     => !empty($img['sizes']['medium_large']) ? $img['sizes']['medium_large'] : $img['url'],
                        'caption' => !empty($img['caption']) ? $img['caption'] : (!empty($img['title']) ? $img['title'] : get_the_title($post_id)),
                        'full'    => $img['url']
                    ];
                }
            }
        }
    }

    if ( empty($all_images) ) {
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            echo '<div class="elementor-alert elementor-alert-info">No photos found.</div>';
        }
        return;
    }

    $total_images  = count($all_images);


    $initial_count = ( !empty($settings['posts_per_page']) && (int)$settings['posts_per_page'] > 0 )
        ? (int)$settings['posts_per_page']
        : $total_images;

    $display_images = array_slice($all_images, 0, $initial_count);
    $hidden_images  = array_slice($all_images, $initial_count);
    $widget_id      = $this->get_id();
    ?>

    <div class="rp-product-gallery-wrapper"
         data-total="<?php echo esc_attr($total_images); ?>"
         data-per-page="<?php echo esc_attr($initial_count); ?>"
         data-product-id="<?php echo esc_attr($post_id); ?>"
         data-source="<?php echo esc_attr($gallery_source); ?>">

        <div class="rp-gallery-container">
            <div class="rp-gallery-grid" id="rp-grid-<?php echo esc_attr($widget_id); ?>">
                <div class="rp-gallery-sizer"></div>
                <?php foreach ( $display_images as $index => $img ) :
                    $is_video = !empty($img['is_video']);
                ?>
                    <div class="rp-gallery-item<?php echo $is_video ? ' rp-video-item' : ''; ?>" data-index="<?php echo $index + 1; ?>">
                        <div class="rp-item-inner">
                                <a href="<?php echo esc_url($img['full']); ?>"
                                   data-fancybox="gallery"
                                   data-elementor-open-lightbox="no"
                                   data-index="<?php echo $index + 1; ?>"
                                   data-caption="<?php echo !empty($img['caption']) ? esc_attr($img['caption']) : ''; ?>">
                                <img src="<?php echo esc_url($img['url'] ?: ''); ?>" alt="<?php echo !empty($img['caption']) ? esc_attr($img['caption']) : ''; ?>">
                                <?php if ( $is_video ) : ?>
                                   <span class="rp-play-icon">
                                         <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/imgs/railpro-gallery-play-01-icon.svg" alt="button play">
                                    </span>
                                <?php endif; ?>
                                <span class="rp-gallery-number"><?php echo $index + 1; ?></span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="rp-gallery-hidden-items" style="position: absolute; width: 0; height: 0; overflow: hidden; opacity: 0; pointer-events: none; visibility: hidden;">
                <?php foreach ( $hidden_images as $index => $img ) :
                    $global_index = $initial_count + $index + 1;
                ?>
                    <a href="<?php echo esc_url($img['full']); ?>"
                       class="rp-hidden-gallery-item"
                       data-index="<?php echo $global_index; ?>"
                       data-fancybox="gallery"
                       data-thumb="<?php echo esc_url($img['url'] ?: ''); ?>"
                       data-elementor-open-lightbox="no"
                       data-caption="<?php echo !empty($img['caption']) ? esc_attr($img['caption']) : ''; ?>">
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ( isset($settings['show_load_more']) && $settings['show_load_more'] === 'yes' && $total_images > $initial_count ) : ?>
                <div class="rp-gallery-footer">
                    <button class="rp-load-more-btn">
                        <?php echo isset($settings['load_more_text']) ? esc_html($settings['load_more_text']) : esc_html__('Load More', 'railpro'); ?>
                    </button>
                    <div class="rp-loader" style="display:none;"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
}