<?php
/*
* Plugin Name: WooCommerce Custom Thank You Messages
* Description: Extends Woocommerce and allows you to set the Thank You Messages on the Order Received Page for each Order Status.
* Version: 1.0.0
* Author: Creative Little Dots
* Author URI: http://creativelittledots.co.uk
*
* Text Domain: woocommerce-custom-thank-you-messages
* Domain Path: /languages/
*
* Requires at least: 3.8
* Tested up to: 4.1.1
*
* Copyright: © 2009-2015 Creative Little Dots
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Custom_Thank_You_Messages {
	
	public function __construct() {
		
		add_filter( 'woocommerce_get_settings_pages', array($this, 'add_messages_settings_page') );
		add_action( 'woocommerce_admin_field_editor', array($this, 'display_editor') );
		add_filter( 'woocommerce_thankyou_order_received_text', array($this, 'display_custom_message'), 10, 2 );
		
	}
	
	public function add_messages_settings_page($settings) {
		
		$settings[] = include( 'classes/class-wc-settings-messages.php' );
		
		return $settings;
		
	}
	
	public function display_editor($value) {
		
		if ( ! isset( $value['type'] ) ) {
			continue;
		}
		if ( ! isset( $value['id'] ) ) {
			$value['id'] = '';
		}
		if ( ! isset( $value['title'] ) ) {
			$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
		}
		if ( ! isset( $value['class'] ) ) {
			$value['class'] = '';
		}
		if ( ! isset( $value['css'] ) ) {
			$value['css'] = '';
		}
		if ( ! isset( $value['default'] ) ) {
			$value['default'] = '';
		}
		if ( ! isset( $value['desc'] ) ) {
			$value['desc'] = '';
		}
		if ( ! isset( $value['desc_tip'] ) ) {
			$value['desc_tip'] = false;
		}
		if ( ! isset( $value['placeholder'] ) ) {
			$value['placeholder'] = '';
		}
		if ( ! isset( $value['settings'] ) ) {
			$value['settings'] = array();
		}
		
		// Description handling
		$field_description = WC_Admin_Settings::get_field_description( $value );
		
		extract( $field_description );
		
		// Custom attribute handling
		$custom_attributes = array();
		
		$value['css'] = "display:none;";

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		
		
		$option_value = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		
		?>
		
		<tr valign="top">
			
			<th scope="row" class="titledesc">
				
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				
				<?php echo $tooltip_html; ?>
				
			</th>
			
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">

				<?php wp_editor( $option_value, $value['id'], $value['settings']); ?>
				
				<?php echo $description; ?>
					
				</div>
					
			</td>
			
		</tr>
					
		<?php
		
	}
	
	public function display_custom_message($message, $order) {
		
		if( $message = get_option('wc-' . $order->get_status() . '_messages_message') ) {
			
			$replace = apply_filters('woocommerce_custom_thank_you_messages_replace_strings', array(
				'{order_number}' => $order->id, 
				'{order_checkout_payment_url}' => $order->get_checkout_payment_url()
			), $order);
			
			$message = str_replace(array_keys($replace),array_values($replace), $message);
			
			$message = wpautop(do_shortcode($message));
			
		}

		return $message;
		
	}
	
}

$GLOBALS['WC_Custom_Thank_You_Messages'] = new WC_Custom_Thank_You_Messages();

?>