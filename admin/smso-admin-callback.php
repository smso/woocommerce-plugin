<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.smso.ro/
 * @since      1.0.0
 *
 * @package    smso
 * @subpackage smso/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    smso
 * @subpackage smso/admin
 * @author     smso <support@smso.ro>
 */
class Smso_Admin_Callback {
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public $states = array(
					'pending'    => array('label' => 'Pending', 'message_name'=> 'smso_pending_message', 'active_name'=> 'smso_pending_active'),
					'processing' => array('label' => 'Processing', 'message_name'=> 'smso_processing_message', 'active_name'=> 'smso_processing_active'),
					'on-hold'    => array('label' => 'On Hold', 'message_name'=> 'smso_on_hold_message', 'active_name'=> 'smso_on_hold_active'),
					'completed'  => array('label' => 'Completed', 'message_name'=> 'smso_completed_message', 'active_name'=> 'smso_completed_active'),
					'cancelled'  => array('label' => 'Cancelled', 'message_name'=> 'smso_cancelled_message', 'active_name'=> 'smso_cancelled_active'),
					'refunded'   => array('label' => 'Refunded', 'message_name'=> 'smso_refunded_message', 'active_name'=> 'smso_refunded_active'),
					'failed'     => array('label' => 'Failed', 'message_name'=> 'smso_failed_message', 'active_name'=> 'smso_failed_active'),
					 );

	public $smso_active;
	public $smso_token;
	public $smso_sender;
	public $smso_phone_number;

	public $smso_pending_active;
	public $smso_pending_message;

	public $smso_processing_active;
	public $smso_processing_message;

	public $smso_on_hold_active;
	public $smso_on_hold_message;

	public $smso_completed_active;
	public $smso_completed_message;

	public $smso_cancelled_active;
	public $smso_cancelled_message;

	public $smso_refunded_active;
	public $smso_refunded_message;

	public $smso_failed_active;
	public $smso_failed_message;

	public $definition = array(
	'smso_active'            => array(),
	'smso_token'             => array(),
	'smso_sender'            => array(),

	'smso_pending_active' 	 => array(),
	'smso_pending_message'   => array(),

	'smso_processing_active' => array(),
	'smso_processing_message'=> array(),

	'smso_on_hold_active' 	 => array(),
	'smso_on_hold_message'	 => array(),

	'smso_completed_active'  => array(),
	'smso_completed_message' => array(),

	'smso_cancelled_active'  => array(),
	'smso_cancelled_message' => array(),

	'smso_refunded_active'   => array(),
	'smso_refunded_message'  => array(),

	'smso_failed_active'     => array(),
	'smso_failed_message'    => array(),
	);
	
	public function __construct( $plugin_name, $version ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function getFileds()
	{
		foreach (array_keys($this->definition) as $key) {			 			 
		$this->$key = isset($_POST[$key]) ? trim($_POST[$key]) : ( isset($_GET[$key]) ? trim($_GET[$key]) : $this->$key );
		}	 
	}
	public function updateOptions()
	{
		foreach (array_keys($this->definition) as $key) {
			if(get_option($key) !== false){
				update_option($key, $this->$key);
			}else{
				add_option($key, $this->$key);
			}			 
		}	 
	}
	public function getOptions()
	{
		foreach (array_keys($this->definition) as $key) {
			if(get_option($key)){
				$this->$key = get_option($key);
			}
		}
	}
	public function admin_api_settings()
	{ 
		if(isset($_POST['smso_save_settings'])){
			$this->getFileds();
			$this->updateOptions();
		}
		if(isset($_POST['smso_test'])){
			if(!empty($_POST['smso_phone_number'])){
				$sms_admin = new Smso_Admin();
				$this->smso_phone_number = $sms_admin->checkPhone($_POST['smso_phone_number']);
				if(get_option('smso_phone_number') !== false){
					update_option('smso_phone_number', $this->smso_phone_number);
				}else{
					add_option('smso_phone_number', $this->smso_phone_number);
				}
				$message = 'THIS TEST WAS SEND FROM SMSO WORDPRESS PLUGIN!';
				$result_test['status'] = $sms_admin->sendTestSMS($this->smso_phone_number, $message);
				$result_test = $sms_admin->getErrorMessage($result_test['status']['status']);
				if(!$result_test['error']){
					$sms_admin->addToSmsoTable($this->smso_phone_number, 'THIS TEST WAS SEND FROM SMSO WORDPRESS PLUGIN!', $this->smso_token);
				}
			}else{
				if(get_option('smso_phone_number') !== false){
					update_option('smso_phone_number', "");
				}else{
					add_option('smso_phone_number', "");
				}
			}
		}
		$this->getOptions();
		return require_once(plugin_dir_path( dirname( __FILE__ ) ) . '/template/admin/admin_api_settings.tpl.php');
	}
}