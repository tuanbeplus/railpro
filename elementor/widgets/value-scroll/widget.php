<?php
namespace RailproElementorWidgets\Widgets\ValueScroll;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class Widget_ValueScroll extends Widget_Base {

    public function get_name() { return 'rp-value-scroll'; }
    public function get_title() { return __( 'RP - Value Scroll', 'railpro' ); }
    public function get_icon() { return 'eicon-text-area'; }
    public function get_categories() { return [ 'railpro' ]; }

    protected function register_controls() {
        $this->start_controls_section( 
            'section_content', 
            [ 
                'label' => __( 'Content', 'railpro' ) 
            ] 
        );

        $this->add_control( 
            'prefix_text', 
            [ 
                'label' => __( 'Prefix Text', 'railpro' ), 
                'type' => Controls_Manager::TEXT, 
                'default' => __( 'We value', 'railpro' ) 
            ] 
        );

        $repeater = new Repeater();
        $repeater->add_control( 
            'value_text', 
            [ 
                'label' => __( 'Value', 'railpro' ), 
                'type' => Controls_Manager::TEXT, 
                'default' => __( 'dependability.', 'railpro' ), 
                'label_block' => true 
            ] 
        );

        $this->add_control( 
            'values', 
            [ 
                'label' => __( 'Values', 'railpro' ), 
                'type' => Controls_Manager::REPEATER, 
                'fields' => $repeater->get_controls(), 
                'default' => [ 
                    [ 'value_text' => 'dependability.' ], 
                    [ 'value_text' => 'integrity.' ], 
                    [ 'value_text' => 'accountability.' ], 
                    [ 'value_text' => 'craftsmanship.' ], 
                    [ 'value_text' => 'service.' ], 
                    [ 'value_text' => 'communication.' ], 
                    [ 'value_text' => 'relationships.' ] 
                ], 
                'title_field' => '{{{ value_text }}}' 
            ] 
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_layout',
            [
                'label' => __( 'Layout & Spacing', 'railpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_alignment',
            [
                'label' => __( 'Alignment', 'railpro' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Left', 'railpro' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'railpro' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'railpro' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll li' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'prefix_spacing',
            [
                'label' => __( 'Gap Between Prefix & Value', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                    'em' => [ 'min' => 0, 'max' => 5, 'step' => 0.1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll li' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __( 'Row Spacing', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                    'em' => [ 'min' => 0, 'max' => 5, 'step' => 0.1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_prefix',
            [
                'label' => __( 'Prefix Styling', 'railpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'prefix_color',
            [
                'label' => __( 'Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-value-prefix' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'prefix_typography',
                'label' => __( 'Typography', 'railpro' ),
                'selector' => '{{WRAPPER}} .rp-value-prefix',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_values',
            [
                'label' => __( 'Values Styling', 'railpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color_active',
            [
                'label' => __( 'Active Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll' => '--color-active: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'color_inactive',
            [
                'label' => __( 'Inactive Color', 'railpro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll' => '--color-inactive: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'opacity_inactive',
            [
                'label' => __( 'Inactive Opacity', 'railpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0.1, 'max' => 1, 'step' => 0.05 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .rp-value-scroll' => '--opacity-inactive: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => __( 'Typography', 'railpro' ),
                'selector' => '{{WRAPPER}} .rp-value-text',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $values    = ! empty( $settings['values'] ) ? $settings['values'] : [];
        $prefix    = ! empty( $settings['prefix_text'] ) ? $settings['prefix_text'] : 'We value';
        $widget_id = $this->get_id();
        ?>
        <section class="rp-value-scroll" id="rp-vs-<?php echo esc_attr( $widget_id ); ?>" data-animate="true">
            <ul>
                <?php foreach ( $values as $item ) : ?>
                    <li>
                        <span class="rp-value-prefix"><?php echo esc_html( $prefix ); ?></span>
                        <span class="rp-value-text"><?php echo esc_html( $item['value_text'] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php
    }
}