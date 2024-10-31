<?php
/**
 * ReSRC_For_WordPress
 *
 * @package   ReSRC_For_WordPress_Admin
 * @author    ReSRC <team@resrc.it>
 * @license   GPL-2.0+
 * @link      http://www.resrc.it/wordpress
 * @copyright 2014 ReSRC
 */

/**
 * ReSRC_For_WordPress_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package ReSRC_For_WordPress_Admin
 * @author  ReSRC <team@resrc.it>
 */

class ReSRC_For_WordPress_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = ReSRC_For_WordPress::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

        add_action( 'admin_init', array( $this, 'resrc_options_init' ) );

			}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ReSRC_For_WordPress::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), 'nil', ReSRC_For_WordPress::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'ReSRC Settings', $this->plugin_slug ),
			__( 'ReSRC', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}



    
public function resrc_options_init() {
    register_setting( 'resrc-options-group', 'resrc-account-type' );
    register_setting( 'resrc-options-group', 'resrc-domain-pattern' );
    register_setting( 'resrc-options-group', 'resrc-enable-resrc', array ($this, 'resrc_enable_resrc_validate' ));
    add_settings_section( 'resrc-options', '', array ($this, 'resrc_options_callback'), 'ReSRC' );
    add_settings_field( 'resrc-account-type-field', 'Account Type', array ( $this, 'resrc_account_type_callback' ), 'ReSRC', 'resrc-options' );
    add_settings_field( 'resrc-domain-pattern-field', 'Domain Pattern', array ( $this, 'resrc_domain_pattern_callback' ), 'ReSRC', 'resrc-options' );
    add_settings_field( 'resrc-enable-field', 'Enable ReSRC', array ( $this, 'resrc_enable_resrc_callback' ), 'ReSRC', 'resrc-options' );
}

public function resrc_account_type_callback() {
    $setting = esc_attr( get_option( 'resrc-account-type' ) );
    echo '
    <fieldset><legend class="screen-reader-text"><span>Account Type</span></legend>
    <label title="Trial"><input type="radio" name="resrc-account-type" value="trial"';
    if ($setting == "trial") {
        echo " checked ";
    }
    echo '"> <span>Trial</span></label><br>
    <label title="Paid"><input type="radio" name="resrc-account-type" value="paid"';
    if ($setting == "paid") {
        echo " checked ";
    }
   echo '> <span>Paid</span></label><br>
    
    <p class="description">During your free trial period, choose Trial.</p><p class="description">Once you provide billing information for a paid plan, choose Paid.</p>
    </fieldset>
    ';
}

public function resrc_domain_pattern_callback() {
    $setting = esc_attr( get_option( 'resrc-domain-pattern' ) );
    echo "<input type='text' name='resrc-domain-pattern' value='$setting' />";
    echo "<p class='description'>Paste this in directly from your domain page (ex. *.mydomain.com or sub.mydomain.com)</p>";
    echo "<img class='resrc-screenshot' src='" . plugins_url( 'assets/img/resrc-domain.png', __FILE__ ) . "'>";
}

public function resrc_enable_resrc_callback() {
    $setting = esc_attr( get_option( 'resrc-enable-resrc' ) );
    echo '<fieldset><legend class="screen-reader-text"><span>Enable ReSRC</span></legend><label for="resrc-enable-resrc">
<input name="resrc-enable-resrc" type="checkbox" id="resrc-enable-resrc" value="1"';
    if ($setting == 1) {
        echo " checked ";
    }
    echo '/> Enable ReSRC</label></fieldset>';
}

public function resrc_options_callback() {
    echo "<p>ReSRC optimizes and delivers perfect images on any device. Link your images to our super simple service - we take care of everything.</p>";
    echo "<p>Make sure you <a href='http://www.resrc.it/signup'>register for a ReSRC account</a> before using this plugin.</p>";
}

public function resrc_enable_resrc_validate($input) {
    if ($input == 1) {
        $domain_pattern = esc_attr( get_option( 'resrc-domain-pattern' ) );
    
    $account_type = esc_attr( get_option( 'resrc-account-type' ) );
    if (!$domain_pattern) {
        add_settings_error( '', '', 'Please enter a domain pattern before enabling ReSRC.' );
        return;
    }
    if (!$account_type) {
        add_settings_error( '', '', 'Please select an account type before enabling ReSRC.' );
        return;
    }
}
    return $input;
}


}
