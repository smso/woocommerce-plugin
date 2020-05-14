<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.smso.ro/
 * @since      1.0.0
 *
 * @package    smso
 * @subpackage smso/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    smso
 * @subpackage smso/includes
 * @author     smso <support@smso.ro>
 */
class Smso_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() 
	{
		Smso_Activator::db_history_smso();
	}
	private static function db_history_smso()
	{
 		global $wpdb;
 		$charset_collate = $wpdb->get_charset_collate();
 		$sql = "CREATE TABLE " . $wpdb->prefix . "smso_history (
 		id        int(9) NOT NULL AUTO_INCREMENT,
	    phone     longtext,
	    message   longtext,
	    date_sent datetime,
	    token 	  longtext,
	    PRIMARY KEY  (id)
	  ) $charset_collate;";
	   $result = $wpdb->query($sql);
	}
}
