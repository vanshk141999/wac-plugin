<?php
/**
 * Admin Class for the meta box and global settings for the plugin.
 *
 * @package WAC\Includes
 * @since 1.0.0
 */

namespace WAC\Includes;

use WAC\Includes\Traits\Get_Instance;

if ( ! defined( constant_name: 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class for the meta box and global settings for the plugin.
 *
 * @package WAC\Includes
 * @since 1.0.0
 */
class Admin {
	use Get_Instance;

	/**
	 * Plugin loader constructor
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_contributors_metabox' ) );
		add_action( 'save_post', array( $this, 'save_contributors_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Adds settings link in the plugin action links on plugin list page.
	 *
	 * @access public
	 * @hooked plugin_action_links
	 * @param array  $links The array of plugin action links.
	 * @param string $file The plugin file.
	 * @since 1.0.0
	 * @return array The updated array of plugin action links.
	 */
	public function add_settings_link( $links, $file ) {
		// check is the file is wac main file.
		if ( $file === 'wp-additional-contributors/wp-additional-contributors.php' ) {
			// add settings link to the array.
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=wac-display-settings' ) ) . '">' . esc_html__( 'Settings', 'wac' ) . '</a>' );
		}
		return $links;
	}

	/**
	 * Add settings page
	 *
	 * @access public
	 * @hooked admin_menu
	 * @since 1.0.0
	 * @return void
	 */
	public function add_settings_page() {
		add_submenu_page(
			'options-general.php',
			'',
			'WAC Card Settings',
			'manage_options',
			'wac-display-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render settings page
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
			<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
					<?php
						// output nonce field.
						settings_fields( 'wac_display_settings' );
						// output settings fields.
						do_settings_sections( 'wac-display-settings' );
						// submit button.
						submit_button( 'Save Settings' );
					?>
				</form>
			</div>
		<?php
	}

	/**
	 * Register settings page and register settings section and fields
	 *
	 * @access public
	 * @hooked admin_init
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings() {
		// Register wac settings.
		register_setting(
			'wac_display_settings',
			'wac_display_options',
			array(
				'sanitize_callback' => array( $this, 'sanitize_display_options' ),
			)
		);

		// Add settings section to display settings & title.
		add_settings_section(
			'wac_display_section',
			'Contributor Card Display Settings',
			null,
			'wac-display-settings'
		);

		// All the checkboxes required for the display settings.
		$display_elements = array(
			'avatar'  => 'Display Image',
			'name'    => 'Display Name',
			'bio'     => 'Bio',
			'website' => 'Website Link',
		);
		foreach ( $display_elements as $key => $label ) {
			add_settings_field(
				'wac_show_' . $key,
				$label,
				array( $this, 'render_checkbox_field' ),
				'wac-display-settings',
				'wac_display_section',
				array( 'key' => $key )
			);
		}

		// render order field.
		add_settings_field(
			'wac_element_order',
			'Element Order',
			array( $this, 'render_order_field' ),
			'wac-display-settings',
			'wac_display_section'
		);
	}

	/**
	 * Sanitize display options
	 *
	 * @access public
	 * @param array $input Input array.
	 * @since 1.0.0
	 * @return array Sanitized array.
	 */
	public function sanitize_display_options( $input ) {
		$output = array();

		// Sanitize checkboxes.
		$checkboxes = array( 'avatar', 'name', 'bio', 'website' );
		foreach ( $checkboxes as $key ) {
			$output[ 'show_' . $key ] = isset( $input[ 'show_' . $key ] ) ? 1 : 0;
		}

		// Sanitize element order.
		if ( isset( $input['element_order'] ) ) {
			$order                   = is_array( $input['element_order'] ) ? $input['element_order'] : explode( ',', $input['element_order'] );
			$output['element_order'] = array_filter( $order );
		} else {
			$output['element_order'] = array( 'name', 'bio', 'website' );
		}

		return $output;
	}

	/**
	 * Render checkbox field
	 *
	 * @access public
	 * @param array $args Arguments containing the key.
	 * @since 1.0.0
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$options = get_option(
			'wac_display_options',
			array(
				'show_avatar'   => 1,
				'show_name'     => 1,
				'show_bio'      => 1,
				'show_website'  => 1,
				'element_order' => array( 'name', 'bio', 'website' ),
			)
		);
		$key     = $args['key'];
		$checked = isset( $options[ 'show_' . $key ] ) ? $options[ 'show_' . $key ] : 1;
		echo "<input type='checkbox' name='wac_display_options[show_{$key}]' value='1' " . checked( 1, $checked, false ) . '/>';
	}

	/**
	 * Render order field
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function render_order_field() {
		$options = get_option(
			'wac_display_options',
			array(
				'show_name'     => 1,
				'show_bio'      => 1,
				'show_website'  => 1,
				'element_order' => array( 'name', 'bio', 'website' ),
			)
		);
		$order   = isset( $options['element_order'] ) ? $options['element_order'] : array( 'image', 'name', 'bio', 'website' );

		// Convert string to array if needed.
		if ( is_string( $order ) ) {
			$order = array_filter( explode( ',', $order ) );
		}

		ob_start();
		?>
		<ul id="wac-sortable" class="wac-sortable">
			<?php foreach ( $order as $element ) : ?>
				<?php if ( ! empty( $element ) ) : ?>
					<li class='ui-state-default' data-element='<?php echo esc_attr( $element ); ?>'>
						<span class='dashicons dashicons-menu'></span> 
						<?php echo ucfirst( esc_html( $element ) ); ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<input type='hidden' name='wac_display_options[element_order]' id='wac-element-order' 
				value='<?php echo esc_attr( is_array( $order ) ? implode( ',', $order ) : $order ); ?>' />
		<?php
		$output = ob_get_clean();
		echo $output;
	}

	/**
	 * Add contributors metabox in the posts
	 *
	 * @access public
	 * @hooked add_meta_boxes
	 * @since 1.0.0
	 * @return void
	 */
	public function add_contributors_metabox() {

		// if current user is other than admin, editor or author, return.
		if ( ! current_user_can( 'publish_posts' ) ) {
			return;
		}

		add_meta_box(
			'wp_contributors_metabox',
			'Contributors',
			array( $this, 'render_contributors_metabox' ),
			'post',
			'side',
			'high'
		);
	}

	/**
	 * Render contributors metabox
	 *
	 * @access public
	 * @param WP_Post $post Current post object.
	 * @since 1.0.0
	 * @return void
	 */
	public function render_contributors_metabox( $post ) {
		$wac_editor_meta       = get_post_meta( $post->ID, 'wac_editor_meta', true ) ?: array();
		$contributors_list     = $wac_editor_meta['contributors_list'] ?? array();
		$contributor_box_title = $wac_editor_meta['contributor_box_title'] ?? 'Contributors';
		$users                 = get_users();

		wp_nonce_field( 'wac_contributors_nonce', 'wac_contributors_nonce' );

		ob_start();
		?>
		<div class="wac-meta-box-ctn">
			<label class="wac-meta-box-label" for="wac_contributors_title"><?php _e( 'Contributors Box Title:', 'wac' ); ?></label>
			<input type="text" name="wac_contributor_box_title" class="wac-meta-box-input" value="<?php echo esc_attr( $contributor_box_title ); ?>">
		</div>
		<div class="wac-meta-box-ctn">
			<label class="wac-meta-box-label" for="wac_contributors"><?php _e( 'Contributors:', 'wac' ); ?></label>
			<ul>
				<?php foreach ( $users as $user ) : ?>
					<?php $checked = in_array( $user->ID, $contributors_list ) ? 'checked' : ''; ?>
					<li>
						<input type="checkbox" name="wac_contributors_list[]" value="<?php echo esc_attr( $user->ID ); ?>" <?php echo $checked; ?>> 
						<?php echo esc_html( $user->display_name ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
		$output = ob_get_clean();
		echo $output;
	}

	/**
	 * Save contributors metabox metas.
	 *
	 * @access public
	 * @param int $post_id Post ID.
	 * @since 1.0.0
	 * @return void
	 */
	public function save_contributors_metabox( $post_id ) {
		// return if nonce is not set or nonce verification fails.
		if ( ! isset( $_POST['wac_contributors_nonce'] ) || ! wp_verify_nonce( $_POST['wac_contributors_nonce'], 'wac_contributors_nonce' ) ) {
			return;
		}
		// Check if this is an auto save routine. If it is, return.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check user permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Sanitize and save the title.
		$contributor_box_title = isset( $_POST['wac_contributor_box_title'] ) ? sanitize_text_field( $_POST['wac_contributor_box_title'] ) : 'Contributors';
		// Sanitize and save the contributors list.
		$contributors_list = isset( $_POST['wac_contributors_list'] ) ? array_map( 'intval', $_POST['wac_contributors_list'] ) : array();

		// Save both title and contributors as an associative array.
		$wac_editor_meta = array(
			'contributors_list'     => $contributors_list,
			'contributor_box_title' => $contributor_box_title,
		);

		update_post_meta( $post_id, 'wac_editor_meta', $wac_editor_meta );
	}

	/**
	 * Enqueue admin assets
	 *
	 * @access public
	 * @hooked admin_enqueue_scripts
	 * @param string $hook Current admin page hook.
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {

		// load styles on both global settings and editor page.
		wp_enqueue_style(
			'wac-admin-css',
			WAC_ASSETS . '/css/admin.css',
			array(),
			WAC_VERSION
		);

		if ( 'settings_page_wac-display-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script(
			'wac-admin-js',
			WAC_ASSETS . '/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			WAC_VERSION,
			true
		);
	}
}
