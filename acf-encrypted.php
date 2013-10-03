<?php
/*
Plugin Name: Advanced Custom Fields: Encrypted Field
Version: 1.0.0
Author: Toro_Unit
Author URI: http://www.torounit.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class acf_field_encrypted_plugin
{
	/*
	*  Construct
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function __construct()
	{
		// set text domain
		$domain = 'acf-encrypted';
		$mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $mofile );

		// version 4+
		add_action('acf/register_fields', array($this, 'register_fields'));

	}


	/*
	*  register_fields
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function register_fields()
	{
		include_once('encrypted-v4.php');
	}

}

new acf_field_encrypted_plugin();

?>
