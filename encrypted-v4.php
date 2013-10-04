<?php

class acf_field_encrypted extends acf_field
{
	// vars
	public $settings, // will hold info such as dir / path
		$defaults; // will hold default field options

	public function __construct() {
		// vars
		$this->name = 'encrypted';
		$this->label = __('encrypted text','acf-encrypted');
		$this->category = __("Basic",'acf');
		$this->defaults = array(
			'key_string' => self::random_str(),
			'input_type' => "text"
		);

		// do not delete!
		parent::__construct();

		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);
	}


	public function create_options( $field ) {

		$field = array_merge($this->defaults, $field);
		$key = $field['name'];
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?> field_option_<?php echo $this->name; ?>_input_type">
			<td class="label">
				<label><?php _e("Type",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'    =>  'radio',
					'name'    =>  'fields[' . $key . '][input_type]',
					'value'   =>  $field['input_type'],
					'layout'  =>  'horizontal',
					'choices' =>  array(
						'type' => __('Text','acf'),
						'password' => __('Password','acf'),
					)
				));

				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?> field_option_<?php echo $this->name; ?>_key_string">
			<td class="label">
				<label><?php _e("Key Strings",'acf-encrypted'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][key_string]',
					'value'	=>	$field['key_string'],
				));
				?>
			</td>
		</tr>
		<?php
	}


	public function create_field( $field ) {
		echo '<input type="' . esc_attr( $field['input_type'] ) . '" value="' . esc_attr( $field['value'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" />';
	}


	public function load_value( $value, $post_id, $field ) {

		if( $value ) {
			$key = self::get_post_key($post_id).$field['key_string'];
			$value = Encryption::decrypt( $value ,$key ,$post_id );
		}
		$value = mb_substr( $value, 0, mb_strlen($value)-10 );
		return $value;
	}


	public function update_value( $value, $post_id, $field ) {
		$salt = substr(md5($field["name"]),0, 8);
		$value = $value."++".$salt;
		if( $value ) {
			$key = self::get_post_key($post_id).$field['key_string'];
			$value = Encryption::encrypt( $value ,$key, $post_id );
		}
		return $value;
	}

	public static function random_str($length = 16){
		return substr(base_convert(md5(uniqid()), 16, 36), 0, $length);
	}


	public static function get_post_key( $post_id ) {
		if ($parent_id = wp_is_post_revision($post_id) ) {
			$post_id = $parent_id;
		}
		$key = get_post_meta($post_id, "post_key" ,true);
		if($key === ""){
			$key = self::random_str();
			update_post_meta($post_id, "post_key", $key);
		}

		return $key;
	}


	public function field_group_admin_enqueue_scripts(){

		wp_register_style('acf-input-encrypted', $this->settings['dir'] . 'css/field-group.css', array('acf-field-group'), $this->settings['version']);

		wp_enqueue_style(array(
			'acf-input-encrypted',
		));
	}
}


class Encryption {

	private static function mcrypt_open($key) {

		$key = md5($key);
		$td  = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
		$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

		if (mcrypt_generic_init($td, $key, $iv) < 0) {
			exit('error.');
		}

		return $td;
	}

	private static function mcrypt_close( $td ) {
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
	}

	public static function encrypt( $input, $key, $post_id ) {
		$td = self::mcrypt_open($key);
		$encrypt_text = base64_encode(mcrypt_generic($td, $input));//返り値がバイナリなのでbase64に。
		self::mcrypt_close($td);

		return $encrypt_text;
	}

	public static function decrypt( $input, $key, $post_id ) {
		if( !$input ) {
			return $input;
		}
		$td = self::mcrypt_open($key);
		$decrypt_text = mdecrypt_generic($td, base64_decode( $input ));
		self::mcrypt_close($td);

		return $decrypt_text = str_replace("\0", "", $decrypt_text);
	}
}

// create field
new acf_field_encrypted();
?>