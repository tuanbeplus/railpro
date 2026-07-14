<?php

namespace RailproElementorWidgets\Widgets\PortfolioFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

class Widget_PortfolioFilter extends Widget_Base
{
    private static $titles_script_hooked = false;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        if (!self::$titles_script_hooked) {
            self::$titles_script_hooked = true;
            add_action('elementor/editor/footer', [$this, 'print_portfolio_titles_script']);
        }
    }

    public function print_portfolio_titles_script()
    {
        echo '<script>window.railproPortfolioTitles = ' . wp_json_encode($this->get_portfolio_options()) . ';</script>';
    }

    public function get_name()
    {
        return 'portfolio-filter';
    }

    public function get_title()
    {
        return __('Portfolio Filter', 'railpro');
    }

    public function get_icon()
    {
        return 'eicon-filter';
    }

    public function get_categories()
    {
        return ['railpro'];
    }

    protected function register_controls()
    {
        // Settings
        $this->start_controls_section('section_settings', [
            'label' => __('Settings', 'railpro'),
        ]);

        $this->add_control('posts_per_page', [
            'label' => __('Posts per page', 'railpro'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
        ]);

        $this->add_control('loop_template_id', [
            'label' => __('Loop Item Template ID', 'railpro'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2371,
            'description' => __('Elementor Loop Item template ID', 'railpro'),
        ]);

        $this->add_control('orderby', [
            'label' => __('Order By', 'railpro'),
            'type' => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => __('Date', 'railpro'),
                'title' => __('Title', 'railpro'),
            ],
        ]);

        $this->add_control('order', [
            'label' => __('Order', 'railpro'),
            'type' => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => [
                'DESC' => __('DESC', 'railpro'),
                'ASC' => __('ASC', 'railpro'),
            ],
        ]);

        $title_field_template = '<# var t = (window.railproPortfolioTitles || {})[portfolio_id]; #>{{{ portfolio_id ? (t ? t : "ID: " + portfolio_id) : "' . esc_js(__('Select a portfolio', 'railpro')) . '" }}}';

        $residential_repeater = new Repeater();
        $residential_repeater->add_control('portfolio_id', [
            'label' => __('Portfolio', 'railpro'),
            'type' => Controls_Manager::SELECT2,
            'label_block' => true,
            'options' => $this->get_portfolio_options_by_group('residential'),
        ]);

        $this->add_control('pinned_residential', [
            'label' => __('Featured Residential Projects', 'railpro'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $residential_repeater->get_controls(),
            'title_field' => $title_field_template,
            'prevent_empty' => false,
            'description' => __('Add a row per Residential portfolio, in priority order. Drag rows to reorder — these always show first (in this order) on the Residential tab and its product filters.', 'railpro'),
        ]);

        $multifamily_repeater = new Repeater();
        $multifamily_repeater->add_control('portfolio_id', [
            'label' => __('Portfolio', 'railpro'),
            'type' => Controls_Manager::SELECT2,
            'label_block' => true,
            'options' => $this->get_portfolio_options_by_group('multifamily'),
        ]);

        $this->add_control('pinned_multifamily', [
            'label' => __('Featured Multifamily Projects', 'railpro'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $multifamily_repeater->get_controls(),
            'title_field' => $title_field_template,
            'prevent_empty' => false,
            'separator' => 'before',
            'description' => __('Add a row per Multifamily portfolio, in priority order. Drag rows to reorder — these always show first (in this order) on the Multifamily tab and its product filters.', 'railpro'),
        ]);

        $this->end_controls_section();

        // Style: Tabs
        $this->start_controls_section('style_tabs', [
            'label' => __('Tabs', 'railpro'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('tabs_gap', [
            'label' => __('Gap between tabs', 'railpro'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-tabs' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('tabs_bottom_spacing', [
            'label' => __('Bottom spacing', 'railpro'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('tabs_border_color', [
            'label' => __('Border color', 'railpro'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pf-tabs' => 'border-bottom-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tab_typography',
                'selector' => '{{WRAPPER}} .pf-tab',
            ]
        );

        $this->add_responsive_control('tab_padding', [
            'label' => __('Padding', 'railpro'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
            ],
        ]);

        $this->end_controls_section();

        // Style: Product Buttons
        $this->start_controls_section('style_products', [
            'label' => __('Product Filter Buttons', 'railpro'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('products_gap', [
            'label' => __('Gap between buttons', 'railpro'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-products' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'product_btn_typography',
                'selector' => '{{WRAPPER}} .pf-product-btn',
            ]
        );

        $this->add_responsive_control('product_btn_padding', [
            'label' => __('Padding', 'railpro'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-product-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
            ],
        ]);

        $this->end_controls_section();

        // Style: Grid
        $this->start_controls_section('style_grid', [
            'label' => __('Portfolio Grid', 'railpro'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('grid_columns', [
            'label' => __('Columns', 'railpro'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'selectors' => [
                '{{WRAPPER}} .pf-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
            ],
        ]);

        $this->add_control('grid_gap', [
            'label' => __('Gap', 'railpro'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .pf-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function get_portfolio_options()
    {
        $options = [];

        $posts = get_posts([
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'fields' => 'ids',
        ]);

        foreach ($posts as $post_id) {
            $options[$post_id] = get_the_title($post_id);
        }

        return $options;
    }

    private function get_portfolio_options_by_group($group)
    {
        $options = [];

        $terms = get_terms([
            'taxonomy' => 'portfolio-category',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return $options;
        }

        $term_id = null;
        foreach ($terms as $term) {
            if (strpos(strtolower($term->slug), $group) !== false) {
                $term_id = $term->term_id;
                break;
            }
        }

        if (!$term_id) {
            return $options;
        }

        $posts = get_posts([
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'fields' => 'ids',
            'tax_query' => [[
                'taxonomy' => 'portfolio-category',
                'field' => 'term_id',
                'terms' => [$term_id],
            ]],
        ]);

        foreach ($posts as $post_id) {
            $options[$post_id] = get_the_title($post_id);
        }

        return $options;
    }

    public static function sort_pinned_first(array $ids, array $pinned)
    {
        $ids = array_values($ids);

        if (empty($pinned)) {
            return $ids;
        }

        $ids_lookup = array_flip($ids);

        $pinned_first = [];
        foreach ($pinned as $pid) {
            if (isset($ids_lookup[$pid])) {
                $pinned_first[] = $pid;
            }
        }

        $pinned_lookup = array_flip($pinned_first);
        $rest = array_values(array_filter($ids, function ($id) use ($pinned_lookup) {
            return !isset($pinned_lookup[$id]);
        }));

        return array_merge($pinned_first, $rest);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $per_page = max(1, (int) ($settings['posts_per_page'] ?? 3));
        $template_id = max(1, (int) ($settings['loop_template_id'] ?? 2371));
        $columns = max(1, (int) ($settings['grid_columns'] ?? 3));
        $orderby = $settings['orderby'] ?? 'date';
        $order = $settings['order'] ?? 'DESC';

        $pinned_residential = [];
        foreach ((array) ($settings['pinned_residential'] ?? []) as $row) {
            if (!empty($row['portfolio_id'])) {
                $pinned_residential[] = (int) $row['portfolio_id'];
            }
        }
        $pinned_residential = array_values(array_unique($pinned_residential));

        $pinned_multifamily = [];
        foreach ((array) ($settings['pinned_multifamily'] ?? []) as $row) {
            if (!empty($row['portfolio_id'])) {
                $pinned_multifamily[] = (int) $row['portfolio_id'];
            }
        }
        $pinned_multifamily = array_values(array_unique($pinned_multifamily));

        // Categories
        $raw_cats = get_terms([
            'taxonomy' => 'portfolio-category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (is_wp_error($raw_cats)) {
            $raw_cats = [];
        }

        $residential_cat = null;
        $multifamily_cat = null;
        $other_cats = [];

        foreach ($raw_cats as $cat) {
            $slug = strtolower($cat->slug);
            if (strpos($slug, 'residential') !== false) {
                $residential_cat = $cat;
            } elseif (strpos($slug, 'multifamily') !== false) {
                $multifamily_cat = $cat;
            } else {
                $other_cats[] = $cat;
            }
        }

        $ordered_cats = array_values(
            array_filter(
                array_merge(
                    [$residential_cat, $multifamily_cat],
                    $other_cats
                )
            )
        );

        $default_cat =
            $residential_cat
            ?: $multifamily_cat
            ?: ($ordered_cats[0] ?? null);

        $default_cat_id =
            $default_cat
            ? (string) $default_cat->term_id
            : 'all';

        $pinned_by_cat = [];
        if ($residential_cat) {
            $pinned_by_cat[(string) $residential_cat->term_id] = $pinned_residential;
        }
        if ($multifamily_cat) {
            $pinned_by_cat[(string) $multifamily_cat->term_id] = $pinned_multifamily;
        }

        // All products
        $all_products = [];
        $all_portfolios = get_posts([
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);

        foreach ($all_portfolios as $post_id) {
            $raw_products = get_field(
                'products',
                $post_id
            );

            if (!$raw_products) {
                continue;
            }

            foreach ((array) $raw_products as $prod) {
                if (
                    is_object($prod)
                    && !empty($prod->ID)
                ) {
                    $all_products[$prod->ID] =
                        $prod->post_title;
                }
            }
        }

        // Preload
        $preloaded_categories = [];
        $max_pages_map = [];

        foreach ($ordered_cats as $cat) {
            $cat_id = (string) $cat->term_id;

            $all_query = new \WP_Query([
                'post_type' => 'portfolio',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => $orderby,
                'order' => $order,
                'fields' => 'ids',
                'tax_query' => [[
                    'taxonomy' => 'portfolio-category',
                    'field' => 'term_id',
                    'terms' => [$cat_id],
                ]],
            ]);

            $ordered_ids = self::sort_pinned_first(
                $all_query->posts,
                $pinned_by_cat[$cat_id] ?? []
            );

            $max_pages_map[$cat_id] = $per_page > 0
                ? (int) max(1, ceil(count($ordered_ids) / $per_page))
                : 1;

            $visible_products = [];
            foreach ($ordered_ids as $pid) {
                $raw_products = get_field('products', $pid);
                if ($raw_products) {
                    foreach ((array) $raw_products as $prod) {
                        if (
                            is_object($prod)
                            && !empty($prod->ID)
                        ) {
                            $visible_products[$prod->ID] =
                                $prod->post_title;
                        }
                    }
                }
            }

            $page_ids = array_slice($ordered_ids, 0, $per_page);

            ob_start();
            foreach ($page_ids as $post_id) {
                $raw_products = get_field('products', $post_id);

                $product_ids = [];
                if ($raw_products) {
                    foreach ((array) $raw_products as $prod) {
                        if (
                            is_object($prod)
                            && !empty($prod->ID)
                        ) {
                            $product_ids[] =
                                (string) $prod->ID;
                        }
                    }
                }

                $cat_ids = wp_get_post_terms(
                    $post_id,
                    'portfolio-category',
                    ['fields' => 'ids']
                );

                $GLOBALS['post'] = get_post($post_id);
                setup_postdata($GLOBALS['post']);
?>

                <div
                    class="pf-card pf-animate-in"
                    data-products="<?= esc_attr(implode(',', $product_ids)) ?>"
                    data-categories="<?= esc_attr(implode(',', $cat_ids)) ?>">

                    <?php
                    echo \Elementor\Plugin::$instance
                        ->frontend
                        ->get_builder_content_for_display(
                            $template_id,
                            true
                        );
                    ?>
                </div>
        <?php
            }
            wp_reset_postdata();

            $preloaded_categories[$cat_id] = [
                'html' => ob_get_clean(),
                'products' => $visible_products,
            ];
        }
        ?>
        <div
            class="portfolio-filter-widget"
            data-default-cat="<?= esc_attr($default_cat_id) ?>"
            data-columns="<?= esc_attr($columns) ?>"
            data-template-id="<?= esc_attr($template_id) ?>"
            data-per-page="<?= esc_attr($per_page) ?>"
            data-orderby="<?= esc_attr($orderby) ?>"
            data-order="<?= esc_attr($order) ?>"
            data-pinned-map='<?= esc_attr(wp_json_encode($pinned_by_cat)) ?>'
            data-max-pages='<?= esc_attr(wp_json_encode($max_pages_map)) ?>'>

            <?php if (!empty($ordered_cats)) : ?>
                <div class="pf-tabs" role="tablist">
                    <?php foreach ($ordered_cats as $cat) :
                        $is_active =
                            ((string) $cat->term_id === $default_cat_id);
                    ?>
                        <button
                            class="pf-tab<?= $is_active ? ' active' : '' ?>"
                            data-cat="<?= esc_attr($cat->term_id) ?>"
                            role="tab"
                            aria-selected="<?= $is_active ? 'true' : 'false' ?>">
                            <?php echo esc_html($cat->name) ?> Projects
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div
                class="pf-products"
                data-all-products='<?= esc_attr(wp_json_encode($all_products)) ?>'>

                <?php
                $default_products =
                    $preloaded_categories[$default_cat_id]['products'] ?? [];

                foreach ($default_products as $product_id => $product_title) :
                ?>
                    <button
                        class="pf-product-btn"
                        data-product="<?= esc_attr($product_id) ?>">
                        <?= esc_html($product_title) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="pf-grid">
                <?php
                echo $preloaded_categories[$default_cat_id]['html'] ?? '';
                ?>
            </div>

            <script
                type="application/json"
                class="pf-preloaded-data">
                <?php echo wp_json_encode($preloaded_categories); ?>
            </script>
        </div>
<?php
    }
}