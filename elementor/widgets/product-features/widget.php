<?php 
namespace RailproElementorWidgets\Widgets\ProductFeatures;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_ProductFeatures extends Widget_Base {

    public function get_name() {
        return 'rp-product-features';
    }

    public function get_title() {
        return __( 'RP - Product Features', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'railpro' ];
    }

    private function get_acf_repeater_options() {
        $options = [
            'product_features' => 'Product Features (Default)',
        ];

        if ( function_exists( 'acf_get_field_groups' ) ) {
            $field_groups = acf_get_field_groups();
            foreach ( $field_groups as $group ) {
                $fields = acf_get_fields( $group['ID'] );
                if ( $fields ) {
                    foreach ( $fields as $field ) {
                        if ( $field['type'] === 'repeater' ) {
                            $options[ $field['name'] ] = $field['label'] . ' (' . $field['name'] . ')';
                        }
                    }
                }
            }
        }

        return $options;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'railpro' ),
            ]
        );

        $this->add_control(
            'acf_slug',
            [
                'label'   => __( 'Select Source (ACF)', 'railpro' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $this->get_acf_repeater_options(),
                'default' => 'product_features',
                'description' => __( 'Choose the ACF Repeater field you want to display.', 'railpro' ),
            ]
        );

        $this->add_control(
            'max_items',
            [
                'label' => __( 'Max Items', 'railpro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'active_first',
            [
                'label' => __( 'Start with First Item Open?', 'railpro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'railpro' ),
                'label_off' => __( 'No', 'railpro' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => __( 'Layout', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_height',
            [
                'label' => __( 'Height', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [ 'min' => 200, 'max' => 1000 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .features-grid' => 'height: {{SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}} .feature-card' => 'height: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} .feature-card' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'expansion_rate',
            [
                'label' => __( 'Expansion Rate', 'railpro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 0.1,
                'selectors' => [
                    '{{WRAPPER}} .feature-card:hover' => 'flex: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_gap',
            [
                'label' => __( 'Item Gap', 'railpro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'selectors' => [
                    '{{WRAPPER}} .features-grid' => 'gap: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'hover_zoom',
            [
                'label' => __( 'Hover Zoom Image', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 1, 'max' => 1.5, 'step' => 0.05 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .feature-card:hover .feature-bg-img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => __( 'Border Radius', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .feature-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_overlay',
            [
                'label' => __( 'Overlay & Hover', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'overlay_tabs' );

        $this->start_controls_tab(
            'overlay_normal_tab',
            [
                'label' => __( 'Normal', 'railpro' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'overlay_background',
                'label' => __( 'Overlay Background', 'railpro' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .feature-content',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'overlay_hover_tab',
            [
                'label' => __( 'Hover', 'railpro' ),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'overlay_hover_background',
                'label' => __( 'Hover Background', 'railpro' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .feature-card:hover .feature-hover-overlay',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => 'rgba(0,0,0,0.5)',
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_typo',
            [
                'label' => __( 'Typography', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .feature-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .feature-title',
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label' => __( 'Description Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .feature-desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'selector' => '{{WRAPPER}} .feature-desc',
            ]
        );

        $this->start_controls_tabs( 'content_padding_tabs' );

        $this->start_controls_tab(
            'content_padding_normal_tab',
            [
                'label' => __( 'Normal', 'railpro' ),
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Content Padding', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .feature-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'content_padding_hover_tab',
            [
                'label' => __( 'Hover', 'railpro' ),
            ]
        );

        $this->add_responsive_control(
            'content_padding_hover',
            [
                'label' => __( 'Content Padding (Hover)', 'railpro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .feature-card:hover .feature-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $acf_slug = !empty($settings['acf_slug']) ? $settings['acf_slug'] : 'product_features';
        $max_items = $settings['max_items'];
        
        $post_id = get_the_ID();

        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $document = \Elementor\Plugin::$instance->documents->get_current();
            if ( $document ) {
                $preview_id = $document->get_settings( 'preview_id' );
                $post_id = $preview_id ? $preview_id : $document->get_main_id();
            }
        }

        $features = get_field($acf_slug, $post_id);

        if ( $features ) {
         
            $features = array_filter($features, function($item) {
                $image     = isset($item['image']) ? $item['image'] : null;
                $title     = isset($item['title']) ? $item['title'] : '';
                $desc      = isset($item['description']) ? $item['description'] : '';
                $image_url = '';

                if ( is_array($image) ) {
                    $image_url = $image['url'];
                } elseif ( is_numeric($image) ) {
                    $image_url = wp_get_attachment_url($image);
                } else {
                    $image_url = $image;
                }

                return !(empty($image_url) && empty($title) && empty($desc));
            });
        }

        if ( !empty($features) ) : 
            $features = array_slice($features, 0, $max_items);
            ?>
            <div class="rp-product-features-container">
                <div class="features-grid">
                    <?php 
                    $count = 0;
                    foreach ( $features as $item ) : 
                        $image     = isset($item['image']) ? $item['image'] : null;
                        $title     = isset($item['title']) ? $item['title'] : '';
                        $desc      = isset($item['description']) ? $item['description'] : '';
                        $image_url = '';

                        if ( is_array($image) ) {
                            $image_url = $image['url'];
                        } elseif ( is_numeric($image) ) {
                            $image_url = wp_get_attachment_url($image);
                        } else {
                            $image_url = $image;
                        }

                        if ( empty($image_url) && empty($title) && empty($desc) ) {
                            continue;
                        }

                        $count++;
                        $is_active_class = ($count === 1 && $settings['active_first'] === 'yes') ? 'is-active' : '';
                        ?>
                        <div class="feature-card <?php echo $is_active_class; ?>">
                            <div class="feature-bg-img" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo esc_html($title); ?></h3>
                                <div class="feature-desc"><?php echo esc_html($desc); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
                const cards = document.querySelectorAll('.rp-product-features-container .feature-card');
                cards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        cards.forEach(item => {
                            item.classList.remove('is-active');
                        });
                        card.classList.add('is-active');
                    });
                });
            </script>
            <?php 
        endif;
    }
}
