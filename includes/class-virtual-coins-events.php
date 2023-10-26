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
class Virtual_Coins_Events {

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
	 * Function returns the array of earning events.
	 *
	 * @return array
	 */
	private function declare_earning_events() {
		$args = array(
			'select_cp',
			'register',
			'chat_cp',
			'review',
			'notify',
			'checkin',
			'purchase',
		);
		return $args;
	}

	/**
	 * Function returns the options of individual coins events.
	 *
	 * @param string $event event name.
	 *
	 * @return array
	 */
	private function get_coins_options( $event ) {
		$options = get_field( $event, 'option' );
		return $options;
	}

	/**
	 * Function updates user's wallet after coins event
	 *
	 * @param mixed  $user_id user id.
	 * @param int    $value value of wallet operation.
	 * @param string $operation symbolic name of wallet operation ( earn, spend, expire).
	 *
	 * @return void
	 */
	private function update_wallet( $user_id, $value, $operation ) {

		$balance = 0;
		if ( ! empty( get_user_meta( $user_id, 'coins_wallet_balance', true ) ) ) {
			$balance = (int) get_user_meta( $user_id, 'coins_wallet_balance', true );
		}

		if ( in_array( $operation, $this->declare_earning_events() ) ) {
			$earnings = 0;
			if ( ! empty( get_user_meta( $user_id, 'coins_earned', true ) ) ) {
				$earnings = (int) get_user_meta( $user_id, 'coins_earned', true );
			}
			$new_value   = $earnings + $value;
			$new_balance = $balance + $value;
			update_user_meta( $user_id, 'coins_earned', $new_value );
			update_user_meta( $user_id, 'coins_wallet_balance', $new_balance );
		}

		if ( 'spend' === $operation ) {
			$spends = 0;
			if ( ! empty( get_user_meta( $user_id, 'coins_spent', true ) ) ) {
				$spends = (int) get_user_meta( $user_id, 'coins_spent', true );
			}
			$new_value   = $spends + $value;
			$new_balance = $balance - $value;
			update_user_meta( $user_id, 'coins_spent', $new_value );
			update_user_meta( $user_id, 'coins_wallet_balance', $new_balance );
		}

		if ( 'expire' === $operation ) {
			$lost = 0;
			if ( ! empty( get_user_meta( $user_id, 'coins_expired', true ) ) ) {
				$lost = (int) get_user_meta( $user_id, 'coins_expired', true );
			}
			$new_value   = $lost + $value;
			$new_balance = $balance - $value;
			update_user_meta( $user_id, 'coins_expired', $new_value );
			update_user_meta( $user_id, 'coins_wallet_balance', $new_balance );
		}
	}

	/**
	 * Function updates coins event meta
	 *
	 * @param mixed  $post_id user id.
	 * @param int    $value value of operation.
	 * @param string $operation symbolic name of operation (earn, spend, expire).
	 *
	 * @return void
	 */
	private function update_coin_event( $post_id, $value, $operation ) {

		if ( in_array( $operation, $this->declare_earning_events() ) ) {
			$now = DateTime::createFromFormat( 'U.u', microtime( true ) );
			$args = array(
				'coin_event_value'           => (int) $value,
				'coin_event_type'            => $this->get_coins_options( $operation )['event_name'],
				'coin_event_balance'         => (int) $value,
				// @codingStandardsIgnoreStart
				'coin_event_date'            => $now->format( 'Y-m-d H:i:s.u' ),
				'coin_event_status'          => 'active',
				'coin_event_expiration_date' => date( 'Y-m-d H:i:s', strtotime( 'now + ' . (int) $this->get_coins_options( $operation )['coins_expire'] . ' days' ) ),
				// @codingStandardsIgnoreEnd
			);
			foreach ( $args as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		if ( 'expire' === $operation ) {
			$lost = 0;
			if ( ! empty( get_user_meta( $user_id, 'coins_expired', true ) ) ) {
				$lost = (int) get_user_meta( $user_id, 'coins_expired', true );
			}
			$new_value   = $lost + $value;
			$new_balance = $balance - $value;
			update_user_meta( $user_id, 'coins_expired', $new_value );
			update_user_meta( $user_id, 'coins_wallet_balance', $new_balance );
		}
	}


	/**
	 * Function to check if event can bring the coins
	 *
	 * @param string $event event name.
	 * @param int    $user_id user id.
	 *
	 * @return bool
	 */
	private function check_repeats( $event, $user_id ) {

		global $wpdb;
		$repeat    = true;
		$completed = $wpdb->get_results(
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
		$repeats   = (int) $this->get_coins_options( $event )['event_repeat'];
		$uses      = count( $completed );

		if ( $uses >= $repeats ) {
			$repeat = false;
		}
		return $repeat;
	}

	/**
	 * Function to check if event can bring the coins
	 *
	 * @param string $event event name.
	 * @param object $user user object.
	 *
	 * @return void
	 */
	private function add_coins( $event, $user ) {

		$args = array(
			'select_cp' => 'Select Collection Point',
			'register'  => 'Register',
			'chat_cp'   => 'Chat with Collection Point Host',
			'review'    => 'Leave a Review',
			'notify'    => 'Turn On Notification',
			'purchase'  => 'Purchase Products',
		);

		$coin_data = array(
			'post_status'  => 'publish',
			'post_type'    => 'coin',
			'post_author'  => $user->ID,
			'post_title'   => 'Coin - ' . $args[ $event ] . ' - ' . $user->user_email,
			'post_content' => $event,
		);
		$coin_id   = wp_insert_post( $coin_data, true );

		$this->update_coin_event( $coin_id, $this->get_coins_options( $event )['event_reward'], $event );
		$this->update_wallet( $user->ID, (int) $this->get_coins_options( $event )['event_reward'], $event );
	}

	/**
	 * Create and add the coins when select the collection points.
	 *
	 * @param int    $meta_id ID of updated metadata entry.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @param mixed  $_meta_value Metadata value.
	 *
	 * @return void
	 */
	public function coins_collection_point( $meta_id, $object_id, $meta_key, $_meta_value ) {

		if ( 'current_gbl' === $meta_key ) {

			$user = wp_get_current_user();

			if ( true === $this->check_repeats( 'select_cp', $user->ID ) ) {
				$this->add_coins( 'select_cp', $user );
			}
		}
	}

	/**
	 * Create and add the coins when register.
	 *
	 * @param int $user_id ID of new registered user.
	 *
	 * @return void
	 */
	public function coins_registration( $user_id ) {

		if ( is_admin() ) {
			return;
		}

		$user = get_user_by( 'id', $user_id );

		if ( true === $this->check_repeats( 'register', $user->ID ) ) {
			$this->add_coins( 'register', $user );
		}
	}


	/**
	 * Create and add the coins when order placed.
	 *
	 * @param object $order ID of new order.
	 *
	 * @return void
	 */
	public function coins_purchase( $order ) {

		$order_object = wc_get_order( $order );
		$user         = $order_object->get_user();
		$items        = $order_object->get_items();

		foreach ( $items as $item ) {
			if ( true === $this->check_repeats( 'purchase', $user->ID ) ) {
				$this->add_coins( 'purchase', $user );
			}
		}
	}


	/**
	 * Create and add the coins on user comment is approved.
	 *
	 * @param string $comment_id Id of the comment.
	 * @param string $comment_approved if comment approved.
	 *
	 * @return void
	 */
	public function coins_comment_approve( $comment_id, $comment_approved ) {

		$comment = get_comment( $comment_id );
		$user_id = $comment->user_id;
		$user    = get_user_by( 'ID', $user_id );

		if ( true === $this->check_repeats( 'review', $user_id ) ) {
			$this->add_coins( 'review', $user );
		}
	}

	/**
	 * Create and add the coins when profile completed.
	 *
	 * @param int    $meta_id ID of updated metadata entry.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @param mixed  $_meta_value Metadata value.
	 *
	 * @return void
	 */
	public function coins_notification( $meta_id, $object_id, $meta_key, $_meta_value ) {

		if ( 'agree_for_notification' === $meta_key && ( '1' === $_meta_value || true === $_meta_value ) ) {

			$user = wp_get_current_user();

			if ( true === $this->check_repeats( 'notify', $user->ID ) ) {
				$this->add_coins( 'notify', $user );
			}
		}

	}

	/**
	 * Create and add the coins when checkin.
	 *
	 * @return void
	 */
	public function coins_checkin() {

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			require_once ABSPATH . 'wp-admin/includes/image.php';

			$reward = 0;
			$step   = 0;

			if ( isset( $_POST['reward'] ) ) { // @codingStandardsIgnoreLine
				$reward = trim( sanitize_text_field( wp_unslash( $_POST['reward'] ) ) ); // @codingStandardsIgnoreLine
			}

			if ( isset( $_POST['step'] ) ) { // @codingStandardsIgnoreLine
				$step = trim( sanitize_text_field( wp_unslash( $_POST['step'] ) ) ); // @codingStandardsIgnoreLine
			}

			$coin_data = array(
				'post_status'  => 'publish',
				'post_type'    => 'coin',
				'post_author'  => $user->ID,
				'post_title'   => 'Coin - Checkin day-' . $step . ' - ' . $user->user_email,
				'post_content' => 'checkin_day_' . $step,
			);
			$coin_id   = wp_insert_post( $coin_data, true );

			$args = array(
				'coin_event_value'           => (int) $reward,
				'coin_event_type'            => 'checkin_day_' . $step,
				'coin_event_balance'         => (int) $reward,
				// @codingStandardsIgnoreStart
				'coin_event_date'            => date( 'Y-m-d H:i:s', strtotime( 'now' ) ),
				'coin_event_status'          => 'active',
				'coin_event_expiration_date' => date( 'Y-m-d H:i:s', strtotime( 'now + 90 days' ) ),
				// @codingStandardsIgnoreEnd
			);
			foreach ( $args as $key => $value ) {
				update_post_meta( $coin_id, $key, $value );
			}

			$this->update_wallet( $user->ID, (int) $reward, 'checkin' );
		}

	}

	/**
	 * Create and add the coins when add to cart.
	 *
	 * @return void
	 */
	public function coins_chat() {

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			require_once ABSPATH . 'wp-admin/includes/image.php';

			if ( true === $this->check_repeats( 'chat_cp', $user->ID ) ) {
				$this->add_coins( 'chat_cp', $user );
			}
		}

	}

	/**
	 * Manage redeeming coins in the cart.
	 *
	 * @return void
	 */
	public function manage_coins_cart_fee() {

		$user = wp_get_current_user();
		require_once ABSPATH . 'wp-admin/includes/image.php';

		global $woocommerce;

		$operation = '';
		$redeemed  = 0;

		if ( isset( $_POST['cart_coins'] ) ) {
				$redeemed = (int) trim( sanitize_text_field( wp_unslash( $_POST['cart_coins'] ) ) );
		}
		if ( isset( $_POST['operation'] ) ) {
				$operation = trim( sanitize_text_field( wp_unslash( $_POST['operation'] ) ) );
		}

		if ( 'redeem' === $operation ) {

			$available  = (int) get_user_meta( $user->ID, 'coins_wallet_balance', true );
			$to_convert = 0;

			if ( $redeemed > $available ) {
				$redeemed = $available;
			}

			$conversion = 1;
			if ( ! empty( get_field( 'coins_conversion', 'option' ) ) ) {
				$conversion = (int) get_field( 'coins_conversion', 'option' );
			}
			$to_convert = $redeemed * $conversion;

			setcookie( 'cru', $user->ID, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
			setcookie( 'crv', $to_convert, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );

		} elseif ( 'revert' === $operation ) {
			setcookie( 'cru', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
			setcookie( 'crv', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	/**
	 * Redeeming coins in the cart.
	 *
	 * @return void
	 */
	public function add_coins_cart_fee( $cart ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$user_id  = get_current_user_id();
		$discount = 0;
		if ( isset( $_COOKIE['cru'] ) && (int) $_COOKIE['cru'] === (int) $user_id ) {
			if ( isset( $_COOKIE['crv'] ) && 0 < (int) $_COOKIE['crv'] ) {
				$discount = (int) $_COOKIE['crv'];
			}
		}
		$cart->add_fee( 'Redeemed tokens', -1 * $discount );
	}

	/**
	 * Manage changing coins in the cart when something changes in the cart.
	 *
	 * @return void
	 */

	public function change_coins_cart_fee_on_cart_change() {
		$user = wp_get_current_user();
		require_once ABSPATH . 'wp-admin/includes/image.php';

		if ( isset( $_COOKIE['cru'] ) && (int) $_COOKIE['cru'] === (int) $user->ID ) {
			if ( isset( $_COOKIE['crv'] ) && 0 < (int) $_COOKIE['crv'] ) {

				setcookie( 'cru', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
				setcookie( 'crv', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
			}
		}
	}

	/**
	 * Redeeming coins in the cart.
	 *
	 * @return void
	 */
	public function redeem_cart_coins( $order_id ) {

		$order_object = wc_get_order( $order_id );
		$user         = $order_object->get_user();
		$user_id      = $user->ID;

		if ( isset( $_COOKIE['cru'] ) && (int) $_COOKIE['cru'] === (int) $user_id ) {
			if ( isset( $_COOKIE['crv'] ) && 0 < (int) $_COOKIE['crv'] ) {

				$discount = (int) $_COOKIE['crv'];

				$coins_args = array(
					'post_type'   => 'coin',
					'author'      => $user->ID,
					'post_status' => 'publish',
					'numberposts' => - 1,
					'orderby'     => 'ID',
					'order'       => 'ASC',
					'meta_query'  => array(
						array(
							'key'     => 'coin_event_balance',
							'value'   => 0,
							'compare' => '!=',
						),
					),
				);
				$coins      = get_posts( $coins_args );

				$redeemed_balance = $discount;

				foreach ( $coins as $coin ) {

					$coin_balance = (int) get_post_meta( $coin->ID, 'coin_event_balance', true );

					if ( 0 !== $coin_balance && 0 !== $redeemed_balance ) {

						if ( $redeemed_balance >= $coin_balance ) {
							$meta_args = array(
								'coin_event_balance'    => 0,
								'coin_event_spend'      => $coin_balance,
								'coin_event_spend_date' => date( 'Y-m-d H:i:m', strtotime( 'now' ) ), // @codingStandardsIgnoreLine
							);
							foreach ( $meta_args as $key => $value ) {
								update_post_meta( $coin->ID, $key, $value );
							}
							$redeemed_balance -= $coin_balance;
						} else {
							$meta_args = array(
								'coin_event_balance'    => $coin_balance - $redeemed_balance,
								'coin_event_spend'      => $redeemed_balance,
								'coin_event_spend_date' => date( 'Y-m-d H:i:m', strtotime( 'now' ) ), // @codingStandardsIgnoreLine
							);
							foreach ( $meta_args as $key => $value ) {
								update_post_meta( $coin->ID, $key, $value );
							}
							$redeemed_balance -= $redeemed_balance;
						}
					}
				}
				$user_coins_balance = (int) get_user_meta( $user->ID, 'coins_wallet_balance', true );
				$user_coins_spends  = (int) get_user_meta( $user->ID, 'coins_spent', true );
				$user_meta_args     = array(
					'coins_wallet_balance' => $user_coins_balance - $discount,
					'coins_spent'          => $user_coins_spends + $discount,
				);
				foreach ( $user_meta_args as $key => $value ) {
					update_user_meta( $user->ID, $key, $value );
				}
				setcookie( 'cru', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
				setcookie( 'crv', 0, time() + ( 10 * 365 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
			}
		}
	}

	/**
	 * Create and add the coins when login.
	 *
	 * @param string         $user_login Username.
	 * @param object WP_User $user object of the logged-in user.
	 *
	 * @return void
	 */
	public function coins_login( $user_login, WP_User $user ) {

		if ( true === $this->check_repeats( 'register', $user->ID ) ) {
			$this->add_coins( 'register', $user );
		}
		if ( true === $this->check_repeats( 'select_cp', $user->ID ) && ! empty( get_user_meta( $user->ID, 'current_gbl', true ) ) ) {
			$this->add_coins( 'select_cp', $user );
		}
	}

	/**
	 * Create and add the coins press get register tokens button
	 *
	 * @return void
	 */
	public function get_register_coins() {

		$user = wp_get_current_user();

		if ( true === $this->check_repeats( 'register', $user->ID ) ) {
			$this->add_coins( 'register', $user );
		}
	}



}
