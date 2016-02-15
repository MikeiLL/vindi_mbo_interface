<?php
/**
 * This file contains all the actions and functions to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 1.0.0
 *
 * @package MZVINDIAPI
 * 
 */
 
add_action ('admin_menu', 'mz_vindi_mbo_settings_menu');

	function mz_vindi_mbo_settings_menu() {
		//create submenu under Settings
		add_options_page ('MZ Vindi MBO Settings', esc_attr__('Vindi MBO Interface', 'mz_vindi_mbo'),
		'manage_options', __FILE__, 'mz_vindi_mbo_settings_page');
	}

	function mz_vindi_mbo_settings_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<form action="options.php" method="post">
				<?php settings_fields('mz_vindi_mbo_options'); ?>
				<?php do_settings_sections('mz_vindi_mbo'); ?>
				<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
		<?php
	}

	// Register and define the settings
	add_action('admin_init', 'mz_vindi_mbo_admin_init');

	function mz_vindi_mbo_admin_init(){
		register_setting(
			'mz_vindi_mbo_options',
			'mz_vindi_mbo_options',
			'mz_vindi_mbo_validate_options'
		);
		
		add_settings_section(
			'mz_vindi_mbo',
			'MZ Vindi MBO',
			'mz_vindi_mbo_main',
			'mz_vindi_mbo'
		);
		
		add_settings_field(
			'mz_vindi_mbo_account_number',
			__('Vindi Account Number: ', 'mz_vindi_mbo'),
			'mz_vindi_mbo_account_number',
			'mz_vindi_mbo',
			'mz_vindi_mbo'
		);

	}

	function mz_vindi_mbo_main() {
		mz_pr(MZ_MINDBODY_SCHEDULE_DIR);
	}	
	
	// Display and fill the form field
	function mz_vindi_mbo_account_number() {
		// get option 'vindi account number' value from the database
		$options = get_option( 'mz_vindi_mbo_options',__('Option Not Set', 'mz-vindi-mbo') );
		$mz_vindi_account_number = (isset($options['mz_vindi_account_number'])) ? $options['mz_vindi_account_number'] : __('VINDI ACCOUNT NUMBER', 'mz-vindi_mbo');

		// echo the field
		echo "<input id='mz_vindi_mbo_options' name='mz_vindi_mbo_options[mz_vindi_account_number]' type='text' value='$mz_vindi_account_number' />";
	}
	
	
	// Validate user input (we want text only)
	function mz_vindi_mbo_validate_options( $input ) {
	    foreach ($input as $key => $value)
	    {
				$valid[$key] = wp_strip_all_tags(preg_replace( '/\s/', '', $input[$key] ));
				if( $valid[$key] != $input[$key] )
				{
					add_settings_error(
						'mz_vindi_mbo_text_string',
						'mz_vindi_mbo_texterror',
						'Does not appear to be valid ',
						'error'
					);
				}
			}

		return $valid;
	}
?>