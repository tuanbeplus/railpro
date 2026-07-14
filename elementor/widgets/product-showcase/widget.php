<?php 
namespace RailproElementorWidgets\Widgets\ProductShowcase;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Widget_ProductShowcase extends Widget_Base {

    public function get_name() {
        return 'rp-product-showcase';
    }

    public function get_title() {
        return __( 'RP - Product Showcase', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'railpro' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_query_settings',
            [
                'label' => 'Query Settings',
            ]
        );

        $this->add_control(
            'query_source',
            [
                'label'   => 'Source',
                'type'    => Controls_Manager::SELECT,
                'default' => 'custom_order',
                'options' => [
                    'custom_order' => 'Custom Order (Manual Selection)',
                    'auto_query'   => 'Auto Query (Archive Default)',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => 'Posts Count',
                'type'    => Controls_Manager::NUMBER,
                'description' => 'Leave as empty to show all products',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => 'Order By',
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => 'Date',
                    'title'      => 'Title',
                    'menu_order' => 'Menu Order',
                    'rand'       => 'Random',
                ],
                'condition' => [
                    'query_source' => 'auto_query',
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => 'Order',
                'type'    => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC'  => 'ASC',
                    'DESC' => 'DESC',
                ],
                'condition' => [
                    'query_source' => 'auto_query',
                ],
            ]
        );

        $this->add_control(
            'template_id',
            [
                'label'   => 'Loop Item Template ID',
                'type'    => Controls_Manager::TEXT,
                'default' => '1756',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $categories = get_terms([
            'taxonomy'   => 'product-category',
            'hide_empty' => false,
        ]);

        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            usort( $categories, function( $a, $b ) {
                if ( stripos($a->slug, 'residential') !== false ) return -1;
                if ( stripos($b->slug, 'residential') !== false ) return 1;
                return 0;
            });

            foreach ( $categories as $cat ) {
                $prods = get_posts([
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'product-category',
                            'field'    => 'term_id',
                            'terms'    => $cat->term_id,
                        ]
                    ],
                ]);

                if ( empty($prods) ) {
                    continue;
                }

                $this->start_controls_section(
                    'section_sort_' . $cat->term_id,
                    [
                        'label' => 'Featured Sort: ' . strtoupper($cat->name),
                        'condition' => [
                            'query_source' => 'custom_order',
                        ],
                    ]
                );

                $p_options = [];
                $default_items = [];
                foreach ( $prods as $p ) {
                    $p_options[$p->ID] = $p->post_title;
                    $default_items[] = [
                        'product_id'    => $p->ID,
                        'product_title' => $p->post_title,
                    ];
                }

                $repeater = new Repeater();
                
                $repeater->add_control(
                    'product_title',
                    [
                        'type' => Controls_Manager::HIDDEN,
                    ]
                );

                $repeater->add_control(
                    'product_id',
                    [
                        'label'       => 'Select Product',
                        'type'        => Controls_Manager::SELECT2,
                        'options'     => $p_options,
                        'label_block' => true,
                    ]
                );

                $this->add_control(
                    'items_' . $cat->term_id,
                    [
                        'label'       => 'Product Order',
                        'type'        => Controls_Manager::REPEATER,
                        'fields'      => $repeater->get_controls(),
                        'default'     => $default_items,
                        'title_field' => '{{{ product_title ? product_title : "Product ID: " + product_id }}}',
                    ]
                );

                $this->end_controls_section();
            }
        }

        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => 'Layout & Style',
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label'     => 'Row Gap',
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .rp-product-showcase-list' => 'display: flex; flex-direction: column; gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $template_id = !empty($settings['template_id']) ? sanitize_text_field($settings['template_id']) : '1756';
        $posts_per_page = (isset($settings['posts_per_page']) && !empty($settings['posts_per_page'])) ? $settings['posts_per_page'] : -1;

        echo '<div class="rp-product-showcase-list">';

        $queried_object = get_queried_object();
        $current_cat_id = 0;
        $current_taxonomy = '';

        if ( $queried_object instanceof \WP_Term ) {
            $current_cat_id   = $queried_object->term_id;
            $current_taxonomy = $queried_object->taxonomy;
        }

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
        ];

        $query_source = !empty($settings['query_source']) ? $settings['query_source'] : 'custom_order';
        $manual_product_ids = [];

        if ( $query_source === 'custom_order' && $current_cat_id ) {
            $field_key = 'items_' . $current_cat_id;
            if ( !empty($settings[$field_key]) ) {
                $manual_product_ids = array_column($settings[$field_key], 'product_id');
            }
        }

        if ( $query_source === 'custom_order' && !empty($manual_product_ids) ) {
            $args['post__in'] = $manual_product_ids;
            $args['orderby']  = 'post__in';
        } else {
        
            if ( $current_cat_id ) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => $current_taxonomy,
                        'field'    => 'term_id',
                        'terms'    => [ $current_cat_id ],
                    ]
                ];
            }
     
            $args['orderby'] = !empty($settings['orderby']) ? $settings['orderby'] : 'date';
            $args['order']   = !empty($settings['order']) ? $settings['order'] : 'DESC';
        }

        $query = new \WP_Query($args);

        if ( ! $query->have_posts() && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            unset($args['tax_query']);
            unset($args['post__not_in']);
            $args['posts_per_page'] = 3;
            $query = new \WP_Query($args);
        }

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($template_id, true);
                if ( empty($content) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                    echo '<div style="border:1px dashed #ccc; padding:10px; margin-bottom:10px; text-align:center;">Template error: check ID '.$template_id.'</div>';
                } else {
                    echo $content;
                }
            }
        } else {
            echo '<div class="rp-empty-products" style="text-align:center; padding: 20px;">No products found.</div>';
        }
        wp_reset_postdata();

        echo '</div>';
    }
}
