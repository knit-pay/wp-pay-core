<?php
/**
 * Subscription Post Type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Admin
 */

namespace Pronamic\WordPress\Pay\Admin;

use Pronamic\WordPress\Pay\Core\Statuses;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Subscriptions\Subscription;
use Pronamic\WordPress\Pay\Subscriptions\SubscriptionPostType;
use WP_Post;

/**
 * WordPress admin subscription post type
 *
 * @author  Reüel van der Steege
 * @version 2.1.0
 * @since   1.0.0
 */
class AdminSubscriptionPostType {
	/**
	 * Post type.
	 *
	 * @var string
	 */
	const POST_TYPE = 'pronamic_pay_subscr';

	/**
	 * Plugin.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructs and initializes an admin payment post type object.
	 *
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'request', array( $this, 'request' ) );

		add_filter( 'manage_edit-' . self::POST_TYPE . '_columns', array( $this, 'columns' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'list_table_primary_column', array( $this, 'primary_column' ), 10, 2 );

		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
	}

	/**
	 * Filters and sorting handler.
	 *
	 * @link https://github.com/woothemes/woocommerce/blob/2.3.13/includes/admin/class-wc-admin-post-types.php#L1585-L1596
	 *
	 * @param  array $vars Request variables.
	 * @return array
	 */
	public function request( $vars ) {
		$screen = get_current_screen();

		// Check payment post type.
		if ( self::POST_TYPE !== $screen->post_type ) {
			return $vars;
		}

		// Check post status var.
		if ( isset( $vars['post_status'] ) && ! empty( $vars['post_status'] ) ) {
			return $vars;
		}

		// Set request post status from payment states.
		$vars['post_status']   = array_keys( SubscriptionPostType::get_states() );
		$vars['post_status'][] = 'publish';

		return $vars;
	}

	/**
	 * Columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function columns( $columns ) {
		$columns = array(
			'cb'                              => '<input type="checkbox" />',
			'pronamic_subscription_status'    => sprintf(
				'<span class="pronamic-pay-tip pronamic-pay-icon" title="%s">%s</span>',
				esc_html__( 'Status', 'pronamic_ideal' ),
				esc_html__( 'Status', 'pronamic_ideal' )
			),
			'pronamic_subscription_title'     => __( 'Subscription', 'pronamic_ideal' ),
			'pronamic_subscription_customer'  => __( 'Customer', 'pronamic_ideal' ),
			'pronamic_subscription_amount'    => __( 'Amount', 'pronamic_ideal' ),
			'pronamic_subscription_recurring' => __( 'Recurrence', 'pronamic_ideal' ),
			'pronamic_subscription_date'      => __( 'Date', 'pronamic_ideal' ),
		);

		return $columns;
	}

	/**
	 * Sortable columns.
	 *
	 * @param array $sortable_columns Sortable columns.
	 * @return array
	 */
	public function sortable_columns( $sortable_columns ) {
		$sortable_columns['pronamic_subscription_title'] = 'ID';
		$sortable_columns['pronamic_subscription_date']  = 'date';

		return $sortable_columns;
	}

	/**
	 * Primary column name.
	 *
	 * @param string $column_name Primary column name.
	 * @param string $screen_id   Screen ID.
	 *
	 * @return string
	 */
	public function primary_column( $column_name, $screen_id ) {
		if ( 'edit-pronamic_pay_subscr' !== $screen_id ) {
			return $column_name;
		}

		return 'pronamic_subscription_title';
	}

	/**
	 * Custom columns.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-admin/includes/class-wp-posts-list-table.php#L1183-L1193
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public function custom_columns( $column, $post_id ) {
		$subscription = get_pronamic_subscription( $post_id );

		if ( null === $subscription ) {
			return;
		}

		switch ( $column ) {
			case 'pronamic_subscription_status':
				$post_status = get_post_status( $post_id );

				$label = __( 'Unknown', 'pronamic_ideal' );

				if ( 'trash' === $post_status ) {
					$post_status = get_post_meta( $post_id, '_wp_trash_meta_status', true );
				}

				$status_object = get_post_status_object( $post_status );

				if ( isset( $status_object, $status_object->label ) ) {
					$label = $status_object->label;
				}

				printf(
					'<span class="pronamic-pay-tip pronamic-pay-icon %s" title="%s">%s</span>',
					esc_attr( AdminModule::get_post_status_icon_class( $post_status ) ),
					esc_attr( $label ),
					esc_html( $label )
				);

				break;
			case 'pronamic_subscription_title':
				$source             = $subscription->get_source();
				$source_id          = $subscription->get_source_id();
				$source_description = $subscription->get_source_description();

				$source_id_text = '#' . strval( $source_id );

				$source_link = $subscription->get_source_link();

				if ( null !== $source_link ) {
					$source_id_text = sprintf(
						'<a href="%s">%s</a>',
						esc_url( $source_link ),
						$source_id_text
					);
				}

				echo wp_kses(
					sprintf(
						/* translators: 1: Subscription edit post link with post ID, 2: Subscription source description, 3: Subscription source ID text */
						__( '%1$s for %2$s %3$s', 'pronamic_ideal' ),
						sprintf(
							'<a href="%s" class="row-title"><strong>#%s</strong></a>',
							esc_url( get_edit_post_link( $post_id ) ),
							esc_html( $post_id )
						),
						$source_description,
						$source_id_text
					),
					array(
						'a'      => array(
							'href'  => true,
							'class' => true,
						),
						'strong' => array(),
					)
				);

				break;
			case 'pronamic_subscription_gateway':
				$payment = get_pronamic_payment_by_meta( '_pronamic_payment_subscription_id', $post_id );

				if ( $payment ) {
					$config_id = get_post_meta( $payment->get_id(), '_pronamic_payment_config_id', true );
				}

				if ( isset( $config_id ) && ! empty( $config_id ) ) {
					echo esc_html( get_the_title( $config_id ) );
				} else {
					echo '—';
				}

				break;
			case 'pronamic_subscription_description':
				echo esc_html( get_post_meta( $post_id, '_pronamic_subscription_description', true ) );

				break;
			case 'pronamic_subscription_amount':
				echo esc_html( $subscription->get_total_amount()->format_i18n() );

				break;
			case 'pronamic_subscription_recurring':
				echo esc_html( \Pronamic\WordPress\Pay\Util::format_interval( $subscription->get_interval(), $subscription->get_interval_period() ) );
				echo '<br />';
				echo esc_html( \Pronamic\WordPress\Pay\Util::format_frequency( $subscription->get_frequency() ) );

				break;
			case 'pronamic_subscription_date':
				if ( null !== $subscription->date ) {
					echo esc_html( $subscription->date->format_i18n() );
				}

				break;
			case 'pronamic_subscription_customer':
				echo esc_html( get_post_meta( $post_id, '_pronamic_subscription_customer_name', true ) );

				break;
		}
	}

	/**
	 * Add meta boxes.
	 *
	 * @param string $post_type Post Type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		add_meta_box(
			'pronamic_subscription',
			__( 'Subscription', 'pronamic_ideal' ),
			array( $this, 'meta_box_info' ),
			$post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_payment_lines',
			__( 'Payment Lines', 'pronamic_ideal' ),
			array( $this, 'meta_box_lines' ),
			$post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_subscription_payments',
			__( 'Payments', 'pronamic_ideal' ),
			array( $this, 'meta_box_payments' ),
			$post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_subscription_notes',
			__( 'Notes', 'pronamic_ideal' ),
			array( $this, 'meta_box_notes' ),
			$post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_subscription_update',
			__( 'Update', 'pronamic_ideal' ),
			array( $this, 'meta_box_update' ),
			$post_type,
			'side',
			'high'
		);

		// @link http://kovshenin.com/2012/how-to-remove-the-publish-box-from-a-post-type/.
		remove_meta_box( 'submitdiv', $post_type, 'side' );
	}

	/**
	 * Pronamic Pay subscription info meta box.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_info( $post ) {
		include plugin_dir_path( $this->plugin->get_file() ) . 'admin/meta-box-subscription-info.php';
	}

	/**
	 * Pronamic Pay payment lines meta box.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_lines( $post ) {
		$subscription = get_pronamic_subscription( $post->ID );

		if ( empty( $subscription ) ) {
			return;
		}

		$lines = $subscription->get_lines();

		include plugin_dir_path( $this->plugin->get_file() ) . 'admin/meta-box-payment-lines.php';
	}

	/**
	 * Pronamic Pay subscription notes meta box.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_notes( $post ) {
		$notes = get_comments(
			array(
				'post_id' => $post->ID,
				'type'    => 'subscription_note',
				'orderby' => array( 'comment_date_gmt', 'comment_ID' ),
			)
		);

		include plugin_dir_path( $this->plugin->get_file() ) . 'admin/meta-box-notes.php';
	}

	/**
	 * Pronamic Pay subscription payments meta box.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_payments( $post ) {
		include plugin_dir_path( $this->plugin->get_file() ) . 'admin/meta-box-subscription-payments.php';
	}

	/**
	 * Pronamic Pay subscription update meta box.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_update( $post ) {
		wp_nonce_field( 'pronamic_subscription_update', 'pronamic_subscription_update_nonce' );

		include plugin_dir_path( $this->plugin->get_file() ) . 'admin/meta-box-subscription-update.php';
	}

	/**
	 * Post row actions.
	 *
	 * @param array   $actions Actions array.
	 * @param WP_Post $post    WordPress post.
	 * @return array
	 */
	public function post_row_actions( $actions, $post ) {
		if ( self::POST_TYPE === $post->post_type ) {
			$actions = array( '' );
		}

		return $actions;
	}
}
