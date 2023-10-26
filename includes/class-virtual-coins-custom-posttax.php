<?php
/**
 * The custom post types and custom taxonomies necessary for the plugin functionality
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/includes
 */
class Virtual_Coins_Custom_Posts_Taxonomies {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Defines array of custom post types to be created.
	 *
	 * @return array
	 */
	private function custom_post_types() {
		$types = array(

			'coin' => array(
				'name'                  => 'Coins',
				'singular_name'         => 'Coin',
				'description'           => 'Coin custom post type',
				'public'                => true,
				'publicly_queryable'    => true,
				'show_ui'               => true,
				'show_in_rest'          => true,
				'rest_base'             => '',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'has_archive'           => true,
				'show_in_menu'          => true,
				'show_in_nav_menus'     => true,
				'delete_with_user'      => false,
				'exclude_from_search'   => false,
				'capability_type'       => 'post',
				'map_meta_cap'          => true,
				'hierarchical'          => true,
				'rewrite'               => array(
					'slug'       => 'coin',
					'with_front' => true,
				),
				'query_var'             => true,
				'menu_icon'             => 'dashicons-carrot',
				'supports'              => array( 'title', 'custom-fields', 'author' ),
				'taxonomies'            => array(),
				'show_in_graphql'       => true,
				'graphql_single_name'   => 'coin',
				'graphql_plural_name'   => 'coins',
			),

		);

		return $types;
	}


	/**
	 * Defines array of custom taxonomies to be created.
	 *
	 * @return array
	 */
	private function custom_taxonomies() {
		$types = array(

			'sample' => array(
				'name'                => 'Coin Categories',
				'singular_name'       => 'Coin Category',
				'hierarchical'        => true,
				'show_ui'             => true,
				'show_admin_column'   => true,
				'query_var'           => true,
				'rewrite'             => array( 'slug' => 'coin-category' ),
				'show_in_quick_edit'  => true,
				'show_admin_column'   => true,
				'show_in_graphql'     => true,
				'graphql_single_name' => 'coin_category',
				'graphql_plural_name' => 'coin_categories',
				'show_in_rest'        => true,
				'object_type'         => array( 'coin' ),
			),

		);

		return $types;
	}

	/**
	 * Defines labels for custom post type
	 *
	 * @param    string $name            CPT name.
	 * @param    string $singular_name   CPT singular name.
	 * @return array
	 */
	private function define_custom_post_labels( $name, $singular_name ) {
		$labels = array(
            // @codingStandardsIgnoreStart
			'name'                     => __( $name, $this->plugin_name ),
			'singular_name'            => __( $singular_name, $this->plugin_name ),
			'menu_name'                => __( $name, $this->plugin_name ),
			'all_items'                => __( 'All ' . $name, $this->plugin_name ),
			'add_new'                  => __( 'Add new', $this->plugin_name ),
			'add_new_item'             => __( 'Add new ' . $singular_name, $this->plugin_name ),
			'edit_item'                => __( 'Edit ' . $name, $this->plugin_name ),
			'new_item'                 => __( 'New ' . $singular_name, $this->plugin_name ),
			'view_item'                => __( 'View ' . $singular_name, $this->plugin_name ),
			'view_items'               => __( 'View ' . $name, $this->plugin_name ),
			'search_items'             => __( 'Search ' . $name, $this->plugin_name ),
			'not_found'                => __( 'No ' . $name . ' found', $this->plugin_name ),
			'not_found_in_trash'       => __( 'No ' . $name . ' found in trash', $this->plugin_name ),
			'parent'                   => __( 'Parent ' . $singular_name . ':', $this->plugin_name ),
			'featured_image'           => __( 'Featured image for this ' . $singular_name, $this->plugin_name ),
			'set_featured_image'       => __( 'Set featured image for this ' . $singular_name, $this->plugin_name ),
			'remove_featured_image'    => __( 'Remove featured image for this ' . $singular_name, $this->plugin_name ),
			'use_featured_image'       => __( 'Use as featured image for this ' . $singular_name, $this->plugin_name ),
			'archives'                 => __( $singular_name . ' archives', $this->plugin_name ),
			'insert_into_item'         => __( 'Insert into ' . $singular_name, $this->plugin_name ),
			'uploaded_to_this_item'    => __( 'Upload to this ' . $singular_name, $this->plugin_name ),
			'filter_items_list'        => __( 'Filter ' . $name . ' list', $this->plugin_name ),
			'items_list_navigation'    => __( $name . ' list navigation', $this->plugin_name ),
			'items_list'               => __( $name . ' list', $this->plugin_name ),
			'attributes'               => __( $name . ' attributes', $this->plugin_name ),
			'name_admin_bar'           => __( $singular_name, $this->plugin_name ),
			'item_published'           => __( $singular_name . ' published', $this->plugin_name ),
			'item_published_privately' => __( $singular_name . ' published privately.', $this->plugin_name ),
			'item_reverted_to_draft'   => __( $singular_name . ' reverted to draft.', $this->plugin_name ),
			'item_scheduled'           => __( $singular_name . ' scheduled', $this->plugin_name ),
			'item_updated'             => __( $singular_name . ' updated.', $this->plugin_name ),
			'parent_item_colon'        => __( 'Parent ' . $singular_name . ':', $this->plugin_name ),
            // @codingStandardsIgnoreEnd
		);
		return $labels;
	}

	/**
	 * Defines arguments for custom post type
	 *
	 * @param    array $args            data for CPT arguments.
	 * @return array
	 */
	private function define_custom_post_type_args( $args ) {

		$labels = $this->define_custom_post_labels( $args['name'], $args['singular_name'] );

		$cpt_arguments = array(
            // phpcs:ignore
			'label'                 => __( $args['name'], $this->plugin_name ), 
			'labels'                => $labels,
			'description'           => $args['description'],
			'public'                => $args['public'],
			'publicly_queryable'    => $args['publicly_queryable'],
			'show_ui'               => $args['show_ui'],
			'show_in_rest'          => $args['show_in_rest'],
			'rest_base'             => $args['rest_base'],
			'rest_controller_class' => $args['rest_controller_class'],
			'has_archive'           => $args['has_archive'],
			'show_in_menu'          => $args['has_archive'],
			'show_in_nav_menus'     => $args['show_in_menu'],
			'delete_with_user'      => $args['show_in_nav_menus'],
			'exclude_from_search'   => $args['exclude_from_search'],
			'capability_type'       => $args['capability_type'],
			'map_meta_cap'          => $args['map_meta_cap'],
			'hierarchical'          => $args['hierarchical'],
			'rewrite'               => $args['rewrite'],
			'query_var'             => $args['query_var'],
			'menu_icon'             => $args['menu_icon'],
			'supports'              => $args['supports'],
			'show_in_graphql'       => $args['show_in_graphql'],
			'graphql_single_name'   => $args['graphql_single_name'],
			'graphql_plural_name'   => $args['graphql_plural_name'],
		);
		return $cpt_arguments;
	}

	/**
	 * Registers custom post types.
	 *
	 * @return void
	 */
	public function register_custom_post_types() {

		$post_types = $this->custom_post_types();

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $key => $value ) {
				if ( 'sample' !== $key ) {
					$args = $this->define_custom_post_type_args( $value );
					register_post_type( $key, $args );
				}
			}
		}
	}

	// -----------------------------------------------------------------------------------

	/**
	 * Define custom taxonomy arguments
	 *
	 * @param    string $name            taxonomy name.
	 * @param    string $singular_name   taxonomy singular name.
	 * @param    array  $args            data for taxonomy arguments.
	 * @return array
	 */
	private function define_custom_taxonomy( $name, $singular_name, $args ) {

		$labels = array(
			// @codingStandardsIgnoreStart
			'name'              => _x( $name, 'taxonomy general name', $this->plugin_name ),
			'singular_name'     => _x( $singular_name, 'taxonomy singular name', $this->plugin_name ),
			'search_items'      => __( 'Search ' . $name, $this->plugin_name ),
			'all_items'         => __( 'All ' . $name, $this->plugin_name ),
			'parent_item'       => __( 'Parent ' . $singular_name, $this->plugin_name ),
			'parent_item_colon' => __( 'Parent ' . $singular_name . ':', $this->plugin_name ),
			'edit_item'         => __( 'Edit ' . $singular_name, $this->plugin_name ),
			'update_item'       => __( 'Update ' . $singular_name, $this->plugin_name ),
			'add_new_item'      => __( 'Add New ' . $singular_name, $this->plugin_name ),
			'new_item_name'     => __( 'New ' . $singular_name . 'name', $this->plugin_name ),
			'menu_name'         => __( $name, $this->plugin_name ),
			// @codingStandardsIgnoreEnd
		);
		$tax_arguments = array(
			'hierarchical'        => $args['hierarchical'],
			'labels'              => $labels,
			'show_ui'             => $args['show_ui'],
			'show_admin_column'   => $args['show_admin_column'],
			'query_var'           => $args['query_var'],
			'rewrite'             => $args['rewrite'],
			'show_in_quick_edit'  => $args['show_in_quick_edit'],
			'show_admin_column'   => $args['show_admin_column'],
			'show_in_graphql'     => $args['show_in_graphql'],
			'graphql_single_name' => $args['graphql_single_name'],
			'graphql_plural_name' => $args['graphql_plural_name'],
			'show_in_rest'        => $args['show_in_rest'],
		);

		return $tax_arguments;
	}

	/**
	 * Registers custom post types.
	 *
	 * @return void
	 */
	public function register_custom_taxonomies() {

		$taxonomies = $this->custom_taxonomies();

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $key => $value ) {
				if ( 'sample' !== $key ) {
					$args = $this->define_custom_taxonomy( $value['name'], $value['singular_name'], $value );
					register_taxonomy( $key, $value['object_type'], $args );
				}
			}
		}
	}

}
