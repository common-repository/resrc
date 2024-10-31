<?php
/**
 * ReSRC_For_WordPress
 *
 * @package   ReSRC_For_WordPress
 * @author    ReSRC <team@resrc.it>
 * @license   GPL-2.0+
 * @link      http://www.resrc.it/wordpress
 * @copyright 2014 ReSRC
 */

/**
 * ReSRC_For_WordPress class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-resrc-admin.php`
 *
 * @package ReSRC_For_WordPress
 * @author  ReSRC <team@resrc.it>
 */
class ReSRC_For_WordPress {
	
	/**
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'resrc';

	/**
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		if (get_option( 'resrc-enable-resrc' ) && get_option( 'resrc-enable-resrc' ) == 1) {
			add_filter( 'the_content', array( $this, 'resrc_the_content_filter' ));
			add_filter( 'post_thumbnail_html', array( $this, 'resrc_the_content_filter' ));
		}

		add_action('wp_head', array ( $this, 'add_resrc_js'),99);


	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0'
		AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . 'languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );

	}

	/**
	 * Add the ReSRC JavaScript
	 * @since    1.0.0
	 */
	public function add_resrc_js() {
		
		if (get_option ( 'resrc-account-type' ) == 'trial') {
			echo '<script>resrc = {options: {server: "trial.resrc.it"}};</script>';
		}

		echo <<<STOP
			<script>
			(function () {
			  var d = false;
			  var r = document.createElement("script");
			  r.src = "//use.resrc.it/0.7";
			  r.type = "text/javascript";
			  r.async = "true";
			  r.onload = r.onreadystatechange = function () {
			    var rs = this.readyState;
			    if (d || rs && rs != "complete" && rs != "loaded") return;
			    d = true;
			    try {
			      resrc.ready(function () {
			        resrc.resrc();
			      });
			    } catch (e) {}
			  };
			  var s = document.getElementsByTagName("script")[0];
			  s.parentNode.insertBefore(r, s);
			})();
			</script>
STOP;
}

	/**
	 * @since    1.0.0
	 */

	public function resrc_the_content_filter($content) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/smart-dom-document.php' );
		# Get domain pattern into a regex-friendly form
		$domain_pattern = get_option( 'resrc-domain-pattern' );
		$domain_pattern = str_replace("*.", "|RESRC-ALL-PLACEHOLDER|", $domain_pattern);
		$domain_pattern = str_replace(".", "\.", $domain_pattern);
		$domain_pattern = str_replace("|RESRC-ALL-PLACEHOLDER|", "(.+\.)?", $domain_pattern);

		$doc = new SmartDOMDocument();
		$doc->LoadHTML($content);
		$images = $doc->getElementsByTagName('img');

		# If the domain pattern exists and images are present, find images that match the pattern and apply ReSRC's Preferred Method to them.
		if (!empty($domain_pattern) && count($images) > 0) {
			
			foreach ($images as $image) {
				$class = $image->getAttribute("class");

				# Prevent an endless loop by skipping images that already have the resrc class
				if (strpos($class, "resrc") == false) {
					$src = $image->getAttribute("src");
					$src_parsed = parse_url($src);
					$src_host = $src_parsed['host'];
					if (preg_match('/^' . $domain_pattern . '$/', $src_host)) {

					# Add a data-src attribute to the image, with the same value as the existing src attribute
					if (get_option ( 'resrc-account-type' ) == 'trial') {
						$image->setAttribute('data-src', "http://trial.resrc.it/" . $src);
					} else {
						$image->setAttribute('data-src', "http://app.resrc.it/" . $src);
					}
					
					# Remove the src attribute from the image
					$image->removeAttribute('src');
					
					# Add the resrc class to the image
					$image->setAttribute('class', trim($class . ' resrc'));
					
					# Add a noscript tag before the image, and include an img tag inside of it
					# TODO: bring over other attributes to the noscript img tag (ex. alt)
					$noscript = $doc->createElement('noscript');
					$noscriptnode = $image->parentNode->insertBefore($noscript, $image);
					$img = $doc->createElement('img');
					$newimg = $noscriptnode->appendChild($img);
					$newimg->setAttribute('src', $src);
				}
				}
			}

			# Return the modified post or page content, or the original content.

			return $doc->saveHTMLExact();
		} else {
			return $content;
		}
		
	}

}