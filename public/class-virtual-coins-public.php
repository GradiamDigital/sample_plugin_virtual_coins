<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/public
 * @author     gradiamdigital <info@gradiamdigital.com>
 */
class Virtual_Coins_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Virtual_Coins_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Virtual_Coins_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/virtual-coins-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Virtual_Coins_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Virtual_Coins_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/virtual-coins-public.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Function defining the coins balance shortcode.
	 *
	 * @return int
	 */
	public function define_coins_balance() {
		$balance = 0;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$balance = (int) get_user_meta( $user_id, 'coins_wallet_balance', true );
		}
		return $balance;
	}

	/**
	 * Function collecting the data for coins history.
	 *
	 * @return array
	 */
	public function get_coins_history( $user_id ) {

		$dates_list = array();

		$labels = array(
			'select_cp'      => 'Select Collection Point',
			'register'       => 'Register',
			'purchase'       => 'Purchase Products',
			'chat_cp'        => 'Chat with Collection Point Host',
			'review'         => 'Leave a Review',
			'notify'         => 'Turn On Notification',
			'checkin_day_1'  => 'Checkin Day 1',
			'checkin_day_2'  => 'Checkin Day 2',
			'checkin_day_3'  => 'Checkin Day 3',
			'checkin_day_4'  => 'Checkin Day 4',
			'checkin_day_5'  => 'Checkin Day 5',
			'checkin_day_6'  => 'Checkin Day 6',
			'checkin_day_7'  => 'Checkin Day 7',
			'spin_the_wheel' => 'Spin The Wheel Reward',
		);

		$coins_args = array(
			'post_type'   => 'coin',
			'author'      => $user_id,
			'post_status' => 'publish',
			'numberposts' => - 1,
		);

		$coins = get_posts( $coins_args );
		foreach ( $coins as $coin ) {

			$event_date   = get_post_meta( $coin->ID, 'coin_event_date', true );
			$event_number = get_post_meta( $coin->ID, 'coin_event_value', true );
			$expir_date   = get_post_meta( $coin->ID, 'coin_event_expiration_date', true );
			$expir_number = get_post_meta( $coin->ID, 'coin_event_expired', true );
			$event_sm     = get_post_meta( $coin->ID, 'coin_event_sm', true );

			$dates_list[ $event_date ] = array(
				'coin_id'      => $coin->ID,
				'order_id'     => '',
				'oper_type'    => 'earn',
				'oper_name'    => $labels[ $coin->post_content ],
				'oper_subname' => 'Tokens reward',
				'oper_value'   => $event_number,
			);

			if ( ! empty( $event_sm ) ) {
				$notes = json_decode( $event_sm );
				foreach ( $notes as $array ) {
					if ( 'stw' === $array->type ) {
						$dates_list[ $array->date ] = array(
							'coin_id'      => $coin->ID,
							'order_id'     => '',
							'oper_type'    => 'spend',
							'oper_name'    => 'Spin The Wheel',
							'oper_subname' => 'Price of the game',
							'oper_value'   => -$array->value,
						);
					}
				}
			}

			if ( ! empty( $expir_date ) && strtotime( $expir_date ) <= strtotime( 'now' ) ) {

				$dates_list[ $expir_date ] = array(
					'coin_id'      => $coin->ID,
					'order_id'     => '',
					'oper_type'    => 'spend',
					'oper_name'    => 'Tokens expiration',
					'oper_subname' => 'Tokens earned on ' . $event_date . ' expired',
					'oper_value'   => -1 * $expir_number,
				);
			}
		}

		$orders_args = array(
			'customer_id' => $user_id,
			'limit'       => -1,
		);
		$orders      = wc_get_orders( $orders_args );

		foreach ( $orders as $order ) {
			if ( ! empty( $order->get_fees() ) ) {
				foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
					if ( 'Redeemed tokens' === $item_fee->get_name() && 0 !== (int) $item_fee->get_total() ) {
						$dates_list[ $order->order_date ] = array(
							'coin_id'      => '',
							'order_id'     => $order->get_id(),
							'oper_type'    => 'spend',
							'oper_name'    => 'Order #' . $order->get_id(),
							'oper_subname' => 'Completed purchase',
							'oper_value'   => $item_fee->get_total(),
						);
					}
				}
			}
		}

		krsort( $dates_list );

		return $dates_list;
	}

	/**
	 * Function defining html for coins checkin section.
	 *
	 * @return html
	 */
	private function define_coins_checkin() {
		global $wpdb;
		$user_id          = get_current_user_id();
		$schedule         = '<div class="coins-checkin-schedule">';
		$current_reward   = 0;
		$current_step     = 0;
		$next_reward      = 0;
		$next_step        = 0;
		$current_previous = 0;

		foreach ( range( 1, 7 ) as $i ) {
			$reward         = get_field( 'daily_checkin', 'option' )[ 'checkin_day_' . $i ];
			$completed      = false;
			$completed_date = '';
			$next_completed = false;
			$next_i         = $i + 1;
			$prev_i         = $i - 1;
			$active         = false;

			$completed_args      = array();
			$completed_next_args = array();
			$completed_prev_args = array();

			$results_completed = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
						{$wpdb->prefix}posts.ID
					FROM
						{$wpdb->prefix}posts
					WHERE
						{$wpdb->prefix}posts.post_status = 'publish' And
						{$wpdb->prefix}posts.post_author = %s And
						{$wpdb->prefix}posts.post_content LIKE %s",
					$user_id,
					'checkin_day_' . $i
				)
			);
			if ( ! empty( $results_completed ) ) {
				foreach ( $results_completed as $key => $value ) {
					$completed_args[] = $value->ID;
				}
				$last_event_time = get_post_meta( max( $completed_args ), 'coin_event_date', true );
				if ( strtotime( $last_event_time ) > strtotime( 'yesterday' ) ) {
					$completed      = true;
					$completed_date = $last_event_time;
				}
			}

			$results_next_completed = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
						{$wpdb->prefix}posts.ID
					FROM
						{$wpdb->prefix}posts
					WHERE
						{$wpdb->prefix}posts.post_status = 'publish' And
						{$wpdb->prefix}posts.post_author = %s And
						{$wpdb->prefix}posts.post_content LIKE %s",
					$user_id,
					'checkin_day_' . $next_i
				)
			);
			if ( ! empty( $results_next_completed ) ) {
				foreach ( $results_next_completed as $key => $value ) {
					$completed_next_args[] = $value->ID;
				}
				$last_next_event_time = get_post_meta( max( $completed_next_args ), 'coin_event_date', true );
				if ( strtotime( $last_next_event_time ) > strtotime( 'yesterday' ) ) {
					$completed      = true;
					$next_completed = true;
				}
			}

			if ( 1 === $i ) {
				if ( false === $completed ) {
					$active = true;
				}
			} else {
				if ( false === $completed ) {
					$results_prev_completed = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT
								{$wpdb->prefix}posts.ID
							FROM
								{$wpdb->prefix}posts
							WHERE
								{$wpdb->prefix}posts.post_status = 'publish' And
								{$wpdb->prefix}posts.post_author = %s And
								{$wpdb->prefix}posts.post_content LIKE %s",
							$user_id,
							'checkin_day_' . $prev_i
						)
					);
					if ( ! empty( $results_prev_completed ) ) {
						foreach ( $results_prev_completed as $key => $value ) {
							$completed_prev_args[] = $value->ID;
						}
						$last_prev_event_time = get_post_meta( max( $completed_prev_args ), 'coin_event_date', true );
						if ( strtotime( $last_prev_event_time ) > strtotime( 'yesterday' ) ) {
							$active = true;
						}
					}
				}
			}

			if ( true === $completed ) {
				$schedule .= '<div class="coins-checkin-schedule__day day-' . $i . ' completed" data-date="' . $completed_date . '"><span class="js_coins-checkin-schedule-day-text">';
				$schedule .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/icon_double_tick_brown.svg"/>';
				$schedule .= '</span><span class="coins-checkin-schedule__day__number">Day ' . $i . '</span></div>';
			} else {
				if ( true === $active ) {
					$schedule        .= '<div class="coins-checkin-schedule__day day-' . $i . ' active"><span class="js_coins-checkin-schedule-day-text">' . $reward . '</span><span class="coins-checkin-schedule__day__number">Day ' . $i . '</span></div>';
					$current_reward   = $reward;
					$current_step     = $i;
					$next_step        = $i + 1;
					$next_reward      = get_field( 'daily_checkin', 'option' )[ 'checkin_day_' . $next_step ];
					$current_previous = $i - 1;
				} else {
					$schedule .= '<div class="coins-checkin-schedule__day day-' . $i . '"><span class="js_coins-checkin-schedule-day-text">' . $reward . '</span><span class="coins-checkin-schedule__day__number">Day ' . $i . '</span></div>';
				}
			}
		}
		$schedule .= '</div>';

		$last_completed_2      = array();
		$last_completed_2_data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					{$wpdb->prefix}posts.ID
				FROM
					{$wpdb->prefix}posts
				WHERE
					{$wpdb->prefix}posts.post_status = 'publish' And
					{$wpdb->prefix}posts.post_author = %s And
					{$wpdb->prefix}posts.post_content LIKE %s",
				$user_id,
				'checkin_day_' . $current_previous
			)
		);
		foreach ( $last_completed_2_data as $key => $value ) {
			$last_completed_2[] = $value->ID;
		}
		$last_completed_2_date_time = get_post_meta( max( $last_completed_2 ), 'coin_event_date', true );

		if ( is_user_logged_in() ) {
			if ( 7 === (int) $current_step ) {
				$text  = 'Mission complete!';
				$class = 'coins-checkin-button inactive';
			} else {
				if ( ! empty( $last_completed_2 ) && false === ( strtotime( $last_completed_2_date_time ) < strtotime( 'today' ) ) ) {
					$text  = 'Come back tomorrow to get <strong>' . $current_reward . ' tokens</strong>';
					$class = 'coins-checkin-button inactive';
				} else {
					$text  = 'Check in today to get <strong>' . $current_reward . ' tokens</strong>';
					$class = 'coins-checkin-button js-coins-checkin-button';
				}
			}
		} else {
			$text  = 'Login to earn tokens';
			$class = 'coins-checkin-button js-coins-to-login';
		}
		$html  = '<div class="container container--checkin"><button class="' . $class . '" data-step="';
		$html .= $current_step;
		$html .= '" data-nextreward="' . $next_reward . '" data-reward="' . $current_reward . '" ';
		$html .= 'data-balance="';
		$html .= (int) get_user_meta( $user_id, 'coins_wallet_balance', true );
		$html .= '" data-last="' . $last_completed_2_date_time . '"><span class="js-coins-checkin-button_text">';
		$html .= $text;
		$html .= '</span><img class="spinner" src="' . plugin_dir_url( __FILE__ ) . 'images/spinner-triber-3.gif" style="display: none;"/></button>';
		$html .= $schedule;
		$html .= '</div>';
		return $html;
	}

	/**
	 * Function defining the stw banner shortcode.
	 *
	 * @return html
	 */
	public function define_stw_banner_shortcode() {
		$html  = '<a href="' . get_home_url() . '/spin-the-wheel" class="container container--stw-banner mb-2">';
		$html .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/spin-the-wheel-banner.svg" class="container--stw-banner__image" />';
		$html .= '<span class="container--stw-banner__header">' . get_field( 'stw_banner_header', 'option' ) . '</span>';
		$html .= '<span class="container--stw-banner__subheader">' . get_field( 'stw_banner_subheader', 'option' ) . '</span>';
		$html .= '</a>';

		return $html;
	}

	/**
	 * Function defining the coins missions shortcode.
	 *
	 * @return html
	 */
	public function define_coins_missions_shortcode() {

		global $wpdb;
		$user_id = get_current_user_id();

		$show_stw_banner = get_field( 'coins_stw_banner', 'option' );

		$events = array(
			'register',
			'select_cp',
			'purchase',
			'chat_cp',
			'review',
			'notify',
		);

		$html = '<section class="section section-coins-missions">';
		if ( true === $show_stw_banner ) {
			$html .= $this->define_stw_banner_shortcode();
		}
		$html .= '<div class="container container--coins-missions"><div class="row">';
		foreach ( $events as $event ) {
			$options   = get_field( $event, 'option' );
			$label     = $options['event_name'];
			$reward    = $options['event_reward'];
			$repeat    = $options['event_repeat'];
			$link      = $options['event_link'];
			$completed = array();

			$completed_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
						{$wpdb->prefix}posts.ID
					FROM
						{$wpdb->prefix}posts
					WHERE
						{$wpdb->prefix}posts.post_status = 'publish' And
						{$wpdb->prefix}posts.post_author = %s And
						{$wpdb->prefix}posts.post_content LIKE %s",
					$user_id,
					$event
				)
			);
			foreach ( $completed_data as $key => $value ) {
				$completed[] = $value->ID;
			}
			$completed_q = count( $completed );

			$html .= '<div class="col-12 col-md-8 event-mission">';
			$html .= '<div class="event-mission__coin"><span>' . $reward . '</span></div>';
			$html .= '<div class="event-mission__content">';
			$html .= '<h4>' . $label . '</h4>';
			$html .= '<p><span>' . $completed_q . '</span>/<span>' . $repeat . '</span>';
			$html .= '</div>';
			if ( ! is_user_logged_in() ) {
				$html .= '<button class="event-mission__button inactive"><span>Go</span></button>';
			} else {
				if ( $completed_q === (int) $repeat ) {
					$html .= '<button class="event-mission__button completed"><img src="' . plugin_dir_url( __FILE__ ) . 'images/icon_double_tick_green.svg"/></button>';
				} else {
					if ( 'notify' === $event ) {
						$html .= '<div class="onesignal-customlink-container"></div>';
					} elseif ( 'register' === $event ) {
						$html .= '<a href="#" class="event-mission__button js-get-register-tokens"><span>Get Tokens</span><img class="spinner" src="' . plugin_dir_url( __FILE__ ) . 'images/triber_spinner.gif" style="display: none;"/></a>';
					} else {
						$html .= '<a href="' . get_home_url() . $link . '" class="event-mission__button"><span>Go</span></a>';
					}
				}
			}
			$html .= '</div>';
		}
		$html .= '</div></div></section>';
		return $html;

	}

	/**
	 * Function adding the coins page top section.
	 *
	 * @return html
	 */
	public function define_coins_section_top_shortcode() {

		/**
		 * Function defining the coins missions shortcode.
		 *
		 * @return html
		 */
		$balance = $this->define_coins_balance();
		$checkin = $this->define_coins_checkin();
		$html    = '<section class="section section-coins-head"><div class="container container--header">';
		$html   .= '<div class="coin-ico"></div>';
		$html   .= '<h3>' . $balance . '</h3>';
		$html   .= '<a href="' . get_home_url() . '/coins/my-triber-tokens" class="arrow-right-white"></a>';
		$html   .= '</div>';
		$html   .= $checkin;
		$html   .= '</section>';
		return $html;

	}

	/**
	 * Function adding the coins page top section.
	 *
	 * @return html
	 */
	public function define_coins_cart_input_shortcode() {

		$user_id       = get_current_user_id();
		$balance       = (int) $this->define_coins_balance();
		$cart_subtotal = (float) WC()->cart->subtotal;
		$max_indicator = 0.25;
		if ( ! empty( get_field( 'coins_redeem_limit', 'option' ) ) ) {
			$max_indicator = (float) get_field( 'coins_redeem_limit', 'option' );
		}
		$max_value = floor( $cart_subtotal * $max_indicator );
		if ( $balance < $max_value ) {
			$max_value = $balance;
		}

		if ( 0 < $max_value ) {
			$html = '<div class="coins-cart-input">';
			$html .= '<div id="cart-coins-redeem" class="coins-cart-input__form">';
			$html .= '<input name="cart_coins" type="hidden" value="' . (int) $max_value . '">';
			$html .= '<div class="coins-cart-input__form__text">';
			$html .= '<p class="coin-ico-container"><span class="coin-ico"></span></p>';
			$html .= '<p class="text-container">';
			$html .= '<span>Tokens &#183; ' . number_format( $max_value, 0, '.', ' ' ) . '</span>';
			// if ( isset( $_COOKIE['cru'] ) && (int) $_COOKIE['cru'] === (int) $user_id && isset( $_COOKIE['crv'] ) && 0 <div (int) $_COOKIE['crv'] ) {
				$html .= '<span class="max-info">Up to ' . $max_indicator * 100 . '% of your order</span>';
			// } else {
			// 	$html .= '<span class="coins-cart-input__form__text__max">Up to ' . $max_indicator * 100 . '% of your order</span>';
			// }
			$html .= '</p>';
			$html .= '</div>';
			$html .= '<p class="coins-cart-input__form__button-container">';
			$html .= '<span class="coins-cart-input__form__before-button">[-' . get_woocommerce_currency_symbol() . number_format( $max_value, 2, '.', ' ' ) . ']</span>';
			if ( isset( $_COOKIE['cru'] ) && (int) $_COOKIE['cru'] === (int) $user_id && isset( $_COOKIE['crv'] ) && 0 < (int) $_COOKIE['crv'] ) {
				$html .= '<button type="submit" class="redeem-tokens-button clicked">';
			} else {
				$html .= '<button type="submit" class="redeem-tokens-button">';
			}
			$html .= '</button>';
			$html .= '</p>';
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$html  = '<div class="coins-cart-input">';
			$html .= '<p class="coins-cart-input__header p-0">';
			$html .= '<span class="coin-ico"></span>';
			$html .= '<span>You do not have any tokens to redeem.</span>';
			$html .= '</p>';
			$html .= '</div>';
		}
		return $html;

	}

	/**
	 * Function defining the html for coins history page.
	 *
	 * @return html
	 */
	public function define_coins_history_shortcode() {

		$user_id = get_current_user_id();

		$operations = $this->get_coins_history( $user_id );

		$items = '';

		foreach ( $operations as $key => $value ) {

			$items .= '<div class="c-report__item all ' . $value['oper_type'] . '">';
			$items .= '<div class="c-report__item__icon"><span>' . $value['oper_value'] . '</span></div>';
			$items .= '<div class="c-report__item__content">';
			$items .= '<p class="c-report__item__content__name">' . $value['oper_name'] . '</p>';
			$items .= '<p class="c-report__item__content__subname">' . $value['oper_subname'] . '</p>';
			$items .= '<p class="c-report__item__content__date">' . date( 'Y-m-d H:i:s', strtotime( $key ) ) . '</p>';
			$items .= '</div>';
			$items .= '<div class="c-report__item__value';
			if ( $value['oper_value'] < 0 ) {
				$items .= ' c-report__item__value--negative';
			}
			$items .= '"><span>' . $value['oper_value'] . '</span></div>';
			$items .= '</div>';

		}

		$html  = '<div class="row">';
		$html .= '<div class="c-report-top">';
		$html .= '<div class="c-report-top__balance"><span>' . $this->define_coins_balance() . '</><span>';
		if ( 1 === $this->define_coins_balance() ) {
			$html .= ' Token available';
		} else {
			$html .= ' Tokens available';
		}
		$html .= '</span></div>';
		$html .= '<a class="c-report-top__button" href="' . get_home_url() . '/coins">Earn More Tokens</a>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="c-report">';
		$html .= '<ul class="c-report__header">';
		$html .= '<li><a href="all" class="js-cr-nav active"><span>All History</span></a></li>';
		$html .= '<li><a href="earn" class="js-cr-nav"><span>Earning</span></a></li>';
		$html .= '<li><a href="spend" class="js-cr-nav"><span>Spending</span></a></li>';
		$html .= '</ul>';
		if ( true === empty( $operations ) ) {
			$html .= '<p class="no-history">There is no history</p>';
		} else {
			$html .= $items;
		}
		$html .= '</div>';

		return $html;

	}


	/**
	 * Function defining the html for coins bar on the front page.
	 *
	 * @return html
	 */
	public function define_coins_front_bar() {

		$html  = '<div class="container">';
		$html .= '<a class="coins-bar" href="' . get_home_url() . '/coins">';
		$html .= '<div class="coins-bar__text"><img class="coins-bar__ico" src="' . plugin_dir_url( __FILE__ ) . 'images/token.svg" /><p><span>Daily Triber Tokens Rewards</span>';
		$html .= '<span>' . $this->define_coins_balance();
		if ( 1 === $this->define_coins_balance() ) {
			$html .= ' Token';
		} else {
			$html .= ' Tokens';
		}
		$html .= '</span></p></div>';
		$html .= '<span class="coins-bar__balance">Play to Earn';
		$html .= '<img class="coins-bar__arrow" src="' . plugin_dir_url( __FILE__ ) . 'images/icon_arrow_right_red.svg" /></span>';
		$html .= '</a>';
		$html .= '</div>';

		return $html;

	}

	/**
	 * Function defining the html for coins balance on my account page.
	 *
	 * @return html
	 */
	public function show_coins_balance_myaccount() {

		$html  = '<a id="gtm-event-account_top_tokens" class="gtm-event-account_top" href="/coins">';
		$html .= '<div><img src="' . get_template_directory_uri() . '/assets/svg/icon_token.svg" alt="token"/><span>';
		$html .= $this->define_coins_balance();
		$html .= '</span></div>';
		if ( 1 === $this->define_coins_balance() ) {
			$html .= ' Token';
		} else {
			$html .= ' Tokens';
		}
		$html .= '</a>';

		return $html;

	}





	/**
	 * Function adding the shortcodes
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		add_shortcode( 'coins_top', array( $this, 'define_coins_section_top_shortcode' ) );
		add_shortcode( 'coins_missions', array( $this, 'define_coins_missions_shortcode' ) );
		add_shortcode( 'cart_coins_input', array( $this, 'define_coins_cart_input_shortcode' ) );
		add_shortcode( 'coins_balance', array( $this, 'define_coins_balance' ) );
		add_shortcode( 'coins_history', array( $this, 'define_coins_history_shortcode' ) );
		add_shortcode( 'coins_bar', array( $this, 'define_coins_front_bar' ) );
		add_shortcode( 'stw_banner', array( $this, 'define_stw_banner_shortcode' ) );
		add_shortcode( 'coins_balance_myaccount', array( $this, 'show_coins_balance_myaccount' ) );
	}

}
