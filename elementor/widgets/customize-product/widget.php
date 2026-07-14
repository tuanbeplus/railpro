<?php 
namespace RailproElementorWidgets\Widgets\CustomizeProduct;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_CustomizeProduct extends Widget_Base {

    public function get_name() {
        return 'rp-customize-product';
    }

    public function get_title() {
        return __( 'RP - Customize Product', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'railpro' ];
    }

    protected function register_controls() {


        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => __( 'Layout', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __( 'Item Spacing', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 200 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => __( 'Content Gap', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-card' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => __( 'Card Background Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customize-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __( 'Card Padding', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .customize-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __( 'Card Border Radius', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .customize-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

     
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __( 'Image', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __( 'Image Width', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [ 'min' => 50, 'max' => 600 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-img' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __( 'Image Height', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [ 'min' => 50, 'max' => 800 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Image Border Radius', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-img' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

   
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Content', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

    
        $this->add_control(
            'heading_title',
            [
                'label' => __( 'Title', 'railpro' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __( 'Typography', 'railpro' ),
                'selector' => '{{WRAPPER}} .customize-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customize-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __( 'Bottom Spacing', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_desc',
            [
                'label' => __( 'Description', 'railpro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'label' => __( 'Typography', 'railpro' ),
                'selector' => '{{WRAPPER}} .customize-description',
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customize-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'desc_spacing',
            [
                'label' => __( 'Bottom Spacing', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .customize-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'heading_link',
            [
                'label' => __( 'Link / Button', 'railpro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'link_typography',
                'label' => __( 'Typography', 'railpro' ),
                'selector' => '{{WRAPPER}} .customize-link',
            ]
        );

        $this->start_controls_tabs( 'tabs_link_style' );

        $this->start_controls_tab(
            'tab_link_normal',
            [
                'label' => __( 'Normal', 'railpro' ),
            ]
        );

        $this->add_control(
            'link_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customize-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_link_hover',
            [
                'label' => __( 'Hover', 'railpro' ),
            ]
        );

        $this->add_control(
            'link_hover_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .customize-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $post_id = get_the_ID();

        // Get id product (Preview Settings) when design Template in Elementor
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $document = \Elementor\Plugin::$instance->documents->get_current();
            if ( $document ) {
                $preview_id = $document->get_settings( 'preview_id' );
                $post_id = $preview_id ? $preview_id : $document->get_main_id();
            }
        }

        
        $acf_layout_mode = get_field('customize_col', $post_id);

        if ( empty($acf_layout_mode) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $fallback_products = get_posts([
                'post_type'      => 'product',
                'posts_per_page' => 1,
                'meta_key'       => 'customize_col',
                'fields'         => 'ids'
            ]);
            if ( !empty($fallback_products) ) {
                $post_id = $fallback_products[0];
                $acf_layout_mode = get_field('customize_col', $post_id);
            }
        }

        if ( empty($acf_layout_mode) ) {
            $acf_layout_mode = '2'; 
        }

        $features = get_field('customize_product', $post_id);

        if ( $features ) {
            $features = array_filter($features, function($item) {
                $image     = isset($item['image']) ? $item['image'] : null;
                $title     = isset($item['title']) ? $item['title'] : '';
                $desc      = isset($item['description']) ? $item['description'] : '';
                $button    = isset($item['button']) ? $item['button'] : null;
                $image_url = '';

                if ( is_array($image) ) {
                    $image_url = isset($image['sizes']['medium_large']) ? $image['sizes']['medium_large'] : $image['url'];
                } elseif ( is_numeric($image) ) {
                    $image_url = wp_get_attachment_image_url($image, 'medium_large');
                } else {
                    $image_url = $image;
                }

                $has_button = !empty($button) && (!empty($button['url']) || !empty($button['title']));

                return !(empty($image_url) && empty($title) && empty($desc) && !$has_button);
            });
        }

        if ( empty($features) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-info">No customize product features found.</div>';
            }
            return;
        }

        ?>
            <div class="rp-customize-product-container" data-columns="<?php echo esc_attr($acf_layout_mode); ?>" style="--columns: <?php echo esc_attr($acf_layout_mode); ?>;">
                <div class="customize-grid">
                    <?php foreach ( $features as $item ) : 
                        $image     = isset($item['image']) ? $item['image'] : null;
                        $title     = isset($item['title']) ? $item['title'] : '';
                        $desc      = isset($item['description']) ? $item['description'] : '';
                        $button    = isset($item['button']) ? $item['button'] : null;
                        $image_url = '';
                        $image_alt = '';

                        if ( is_array($image) ) {
                            $image_url = isset($image['sizes']['medium_large']) ? $image['sizes']['medium_large'] : $image['url'];
                            $image_alt = isset($image['alt']) ? $image['alt'] : '';
                        } elseif ( is_numeric($image) ) {
                            $image_url = wp_get_attachment_image_url($image, 'medium_large');
                            $image_alt = get_post_meta($image, '_wp_attachment_image_alt', true);
                        } else {
                            $image_url = $image;
                        }

                        if ( empty($image_alt) ) {
                            $image_alt = $title;
                        }

              
                        if ( empty($image_url) && empty($title) && empty($desc) ) {
                            continue;
                        }

                        $has_image = !empty($image_url);
                        $is_fullwidth = !$has_image; 

                        $card_classes = ['customize-card'];
                        if ( $is_fullwidth ) {
                            $card_classes[] = 'no-image';
                        } else {
                            $card_classes[] = 'has-image';
                        }
                        ?>
                        <div class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">
                            <?php if ( !$is_fullwidth ) : ?>
                                <div class="customize-img">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
                                </div>
                            <?php endif; ?>
                            
                            <div class="customize-content">
                                <?php if ( !empty($title) ) : ?>
                                    <h3 class="customize-title">
                                        <?php if ( $button && !empty($button['url']) ) : ?>
                                            <a href="<?php echo esc_url($button['url']); ?>" target="<?php echo esc_attr($button['target'] ? $button['target'] : '_self'); ?>">
                                        <?php endif; ?>
                                        <?php echo esc_html($title); ?>
                                        <?php if ( $button && !empty($button['url']) ) : ?>
                                            </a>
                                        <?php endif; ?>
                                    </h3>
                                <?php endif; ?>
                                
                                <?php if ( !empty($desc) ) : ?>
                                    <div class="customize-description">
                                        <?php echo $desc; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $button && !empty($button['url']) ) : ?>
                                    <a href="<?php echo esc_url($button['url']); ?>" 
                                       target="<?php echo esc_attr($button['target'] ? $button['target'] : '_self'); ?>" 
                                       class="customize-link">
                                        <?php echo esc_html($button['title'] ? $button['title'] : 'LEARN MORE'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php 
    }
}
