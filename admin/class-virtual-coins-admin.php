<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/admin
 * @author     gradiamdigital <info@gradiamdigital.com>
 */
class Virtual_Coins_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/virtual-coins-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/virtual-coins-admin.js', array( 'jquery' ), $this->version, false );

	}



	/**
	 * Register the ACF option page dedicated to virtual coins settings.
	 *
	 * @since    1.0.0
	 */
	public function create_coins_options_page() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => __( 'Virtual Coins', 'triber' ),
					'menu_title' => __( 'Virtual Coins', 'triber' ),
					'menu_slug'  => 'virtual-coins',
					'redirect'   => false,
				)
			);
		}
	}

	/**
	 * Register the ACF group dedicated to virtual coins options page.
	 *
	 * @since    1.0.0
	 */
	public function create_coins_options_acf_group() {

		$fields = array(
			'select_cp' => 'Select Collection Point',
			'register'  => 'Register',
			'chat_cp'   => 'Chat with Collection Point Host',
			'review'    => 'Leave a Review',
			'notify'    => 'Turn On Notification',
			'purchase'  => 'Purchase Products',
		);

		if ( function_exists( 'acf_add_local_field_group' ) ) {

			acf_add_local_field_group(
				array(
					'key'                   => 'group_vc1',
					'title'                 => 'Virtual Coins Options',
					'location'              => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => 'virtual-coins',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
				)
			);
			foreach ( $fields as $name => $label ) {
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name,
						'label'             => $label,
						'name'              => $name,
						'type'              => 'group',
						'parent'            => 'group_vc1',
						'prefix'            => '',
						'instructions'      => 'Event Name - Max. 3 words!, Event Reward = Reward in coins, Coints Expire = Number of days after event when earned coins expire, Coins Conversion = Value of coins in real money',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
						'layout'            => 'table',
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_event_name',
						'label'             => 'Event Name',
						'name'              => 'event_name',
						'type'              => 'text',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => $label,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
						'default'           => $label,
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_event_description',
						'label'             => 'Event Description',
						'name'              => 'event_description',
						'type'              => 'textarea',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_event_reward',
						'label'             => 'Event Reward',
						'name'              => 'event_reward',
						'type'              => 'number',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'min'               => 0,
						'max'               => '',
						'step'              => 1,
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => 1,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_coins_expire',
						'label'             => 'Coins Expire',
						'name'              => 'coins_expire',
						'type'              => 'number',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'min'               => 0,
						'max'               => '',
						'step'              => 1,
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => 30,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_event_repeat',
						'label'             => 'Hov Many Times Repeatable',
						'name'              => 'event_repeat',
						'type'              => 'number',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => false,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
						'min'               => 1,
						'max'               => '',
						'step'              => 1,
					),
				);
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_' . $name . '_link',
						'label'             => 'Link',
						'name'              => 'event_link',
						'type'              => 'text',
						'parent'            => 'field_vc1_' . $name,
						'prefix'            => '',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => false,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
					),
				);
			}
			acf_add_local_field(
				array(
					'key'               => 'field_vc1_coins_conversion',
					'label'             => 'Coins Conversion',
					'name'              => 'coins_conversion',
					'type'              => 'number',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'min'               => 0,
					'max'               => 1000,
					'step'              => 0.5,
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 1,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc1_coins_redeem_limit',
					'label'             => 'Coins Redeem Limit',
					'name'              => 'coins_redeem_limit',
					'type'              => 'number',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'min'               => 0,
					'max'               => 1,
					'step'              => 0.01,
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0.25,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc1_coins_show_stw_banner',
					'label'             => 'Show Spin The Wheel Banner',
					'name'              => 'coins_stw_banner',
					'type'              => 'true_false',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'instructions'      => '',
					'message'           => 0,
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc1_coins_stw_banner_header',
					'label'             => 'STW banner header',
					'name'              => 'stw_banner_header',
					'type'              => 'text',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 'Spin & Win',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc1_coins_stw_banner_subheader',
					'label'             => 'STW banner subheader',
					'name'              => 'stw_banner_subheader',
					'type'              => 'text',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '3 tokens for every sure-win spin',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);

			acf_add_local_field(
				array(
					'key'               => 'field_vc1_dailycheckin',
					'label'             => 'Daily Checkin',
					'name'              => 'daily_checkin',
					'type'              => 'group',
					'parent'            => 'group_vc1',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'layout'            => 'row',
				),
			);
			foreach ( range( 1, 7 ) as $i ) {
				acf_add_local_field(
					array(
						'key'               => 'field_vc1_dailycheckin_' . $i,
						'label'             => 'Checkin Day ' . $i . ' reward',
						'name'              => 'checkin_day_' . $i,
						'type'              => 'number',
						'parent'            => 'field_vc1_dailycheckin',
						'prefix'            => '',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => false,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'readonly'          => 0,
						'disabled'          => 0,
						'min'               => 1,
						'max'               => '',
						'step'              => 1,
					),
				);
			}
		}

	}
	/**
	 * Register the ACF group dedicated to coin post type.
	 *
	 * @since    1.0.0
	 */
	public function create_coin_post_type_acf_group() {

		if ( function_exists( 'acf_add_local_field_group' ) ) {

			acf_add_local_field_group(
				array(
					'key'                   => 'group_vc2',
					'title'                 => 'Virtual Coin Meta',
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'coin',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
				)
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_type',
					'label'             => 'Coin Event Type',
					'name'              => 'coin_event_type',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_status',
					'label'             => 'Coin Event Status',
					'name'              => 'coin_event_status',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_value',
					'label'             => 'Coin Event Value',
					'name'              => 'coin_event_value',
					'type'              => 'number',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_date',
					'label'             => 'Coin Event Date',
					'name'              => 'coin_event_date',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_spend',
					'label'             => 'Coins Spent',
					'name'              => 'coin_event_spend',
					'type'              => 'number',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_spend_date',
					'label'             => 'Coins Spend Date',
					'name'              => 'coin_event_spend_date',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_expired',
					'label'             => 'Coins Expired',
					'name'              => 'coin_event_expired',
					'type'              => 'number',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_expiration_date',
					'label'             => 'Coins Expiration Date',
					'name'              => 'coin_event_expiration_date',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_balance',
					'label'             => 'Coins Balance',
					'name'              => 'coin_event_balance',
					'type'              => 'number',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc2_coin_event_sm',
					'label'             => 'Coins Special Mark',
					'name'              => 'coin_event_sm',
					'type'              => 'text',
					'parent'            => 'group_vc2',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
				),
			);
		}

	}

	/**
	 * Register user meta coins_wallet.
	 *
	 * @since    1.0.0
	 */
	public function register_coins_wallet() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {

			acf_add_local_field_group(
				array(
					'key'                   => 'group_vc3',
					'title'                 => 'Virtual Coins Status',
					'location'              => array(
						array(
							array(
								'param'    => 'user_form',
								'operator' => '==',
								'value'    => 'all',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
				)
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc3_coins_wallet_balance',
					'label'             => 'Coins Wallet Balance',
					'name'              => 'coins_wallet_balance',
					'type'              => 'number',
					'parent'            => 'group_vc3',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc3_coins_earned',
					'label'             => 'Coins Earned',
					'name'              => 'coins_earned',
					'type'              => 'number',
					'parent'            => 'group_vc3',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc3_coins_spent',
					'label'             => 'Coins Spent',
					'name'              => 'coins_spent',
					'type'              => 'number',
					'parent'            => 'group_vc3',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 0,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
			acf_add_local_field(
				array(
					'key'               => 'field_vc3_coins_expired',
					'label'             => 'Coins Expired',
					'name'              => 'coins_expired',
					'type'              => 'number',
					'parent'            => 'group_vc3',
					'prefix'            => '',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'readonly'          => 1,
					'disabled'          => 0,
					'min'               => 0,
					'max'               => '',
					'step'              => 1,
				),
			);
		}
	}

	/**
	 * Filter coin post columns.
	 *
	 * @param array $columns An array of sortable columns.
	 * @since    1.0.0
	 */
	public function filter_coin_columns( $columns ) {
		$columns = array(
			// @codingStandardsIgnoreStart
			'cb'                  => $columns['cb'],
			'date'                => __( 'Date of Event', $this->plugin_name ),
			'author'              => __( 'User', $this->plugin_name ),
			'event'               => __( 'Event Type', $this->plugin_name ),
			'status'              => __( 'Status', $this->plugin_name ),
			'value'               => __( 'Event Value', $this->plugin_name ),
			'spends'              => __( 'Spends', $this->plugin_name ),
			'spend_date'          => __( 'Spends Date', $this->plugin_name ),
			'expired'             => __( 'Expired', $this->plugin_name ),
			'expiration_date'          => __( 'Expiration Date', $this->plugin_name ),
			'balance'             => __( 'Event Balance', $this->plugin_name ),
			
			
			
			// @codingStandardsIgnoreEnd
		);
		return $columns;
	}

	/**
	 * Add custom columns to coin posts admin table.
	 *
	 * @param string $column The name of the column to display.
	 * @param int    $post_id The current post ID.
	 * @since    1.0.0
	 */
	public function coins_admin_columns( $column, $post_id ) {

		$args = array(
			'date'            => 'coin_event_date',
			'event'           => 'coin_event_type',
			'status'          => 'coin_event_status',
			'value'           => 'coin_event_value',
			'spends'          => 'coin_event_spend',
			'spend_date'      => 'coin_event_spend_date',
			'expired'         => 'coin_event_expired',
			'expiration_date' => 'coin_event_expiration_date',
			'balance'         => 'coin_event_balance',
		);

		foreach ( $args as $key => $value ) {
			if ( $key === $column ) {
				$content = get_post_meta( $post_id, $value, true );
				echo esc_html( $content );
			}
		}

	}

	/**
	 * Manage coin post sortable columns.
	 *
	 * @param array $columns An array of sortable columns.
	 * @since    1.0.0
	 */
	public function coins_admin_sortable_columns( $columns ) {
		$columns['event']           = 'event';
		$columns['status']          = 'status';
		$columns['date']            = 'date';
		$columns['author']          = 'author';
		$columns['spend_date']      = 'spend_date';
		$columns['expiration_date'] = 'expiration_date';
		return $columns;
	}

	/**
	 * Manage coin post queried columns.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 * @since    1.0.0
	 */
	public function admin_coin_posts_orderby( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$args = array(
			'date'            => 'coin_event_date',
			'event'           => 'coin_event_type',
			'status'          => 'coin_event_status',
			'value'           => 'coin_event_value',
			'spends'          => 'coin_event_spend',
			'spend_date'      => 'coin_event_spend_date',
			'expired'         => 'coin_event_expired',
			'expiration_date' => 'coin_event_expiration_date',
			'balance'         => 'coin_event_balance',
		);

		foreach ( $args as $key => $value ) {
			if ( $key === $query->get( 'orderby' ) ) {
				$query->set( 'orderby', 'meta_value' );
				$query->set( 'meta_key', $value );
				$query->set( 'meta_type', 'text' );
			}
		}

	}

}
