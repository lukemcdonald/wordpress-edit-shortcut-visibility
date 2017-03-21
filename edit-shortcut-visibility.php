<?php
/**
 * Plugin Name: Customizer Edit Shortcut Visibility
 * Plugin URI: https://lukemcdonald.com/
 * Description: A handy plugin to hide edit shortcuts in the customizer per user.
 * Version: 1.0.0
 * Author: Luke McDonald
 * Author URI: https://lukemcdonald.com/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: edit-shortcode-visibility
 */

class Edit_Shortcut_Visibility {
	public function load() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		if ( is_admin() ) {
			add_action( 'personal_options',        array( $this, 'render_user_settings_fields' ) );
			add_action( 'personal_options_update', array( $this, 'update_user_settings' ) );
		}
	}

	/**
	 * Enqueue demo bar assets.
	 */
	public function enqueue_assets() {
		if ( ! is_customize_preview() || 'yes' === get_user_meta( get_current_user_id(), 'enable_edit_shortcut_visibility', true ) ) {
			return;
		}

		// http://wordpress.stackexchange.com/questions/252799/disable-visible-edit-shortcuts-in-the-customizer
		$js = 'wp.customize.selectiveRefresh.Partial.prototype.createEditShortcutForPlacement = function() {};';
		wp_add_inline_script( 'customize-selective-refresh', $js );
	}

	/**
	 * Add a field for user's to enable the demo bar.
	 */
	public function render_user_settings_fields( $user ) {
		$enable_edit_shortcut_visibility = get_user_meta( $user->ID, 'enable_edit_shortcut_visibility', true );
		?>
		<tr>
			<th scope="row"><label for="enable-edit-shortcut-visibility">Customizer Edit Shortcuts</label></th>
			<td>
				<label for="enable-edit-shortcut-visibility">
					<input type="checkbox" name="enable_edit_shortcut_visibility" id="enable-edit-shortcut-visibility" value="yes"<?php checked( 'yes', $enable_edit_shortcut_visibility ); ?>>
					<?php esc_html_e( 'Show edit shortcuts in customizer', 'edit-shortcut-visibility' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save user settings.
	 */
	public function update_user_settings( $user_id  ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		update_user_meta( $user_id, 'enable_edit_shortcut_visibility', $this->get_checkbox_value( 'enable_edit_shortcut_visibility' ) );
	}

	/**
	 * Retrieve a checkbox value from the $_POST superglobal.
	 */
	protected function get_checkbox_value( $key ) {
		$value = '';

		if ( isset( $_POST[ $key ] ) && 'yes' === $_POST[ $key ] ) {
			$value = 'yes';
		}

		return $value;
	}
}

$edit_shortcut_visibility = new Edit_Shortcut_Visibility();
$edit_shortcut_visibility->load();
