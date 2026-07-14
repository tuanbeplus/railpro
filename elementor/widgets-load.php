<?php

namespace RailproElementorWidgets;

/**
 * Class ElementorWidgets
 *
 * Main ElementorWidgets class
 * @since 1.0.0
 */
class ElementorWidgets
{

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var ElementorWidgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return ElementorWidgets An instance of the class.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public $widgets = array();

	public function widgets_list()
	{

		$this->widgets = array(
			'sample-widget',
			'product-features',
			'breadcrumbs',
			'customize-product',
			'product-gallery',
			'portfolio-filter',
			'value-scroll',
			'product-showcase',
			'portfolio-showcase',
			'table-of-content'
		);

		return $this->widgets;
	}

	/**
	 * widget_styles
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_styles() {}

	/**
	 * widget_scripts
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files()
	{

		foreach ($this->widgets_list() as $widget) {
			require_once(get_stylesheet_directory() . '/elementor/widgets/' . $widget . '/widget.php');

			foreach (glob(get_stylesheet_directory() . '/elementor/widgets/' . $widget . '/skins/*.php') as $filepath) {
				include $filepath;
			}
		}
	}

	/**
	 * Register categories
	 *
	 * Register new Elementor category.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_categories($elements_manager)
	{

		$elements_manager->add_category(
			'railpro',
			[
				'title' => esc_html__('RailPro', 'railpro')
			]
		);
	}

	/**
	 * Register Widgets	
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets()
	{
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SampleWidget\Widget_SampleWidget());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductFeatures\Widget_ProductFeatures());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Breadcrumbs\Widget_Breadcrumbs());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CustomizeProduct\Widget_CustomizeProduct());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductGallery\Widget_ProductGallery());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PortfolioFilter\Widget_PortfolioFilter());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ValueScroll\Widget_ValueScroll());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductShowcase\Widget_ProductShowcase());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PortfolioShowcase\Widget_PortfolioShowcase());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\TableOfContentAll\Widget_TableOfContentAll());
	}

	/**
	 *  ElementorWidgets class constructor
	 *
	 * Register action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct()
	{

		// AJAX handler for loading portfolios based on filters and pagination
		add_action('wp_ajax_rp_load_portfolios', [$this, 'rp_load_portfolios']); // For logged-in users
		add_action('wp_ajax_nopriv_rp_load_portfolios', [$this, 'rp_load_portfolios']); // For non-logged-in users

		// Register widget styles
		add_action('elementor/frontend/after_register_styles', [$this, 'widget_styles']);

		// Register widget scripts
		add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);

		// Register categories
		add_action('elementor/elements/categories_registered', [$this, 'register_categories']);

		// Register widgets
		add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
	}

	/**
	 * AJAX handler for loading portfolios based on filters and pagination
	 */
	private function sort_pinned_first(array $ids, array $pinned)
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

	public function rp_load_portfolios()
	{
		$paged       = max(1, (int) ($_POST['paged']       ?? 1));
		$per_page    = max(1, (int) ($_POST['per_page']    ?? 3));
		$template_id = max(1, (int) ($_POST['template_id'] ?? 2371));
		$category    = sanitize_text_field($_POST['category'] ?? '');
		$orderby     = sanitize_text_field($_POST['orderby']  ?? 'date');
		$order       = sanitize_text_field($_POST['order']    ?? 'DESC');
		$products    = $_POST['products'] ?? [];
		$pinned      = $_POST['pinned']   ?? [];

		if (!is_array($products)) {
			$products = [];
		}
		if (!is_array($pinned)) {
			$pinned = [];
		}

		$products           = array_values(array_filter(array_map('intval', $products)));
		$pinned             = array_values(array_filter(array_map('intval', $pinned)));
		$has_product_filter = !empty($products);
		$args = [
			'post_type'      => 'portfolio',
			'post_status'    => 'publish',
			'orderby'        => $orderby,
			'order'          => $order,
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		];

		if ($category && $category !== 'all') {
			$args['tax_query'] = [[
				'taxonomy' => 'portfolio-category',
				'field'    => 'term_id',
				'terms'    => [(int) $category],
			]];
		}

		$query         = new \WP_Query($args);
		$matched_posts = [];

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_id      = get_the_ID();
				$raw_products = get_field('products', $post_id);

				$portfolio_product_ids = [];
				foreach ((array) $raw_products as $prod) {
					if (is_object($prod) && !empty($prod->ID)) {
						$portfolio_product_ids[] = (int) $prod->ID;
					}
				}

				// Portfolio must contain ALL selected products
				if ($has_product_filter && array_diff($products, $portfolio_product_ids)) {
					continue;
				}

				$matched_posts[$post_id] = [
					'post_id'     => $post_id,
					'product_ids' => $portfolio_product_ids,
					'cat_ids'     => wp_get_post_terms($post_id, 'portfolio-category', ['fields' => 'ids']),
				];
			}
			wp_reset_postdata();
		}

		$ordered_ids = $this->sort_pinned_first(array_keys($matched_posts), $pinned);
		$total      = count($ordered_ids);
		$max_pages  = $total > 0 ? (int) ceil($total / $per_page) : 1;
		$page_ids   = array_slice($ordered_ids, ($paged - 1) * $per_page, $per_page);
		$page_posts = array_map(function ($id) use ($matched_posts) {
			return $matched_posts[$id];
		}, $page_ids);

		// Render cards
		ob_start();

		foreach ($page_posts as $post_data) {
			$GLOBALS['post'] = get_post($post_data['post_id']);
			setup_postdata($GLOBALS['post']);
?>
			<div
				class="pf-card pf-animate-in"
				data-products="<?= esc_attr(implode(',', $post_data['product_ids'])) ?>"
				data-categories="<?= esc_attr(implode(',', $post_data['cat_ids'])) ?>">
				<?php
				echo \Elementor\Plugin::$instance
					->frontend
					->get_builder_content_for_display($template_id, true);
				?>
			</div>
<?php
		}

		wp_reset_postdata();

		$html = trim(ob_get_clean());

		if (empty($html)) {
			$html = '<div class="pf-empty">No projects found.</div>';
		}

		wp_send_json_success([
			'html'      => $html,
			'max_pages' => $max_pages,
		]);
	}
}

// Instantiate ElementorWidgets Class
ElementorWidgets::instance();