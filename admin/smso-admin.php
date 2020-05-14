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
class Smso_Admin {

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
	private $admin_menu;	 
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name = "", $version = "") 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() 
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */		 		 
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smso-admin.css', array(), $this->version, 'all' );
		if(isset($_GET['page']) && strcmp($_GET['page'], "smso") == 0){
			wp_enqueue_style( $this->plugin_name . 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap/bootstrap.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() 
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smso-admin.js', array( 'jquery' ), $this->version, false );

	}
	public function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $component, $callback, $icon_url, $position) {
		$this->admin_menu[] = array(
								'page_title' => $page_title, 
								'menu_title' => $menu_title, 
								'capability' => $capability, 
								'menu_slug'  => $menu_slug, 
								'callback'   => array( $component, $callback ), 
								'icon_url'   => $icon_url, 
								'position'   => $position
							  );
	}
	public function run_menu_page()
	{
		foreach ( $this->admin_menu as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}
	}
	public function replacePattern($message, $pattern)
	{		
		foreach (array_keys($pattern) as $key) 
		{
	    	$message = str_replace($key, $pattern[$key], $message);
	    }
	    return $message;
	}
	// public function smso_checkout_update_order_meta($order_id)
	// { 
	// 	if($_POST['billing_country'] == 'RO' && $order_id && $phone = get_post_meta($order_id, "_billing_phone", true))
	// 	{
	// 		$phone = trim($phone);			 
	// 		preg_match_all('/^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/', $phone, $matches_ro, PREG_SET_ORDER, 0);
	// 		preg_match_all('/^\+4/', $phone, $matches, PREG_SET_ORDER, 0);			 
	// 		if(!empty($matches_ro) && empty($matches))
	// 		{
	// 			update_post_meta($order_id,"_billing_phone", "+4".$phone);
	// 		}
	// 	}
	// }
	// public function smso_after_checkout_validation($fields, $error)
	// {
	// 	if(isset($fields['billing_country']) && $fields['billing_country'] == 'RO' && is_numeric($fields['billing_phone'])) 
	// 	{
	// 		preg_match_all('/^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/', $fields['billing_phone'], $matches_ro, PREG_SET_ORDER, 0);
	// 		if(empty($matches_ro))
	// 		{
	// 		   $error->add( 'validation', 'Facturare Telefon nu este un număr de telefon valid pentru tara selectata - [RO]' );
	// 		}
	// 	}
	// }

	// public function smso_shipping_fields($fields)
	// {
		 
	// }
	public function smso_order_status_changed($order_id)
	{ 
		$order = new WC_Order($order_id);

		if(!empty($order->billing_country) && $order->billing_country == "RO"){			
			$order->billing_phone = str_replace("+4", "", $order->billing_phone); 
			$order->billing_phone = str_replace(" ", "", $order->billing_phone);
			$order->billing_phone = "+4" . $order->billing_phone;			 
		}

		$modul_active = (bool)get_option('smso_active');
		if( $modul_active &&
			 (get_option('smso_'.$order->status.'_active') !== false && strlen(get_option('smso_'.$order->status.'_active')) ||
			  get_option('smso_on_hold_active') !== false && strlen(get_option('smso_on_hold_active' ))))
		{
		   if(($message = get_option('smso_'.$order->status.'_message')) || ($message = get_option('smso_on_hold_message'))) {		   	 		   	 
		   	$modul_token = get_option('smso_token');
		    $message = $this->replacePattern($message, 
		    array(
	 		'{order_number}' 	    => $order->order_number,
	 		'{order_date}' 			=> $order->order_date,
	 		'{order_total}' 		=> $order->order_total,
			'{billing_first_name}'  => $order->billing_first_name,
			'{billing_last_name}' 	=> $order->billing_last_name,
			'{shipping_method}'     => $order->get_shipping_method()
			));

		   	$this->sendSMS($order->billing_phone, $message, $modul_active, $modul_token);
		   }
		}
	}
	public function sendSMS($to, $body, $modul_active, $modul_token)
    {
        if ($modul_active) {
            global $wpdb;             
            $smso = new Smso_Class($modul_token);
            $sender = $this->getRandomSender();
            if(!$sender) return true;
            $to = $this->checkPhone($to);
            $response = $smso->sendMessage($to, $body, $sender);
			$this->addToSmsoTable($to, $body, $modul_token);
            return true;
        }else {
            return true;
        }
    }    
    public function getRandomSender()
    {
        $values = array();
        $senders_arr = array(
            0 => get_option('smso_sender')
        );
        if(get_option('smso_token')) {
            $sms = new Smso_Class(get_option('smso_token')); 
            $senders = $sms->getSenders();
            foreach ($senders['response'] as $s) {
                $values[] = $s['id'];
            }

            $senderii = array_intersect($senders_arr, $values);
            $rnd = rand(0,(count($senderii)-1));             
            return $senderii[$rnd];
        } else {
            return false;
        }
    }
    public function checkPhone($to) {
        if(strlen($to) == 10){
            $to = "+4".$to;
        }
        return $to;
    }
    public static function getValueSender()
    {
        $values = array();
        $token = get_option('smso_token');
        if( $token != '') {
            $smso = new Smso_Class($token); 
            $senders = $smso->getSenders();
            foreach ($senders['response'] as $s) {
                $values[] = array(
                    'id' => $s['id'],
                    'label' => $s['name']."(cost:".$s['pricePerMessage'].")",
                    'value' => $s['id'],
                );
            }
        }
        return $values;
    }
    public function addToSmsoTable($phone = "", $message = "", $token = "")
    {	
		global $wpdb;
		$table = $wpdb->prefix . "smso_history";
		$sql_phone   = $phone; 
		$sql_message = $message;
		$sql_token   = $token;
		$sql_date    = date("Y-m-d H:i:s");
		$sql = "INSERT INTO " . $table . " (phone, message, date_sent, token) values ('". $sql_phone ."','" . $sql_message . "','" . $sql_date ."','". $sql_token ."')";
		$sql_prepare = $wpdb->prepare($sql);
		$result 	 = $wpdb->query($sql_prepare);
		return $result;
    }
    public function sendTestSMS($to, $body)
    {
    	$token = get_option('smso_token');    	 
    	if(!empty($token)){
	    	$smso = new Smso_Class($token);
	    	$sender = $this->getRandomSender();
	    	$to = $this->checkPhone($to);
	    	$response = $smso->sendMessage($to, $body, $sender);
	    	return $response;
    	}
    	return false;
    }
    public function getErrorMessage($status)
    {
    	$error = array(
    		'error'   => false,
    		'message' => 'The test is successful!'
    	);
    	if(strcmp($status, '401') == 0){
    		$error['error']   = true;
    		$error['message'] = 'Authorization required, API key is not valid.';
		}
		if(strcmp($status, '400') == 0){
			$error['error']   = true;
    		$error['message'] = 'The sender does not exists or you don\'t have access to it.';
		}
		if((bool)$status == false){
			$error['error']   = true;
    		$error['message'] = 'Authorization required, Token is not valid.';
		}
		return $error;
    }
}
