<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_TS_Email_Customer_Trusted_Shops' ) ) :

/**
 * eKomi Review Reminder Email
 *
 * This Email is being sent after the order has been marked as completed to transfer the eKomi Rating Link to the customer.
 *
 * @class 		WC_GZD_Email_Customer_Ekomi
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_TS_Email_Customer_Trusted_Shops extends WC_Email {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id 				= 'customer_trusted_shops';
		$this->title 			= _x( 'Trusted Shops Review Reminder', 'trusted-shops', 'woocommerce-trusted-shops' );
		$this->description		= _x( 'This E-Mail is being sent to a customer to remind him about the possibility to leave a review at Trusted Shops.', 'trusted-shops', 'woocommerce-trusted-shops' );

		$this->template_html 	= 'emails/customer-trusted-shops.php';
		$this->template_plain  	= 'emails/plain/customer-trusted-shops.php';

		// Triggers for this email
		add_action( 'woocommerce_germanized_trusted_shops_review_notification', array( $this, 'trigger' ) );

		if ( property_exists( $this, 'placeholders' ) ) {
			$this->placeholders   = array(
				'{site_title}'   => $this->get_blogname(),
				'{order_number}' => '',
				'{order_date}'   => '',
			);
		}

		// Call parent constuctor
		parent::__construct();

		$this->customer_email = true;
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return _x( 'Please rate your {site_title} order from {order_date}', 'trusted-shops', 'woocommerce-trusted-shops' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return _x( 'Please rate your Order', 'trusted-shops', 'woocommerce-trusted-shops' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	public function trigger( $order_id ) {
		if ( is_callable( array( $this, 'setup_locale' ) ) ) {
			$this->setup_locale();
		}

		if ( $order_id ) {
			$this->object 		= wc_get_order( $order_id );
			$this->recipient	= wc_ts_get_crud_data( $this->object, 'billing_email' );

			if ( property_exists( $this, 'placeholders' ) ) {
				$this->placeholders['{order_date}']   = wc_gzd_get_order_date( $this->object, wc_date_format() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			} else {
				$this->find['order-date']      = '{order_date}';
				$this->find['order-number']    = '{order_number}';
				$this->replace['order-date']   = wc_gzd_get_order_date( $this->object, wc_date_format() );
				$this->replace['order-number'] = $this->object->get_order_number();
			}
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		if ( is_callable( array( $this, 'restore_locale' ) ) ) {
			$this->restore_locale();
		}
	}

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 2.0.4
	 * @return string
	 */
	public function get_additional_content() {
		if ( is_callable( 'parent::get_additional_content' ) ) {
			return parent::get_additional_content();
		}

		return '';
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order' 		     => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin'      => false,
			'plain_text'         => false,
			'email'			     => $this
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain, array(
				'order' 		     => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'			     => $this
			)
		);
	}
}

endif;

return new WC_TS_Email_Customer_Trusted_Shops();