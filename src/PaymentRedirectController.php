<?php
/**
 * Payment Redirect controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay;

use Pronamic\WordPress\Http\Facades\Http;
use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use WP_REST_Request;

/**
 * Payment Redirect controller
 *
 * @author GautamMKGarg
 * @version 1.0.0
 * @since 1.0.0
 */
class PaymentRedirectController {
    /**
     * Plugin object.
     *
     * @var Plugin
     */
    private $plugin;
    
    /**
     * Construct Payment Redirect controller.
     *
     * @param Plugin $plugin Plugin object.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
    }
    
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
		
		add_action( 'init',  function() {
		    /**
		     * ([^/]*) - taxonomy
		     * ([^/]*) - term
		     * ([0-9]{4}) - year 2021
		     * ([0-9]{2}) - month 11
		     *
		     */
		    add_rewrite_rule(
		        'knit-pay/payments/(\d+)/redirect/(\w+)',
		        'index.php?payment_redirect=$matches[1]&key=$matches[2]',
		        'top'
		        );
		} );
		
		    add_filter( 'query_vars', function( $query_vars ) {
		        $query_vars[] = 'payment_redirect';
		        $query_vars[] = 'key';
		        return $query_vars;
		    } );
		    
		        add_filter( 'template_include', function( $template ) {return $template;
		            if ( get_query_var( 'payment_redirect' ) == false || get_query_var( 'payment_redirect' ) == '' ) {
		                return $template;
		            }
		            
		            // Get payment.
		            $payment_id = get_query_var( 'payment_redirect' );
		            
		            $payment = get_pronamic_payment( $payment_id );
		            
		            if ( null === $payment ) {
		                return;
		            }
		            
		            // Validate key.
		            $key = \sanitize_text_field( \wp_unslash( get_query_var( 'key' ) ) );

		            if ( $key !== $payment->key || empty( $payment->key ) ) {
		                return;
		            }
		            
		            // phpcs:enable WordPress.Security.NonceVerification.Recommended
		            
		            Core_Util::no_cache();
		            
		            $gateway = $payment->get_gateway();

		            if ( null !== $gateway ) {
		                // Give gateway a chance to handle redirect.
		                $gateway->payment_redirect( $payment );
		                
		                // Handle HTML form redirect.
		                if ( $gateway->is_html_form() ) {
		                    $gateway->redirect( $payment );
		                }
		            }
		            
		            // Redirect to payment action URL.
		            $action_url = $payment->get_action_url();
		            
		            if ( ! empty( $action_url ) ) {
		                wp_redirect( $action_url );
		                
		                exit;
		            }
		            
		        } );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 * @return void
	 */
	public function rest_api_init() {
		\register_rest_route(
		    $this->plugin->rest_base . '/v1',
			'payments/(?P<payment_id>\d+)/redirect/(?P<key>\w+)',
			[
				/**
				 * IPN and PDT variables.
				 *
				 * @link https://developer.paypal.com/docs/api-basics/notifications/ipn/IPNandPDTVariables/
				 */
				'args'                => [
					'hash'       => [
						'description' => \__( 'Hash.', 'pronamic-ideal' ),
						'type'        => 'string',
					],
					'payment_id' => [
						'description' => \__( 'Payment ID.', 'pronamic-ideal' ),
						'type'        => 'string',
					],
				],
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_payment_redirect' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * REST API PayPal cancel return permission handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool
	 */
	public function rest_api_paypal_cancel_return_permission( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		if ( empty( $payment_id ) ) {
			return false;
		}

		$hash = $request->get_param( 'hash' );

		if ( empty( $hash ) ) {
			return false;
		}

		return \wp_hash( $payment_id ) === $hash;
	}

	/**
	 * REST API PayPal cancel return handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_payment_redirect( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new \WP_Error(
				'rest_paypal_no_payment',
				\sprintf(
					/* translators: %s: Value of PayPayl `custom` parameter. */
					\__( 'No payment found by `payment_id` variable: %s.', 'pronamic-ideal' ),
					(string) $payment_id
				),
				[
					'status' => 404,
				]
			);
		}
		
		// Validate key.
		$key = $request->get_param( 'key' );
		
		if ( $key !== $payment->key || empty( $payment->key ) ) {
		    return;
		}
		
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		
		Core_Util::no_cache();
		
		$gateway = $payment->get_gateway();
		
		if ( null !== $gateway ) {
		    // Give gateway a chance to handle redirect.
		    $gateway->payment_redirect( $payment );
		    
		    // Handle HTML form redirect.
		    if ( $gateway->is_html_form() ) {
		        $gateway->redirect( $payment );
		    }
		}
		
		// Redirect to payment action URL.
		$action_url = $payment->get_action_url();
		
		if ( ! empty( $action_url ) ) {
		    wp_redirect( $action_url );
		    
		    exit;
		}

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		return new \WP_REST_Response( null, 303, [ 'Location' => $payment->get_return_redirect_url() ] );
	}
}
