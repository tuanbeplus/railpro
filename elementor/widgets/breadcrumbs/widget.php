<?php 
namespace RailproElementorWidgets\Widgets\Breadcrumbs;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class Widget_Breadcrumbs extends Widget_Base {

    public function get_name() {
        return 'rp-breadcrumbs';
    }

    public function get_title() {
        return __( 'RP - Breadcrumbs', 'railpro' );
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs';
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
            'source',
            [
                'label'   => __( 'Source', 'railpro' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto'   => __( 'Auto (Dynamic)', 'railpro' ),
                    'manual' => __( 'Manual (Custom)', 'railpro' ),
                ],
            ]
        );

        $this->add_control(
            'depth',
            [
                'label'     => __( 'Breadcrumb Depth', 'railpro' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '2',
                'options'   => [
                    '2' => __( '2 Levels: Taxonomy > Current', 'railpro' ),
                    '3' => __( '3 Levels: Home > Taxonomy > Current', 'railpro' ),
                    '4' => __( '4 Levels: Home > Parent > Taxonomy > Current', 'railpro' ),
                ],
                'condition' => [
                    'source' => 'auto',
                ],
            ]
        );

        $this->add_control(
            'home_text',
            [
                'label'     => __( 'Home Text', 'railpro' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => __( 'Home', 'railpro' ),
                'condition' => [
                    'source' => 'auto',
                    'depth!' => '2',
                ],
            ]
        );

        $this->add_control(
            'home_link_type',
            [
                'label'     => __( 'Home Link', 'railpro' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'home_url',
                'options'   => [
                    'home_url'    => __( 'Site Home (homepage)', 'railpro' ),
                    'custom_page' => __( 'Custom Page (e.g. Showcase)', 'railpro' ),
                ],
                'condition' => [
                    'source' => 'auto',
                    'depth!' => '2',
                ],
            ]
        );

        $this->add_control(
            'home_page',
            [
                'label'       => __( 'Select Page', 'railpro' ),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $this->get_pages_list(),
                'label_block' => true,
                'condition'   => [
                    'source'         => 'auto',
                    'depth!'         => '2',
                    'home_link_type' => 'custom_page',
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'text',
            [
                'label'       => __( 'Text', 'railpro' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'default'     => __( 'Item Name', 'railpro' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label'       => __( 'Link', 'railpro' ),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'https://your-link.com', 'railpro' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'crumbs_list',
            [
                'label'       => __( 'Items List', 'railpro' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'condition'   => [
                    'source' => 'manual',
                ],
                'title_field' => '{{{ text }}}',
            ]
        );

        $this->add_control(
            'separator',
            [
                'label'     => __( 'Separator', 'railpro' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => '|',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label'     => __( 'Alignment', 'railpro' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start' => [
                        'title' => __( 'Left', 'railpro' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'     => [
                        'title' => __( 'Center', 'railpro' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'   => [
                        'title' => __( 'Right', 'railpro' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'railpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} #breadcrumbs, {{WRAPPER}} #breadcrumbs a, {{WRAPPER}} #breadcrumbs .breadcrumb_last',
            ]
        );

        $this->add_control(
            'parent_color',
            [
                'label'     => __( 'Link Color', 'railpro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'parent_hover_color',
            [
                'label'     => __( 'Link Hover Color', 'railpro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'active_color',
            [
                'label'     => __( 'Last Item Color', 'railpro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs .breadcrumb_last' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label'     => __( 'Separator Color', 'railpro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs .separator-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label'     => __( 'Item Spacing', 'railpro' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #breadcrumbs .separator-text' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_pages_list() {
        $pages  = get_pages( [ 'post_status' => 'publish', 'number' => 200 ] );
        $result = [];
        foreach ( $pages as $page ) {
            $result[ $page->ID ] = $page->post_title;
        }
        return $result;
    }

    private function get_auto_breadcrumbs( $settings ) {
        $crumbs = [];
        $depth  = isset( $settings['depth'] ) ? intval( $settings['depth'] ) : 2;

    
        if ( $depth >= 3 ) {
            $home_link_type = ! empty( $settings['home_link_type'] ) ? $settings['home_link_type'] : 'home_url';

            if ( $home_link_type === 'custom_page' && ! empty( $settings['home_page'] ) ) {
                $page_id  = (int) $settings['home_page'];
                $home_url = get_permalink( $page_id ) ?: home_url( '/' );
             
                $home_text = ! empty( $settings['home_text'] ) ? $settings['home_text'] : get_the_title( $page_id );
            } else {
                $home_url  = home_url( '/' );
                $home_text = ! empty( $settings['home_text'] ) ? $settings['home_text'] : __( 'Home', 'railpro' );
            }

            $crumbs[] = [
                'text' => $home_text,
                'url'  => $home_url,
            ];
        }

        $post_type = get_post_type();

        $taxonomy_map = [
            'product'   => 'product-category',
            'portfolio' => 'portfolio-category',
            'post'      => 'category',
        ];

        $taxonomy = isset( $taxonomy_map[ $post_type ] ) ? $taxonomy_map[ $post_type ] : '';

        if ( $taxonomy ) {
            $terms = get_the_terms( get_the_ID(), $taxonomy );

            if ( $terms && ! is_wp_error( $terms ) ) {
                $term = $terms[0];

                if ( $depth >= 4 && $term->parent ) {
                    $parent_term = get_term( $term->parent, $taxonomy );
                    if ( $parent_term && ! is_wp_error( $parent_term ) ) {
                        $crumbs[] = [
                            'text' => $parent_term->name,
                            'url'  => get_term_link( $parent_term ),
                        ];
                    }
                }

           
                $crumbs[] = [
                    'text' => $term->name,
                    'url'  => get_term_link( $term ),
                ];
            }
        }

   
        $crumbs[] = [
            'text' => get_the_title(),
            'url'  => '',
        ];

        return $crumbs;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $crumbs   = [];

        $source = isset( $settings['source'] ) ? $settings['source'] : 'auto';

        if ( $source === 'auto' ) {
            $crumbs = $this->get_auto_breadcrumbs( $settings );
        } else {
            if ( ! empty( $settings['crumbs_list'] ) && is_array( $settings['crumbs_list'] ) ) {
                foreach ( $settings['crumbs_list'] as $item ) {
                    if ( ! empty( $item['text'] ) ) {
                        $crumbs[] = [
                            'text' => $item['text'],
                            'url'  => ! empty( $item['link']['url'] ) ? $item['link']['url'] : '',
                        ];
                    }
                }
            }
        }

        if ( empty( $crumbs ) ) {
            return;
        }

        $separator = ! empty( $settings['separator'] ) ? $settings['separator'] : '|';

        echo '<div id="breadcrumbs">';

        $count = count( $crumbs );
        foreach ( $crumbs as $index => $crumb ) {
            $is_last = ( $index === $count - 1 );
            echo '<span>';
            if ( $is_last || empty( $crumb['url'] ) ) {
                echo '<span class="breadcrumb_last">' . esc_html( $crumb['text'] ) . '</span>';
            } else {
                echo '<a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['text'] ) . '</a>';
            }
            if ( ! $is_last ) {
                echo '<i class="separator-text">' . esc_html( $separator ) . '</i>';
            }
            echo '</span>';
        }

        echo '</div>';
    }
}