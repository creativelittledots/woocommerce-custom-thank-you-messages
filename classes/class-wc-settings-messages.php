<?php
	
/**
 * WooCommerce Message Settings
 *
 * @author      Creative Little Dots
 * @category    Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_Messages' ) ) :
	
/**
 * WC_Settings_Mesages
 */
class WC_Settings_Messages  extends WC_Settings_Page  {

	public function __construct() {
		
		$this->id    = 'messages';
		$this->label = __( 'Messages', 'woocommerce-custom-thank-you-messages' );
		
		add_filter( 'woocommerce_settings_tabs_array', array($this, 'add_messages_page'), 21 );
		add_action( 'woocommerce_settings_' . $this->id, array($this, 'output') );
		add_action( 'woocommerce_settings_save_' . $this->id, array($this, 'save') );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		
	}	
	
	public function add_messages_page( $settings_tabs ) {
		
        $settings_tabs['messages'] = __( 'Messages', 'woocommerce-custom-thank-you-messages' );
        
        return $settings_tabs;
        
    }
    
    /**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
		
		$statuses = wc_get_order_statuses();
		
		$sections = array();
		
		$i = 0;
		
		foreach($statuses as $statusKey => $status) {
			
			if(in_array($statusKey, apply_filters('woocommerce_custom_thank_you_messages_disallowed_statuses', array('wc-failed'))))
				continue;
			
			if($i) {
				
				$sections[$statusKey] = $status;
				
			}
			
			else {
				
				$sections[''] = $status;
				
			}
			
			$i++;
			
		}

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		
	}
    
    /**
	 * Output the settings
	 */
    public function output() {
	    
	    global $current_section;

		$settings = $this->get_settings( $current_section );

 		WC_Admin_Settings::output_fields( $settings );
	    
	}
	
	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}
	
	public function get_settings( $current_section = '' ) {
		
		$settings = array();
		
		$statuses = wc_get_order_statuses();
		
		$current_section = $current_section ? $current_section : array_keys($statuses)[0];
		
		foreach($statuses as $statusKey => $status) {
			
			if ( $statusKey == $current_section ) {
		
				$settings = apply_filters( 'woocommerce_' . $statusKey . '_messages_settings', array(
				   
			        'section_title' => array(
			            'name'     => __( $status, 'woocommerce-custom-thank-you-messages' ),
			            'type'     => 'title',
			            'desc'     => '',
			            'id'       => $statusKey . '_' . $this->id . '_options'
			        ),
			        'editor' => array(
			            'name' => __( 'Message', 'woocommerce-custom-thank-you-messages' ),
			            'type' => 'editor',
			            'default' => __( 'Thank you. Your order has been received.', 'woocommerce'),
			            'settings' => array( 'media_buttons' => true ),
			            'desc' => __( 'This is the content to be displayed on the thank you page when an order is ' . $status, 'woocommerce-custom-thank-you-messages' ),
			            'id'   => $statusKey . '_' . $this->id . '_message'
			        ),
			        'section_end' => array(
			             'type' => 'sectionend',
			             'id' => $statusKey . '_' . $this->id . '_options'
			        )
			        
			    ) );
			    
			}
		
		}
	    
	    return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	    
	}
	
}


endif;

return new WC_Settings_Messages();

?>