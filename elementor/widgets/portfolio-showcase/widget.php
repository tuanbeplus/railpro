<?php 
namespace RailproElementorWidgets\Widgets\PortfolioShowcase;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Widget_PortfolioShowcase extends Widget_Base {

    public function get_name() {
        return 'rp-portfolio-showcase';
    }

    public function get_title() {
        return __( 'RP - Portfolio Showcase', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'railpro' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_layout_settings',
            [
                'label' => 'Layout',
            ]
        );
        
        $this->add_control(
            'template_type',
            [
                'label'   => 'Choose template type',
                'type'    => Controls_Manager::SELECT,
                'default' => 'posts',
                'options' => [
                    'posts' => 'Posts',
                    'taxonomy' => 'Taxonomy',
                ],
                'label_block' => true,
            ]
        );

        $templates = get_posts([
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);
        $template_options = ['' => 'Select Template'];
        if ( !empty($templates) && !is_wp_error($templates) ) {
            foreach ( $templates as $t ) {
                $template_options[$t->ID] = $t->post_title;
            }
        }

        $this->add_control(
            'template_id',
            [
                'label'   => 'Choose a template',
                'type'    => Controls_Manager::SELECT2,
                'default' => '',
                'options' => $template_options,
                'label_block' => true,
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_query_settings',
            [
                'label' => 'Query Settings',
                'condition' => [
                    'template_id!' => '',
                ],
            ]
        );

        $this->add_control(
            'query_source',
            [
                'label'   => 'Source',
                'type'    => Controls_Manager::SELECT,
                'default' => 'auto_query',
                'options' => [
                    'auto_query'   => 'Auto Query',
                    'custom_order' => 'Custom Order',
                ],
            ]
        );

        $categories = get_terms([
            'taxonomy' => 'portfolio-category',
            'hide_empty' => true,
        ]);
        $cat_options = ['' => 'All Categories'];
        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $cat) {
                $cat_options[$cat->term_id] = $cat->name;
            }
        }

        $this->add_control(
            'filter_category',
            [
                'label'   => 'Filter by Category',
                'type'    => Controls_Manager::SELECT,
                'options' => $cat_options,
                'default' => '',
                'condition' => [
                    'query_source' => 'auto_query',
                    'template_type' => 'posts',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => 'Posts Count',
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
                'description' => 'Leave as empty to show all items',
                'condition' => [
                    'query_source' => 'auto_query',
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => 'Order By',
                'type'    => Controls_Manager::SELECT,
                'default' => 'rand',
                'options' => [
                    'date'       => 'Date',
                    'title'      => 'Title',
                    'menu_order' => 'Menu Order',
                    'rand'       => 'Random',
                ],
                'condition' => [
                    'query_source' => 'auto_query',
                    'template_type' => 'posts',
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

        $this->end_controls_section();

        $this->start_controls_section(
            'section_sort_portfolio',
            [
                'label' => 'Portfolio Selection',
                'condition' => [
                    'query_source' => 'custom_order',
                    'template_type' => 'posts',
                    'template_id!' => '',
                ],
            ]
        );

        $portfolios = get_posts([
            'post_type'      => 'portfolio',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);
        
        $p_options = [];
        $default_items = [];
        if ( !empty($portfolios) && !is_wp_error($portfolios) ) {
            foreach ( $portfolios as $p ) {
                $p_options[$p->ID] = $p->post_title;
            }

            $random_portfolios = $portfolios;
            shuffle($random_portfolios);
            $random_portfolios = array_slice($random_portfolios, 0, 6);
            
            foreach ( $random_portfolios as $p ) {
                $default_items[] = [
                    'portfolio_id'    => $p->ID,
                    'portfolio_title' => $p->post_title,
                ];
            }
        }

        $repeater = new Repeater();
        
        $repeater->add_control(
            'portfolio_title',
            [
                'type' => Controls_Manager::HIDDEN,
            ]
        );

        $repeater->add_control(
            'portfolio_id',
            [
                'label'       => 'Select Portfolio',
                'type'        => Controls_Manager::SELECT2,
                'options'     => $p_options,
                'label_block' => true,
            ]
        );

        $p_options_json = json_encode($p_options, JSON_HEX_QUOT | JSON_HEX_APOS);
        $title_field_portfolio = '<# var pt = ' . $p_options_json . '; #>{{{ portfolio_id ? (pt[portfolio_id] ? pt[portfolio_id] : "Portfolio ID: " + portfolio_id) : "' . esc_js(__('Select a portfolio', 'railpro')) . '" }}}';

        $this->add_control(
            'items_portfolio',
            [
                'label'       => 'Portfolio Order',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => $default_items,
                'title_field' => $title_field_portfolio,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_sort_category',
            [
                'label' => 'Taxonomy Selection',
                'condition' => [
                    'query_source' => 'custom_order',
                    'template_type' => 'taxonomy',
                    'template_id!' => '',
                ],
            ]
        );

        $c_options = [];
        $default_cat_items = [];
        if ( !empty($categories) && !is_wp_error($categories) ) {
            $random_cats = $categories;
            shuffle($random_cats);
            $random_cats = array_slice($random_cats, 0, 6);
            foreach ( $categories as $c ) {
                $c_options[$c->term_id] = $c->name;
            }
            foreach ( $random_cats as $c ) {
                $default_cat_items[] = [
                    'cat_id'    => $c->term_id,
                    'cat_title' => $c->name,
                ];
            }
        }

        $repeater_cat = new Repeater();
        $repeater_cat->add_control('cat_title', ['type' => Controls_Manager::HIDDEN]);
        $repeater_cat->add_control('cat_id', [
            'label' => 'Select Category',
            'type' => Controls_Manager::SELECT2,
            'options' => $c_options,
            'label_block' => true,
        ]);

        $c_options_json = json_encode($c_options, JSON_HEX_QUOT | JSON_HEX_APOS);
        $title_field_cat = '<# var ct = ' . $c_options_json . '; #>{{{ cat_id ? (ct[cat_id] ? ct[cat_id] : "Category ID: " + cat_id) : "' . esc_js(__('Select a category', 'railpro')) . '" }}}';

        $this->add_control(
            'items_category',
            [
                'label'       => 'Category Order',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater_cat->get_controls(),
                'default'     => $default_cat_items,
                'title_field' => $title_field_cat,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => 'Layout & Style',
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'template_id!' => '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'columns',
            [
                'label' => 'Columns',
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-portfolio-showcase-list' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label'     => 'Gap',
                'type'      => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-portfolio-showcase-list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $template_id = !empty($settings['template_id']) ? sanitize_text_field($settings['template_id']) : '';
        $posts_per_page = (isset($settings['posts_per_page']) && $settings['posts_per_page'] !== '') ? $settings['posts_per_page'] : -1;
        $template_type = !empty($settings['template_type']) ? $settings['template_type'] : 'posts';
        $query_source = !empty($settings['query_source']) ? $settings['query_source'] : 'auto_query';

        echo '<div class="rp-portfolio-showcase-list">';

        if ( empty($template_id) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="text-align:center; padding:20px; border:1px dashed #ccc;">Please select a Template.</div>';
            }
            echo '</div>';
            return;
        }

        if ( $template_type === 'taxonomy' ) {
            $manual_cat_ids = [];
            
            if ( $query_source === 'custom_order' && !empty($settings['items_category']) ) {
                $manual_cat_ids = array_column($settings['items_category'], 'cat_id');
            }
            
            $args = [
                'taxonomy'   => 'portfolio-category',
                'hide_empty' => false,
            ];
            
            if ( $query_source === 'custom_order' && !empty($manual_cat_ids) ) {
                $args['include'] = $manual_cat_ids;
                $args['orderby'] = 'include';
            } else {
                if ( $posts_per_page > 0 ) {
                    $args['number'] = $posts_per_page;
                }
                $args['orderby'] = 'name';
                $args['order'] = !empty($settings['order']) ? $settings['order'] : 'ASC';
            }
            
            $terms = get_terms($args);

            if ( !is_wp_error($terms) && !empty($terms) ) {
                if ( $query_source === 'auto_query' && !empty($settings['orderby']) && $settings['orderby'] === 'rand' ) {
                    shuffle($terms);
                }
                foreach ( $terms as $term ) {
                    if (class_exists('\ElementorPro\Plugin')) {
                        $GLOBALS['elementor_loop_current_term'] = $term;
                    }
                    
                    $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($template_id, true);
                    
                    if ( empty($content) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                        echo '<div style="border:1px dashed #ccc; padding:10px; margin-bottom:10px; text-align:center;">Template error: check ID '.$template_id.'</div>';
                    } else {
                        echo $content;
                    }
                }
            } else {
                echo '<div class="rp-empty-portfolios" style="text-align:center; padding: 20px;">No categories found.</div>';
            }
            echo '</div>';
            return;
        }

        // --- Post Query (template_type == 'posts') ---
        $args = [
            'post_type'      => 'portfolio',
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
        ];

        $manual_portfolio_ids = [];

        if ( $query_source === 'custom_order' ) {
            if ( !empty($settings['items_portfolio']) ) {
                $manual_portfolio_ids = array_column($settings['items_portfolio'], 'portfolio_id');
            }
            if ( !empty($manual_portfolio_ids) ) {
                $args['post__in'] = $manual_portfolio_ids;
                $args['orderby']  = 'post__in';
            }
        } else {
            $args['orderby'] = !empty($settings['orderby']) ? $settings['orderby'] : 'rand';
            $args['order']   = !empty($settings['order']) ? $settings['order'] : 'DESC';

            if ( !empty($settings['filter_category']) ) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'portfolio-category',
                        'field'    => 'term_id',
                        'terms'    => $settings['filter_category'],
                    ]
                ];
            }
        }

        $query = new \WP_Query($args);

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
            echo '<div class="rp-empty-portfolios" style="text-align:center; padding: 20px;">No portfolios found.</div>';
        }
        wp_reset_postdata();

        echo '</div>';
    }
}
