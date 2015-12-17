<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Whimsy_Docs {

	/**
	 * The single instance of Whimsy_Docs.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.1' ) {
		$this->_version = $version;
		$this->_token = 'whimsy_docs';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );
        
		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
        
		$this->whimsy_docs_init();
		add_action( 'init', array( $this, 'whimsy_docs_init' ), 0 );
        
		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Load shortcode partials.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function whimsy_docs_init () {
		if ( ! function_exists('whimsy_docs_post_type') ) {

        // Register Custom Post Type
        function whimsy_docs_post_type() {

            $labels = array(
                'name'                  => _x( 'Docs', 'Post Type General Name', 'whimsy-docs' ),
                'singular_name'         => _x( 'Doc', 'Post Type Singular Name', 'whimsy-docs' ),
                'menu_name'             => __( 'Docs', 'whimsy-docs' ),
                'name_admin_bar'        => __( 'Doc', 'whimsy-docs' ),
                'parent_item_colon'     => __( 'Parent Item:', 'whimsy-docs' ),
                'all_items'             => __( 'All Items', 'whimsy-docs' ),
                'add_new_item'          => __( 'Add New Doc', 'whimsy-docs' ),
                'add_new'               => __( 'Add New', 'whimsy-docs' ),
                'new_item'              => __( 'New Item', 'whimsy-docs' ),
                'edit_item'             => __( 'Edit Item', 'whimsy-docs' ),
                'update_item'           => __( 'Update Doc', 'whimsy-docs' ),
                'view_item'             => __( 'View Doc', 'whimsy-docs' ),
                'search_items'          => __( 'Search Docs', 'whimsy-docs' ),
                'not_found'             => __( 'Not found', 'whimsy-docs' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'whimsy-docs' ),
                'items_list'            => __( 'Items list', 'whimsy-docs' ),
                'items_list_navigation' => __( 'Items list navigation', 'whimsy-docs' ),
                'filter_items_list'     => __( 'Filter items list', 'whimsy-docs' ),
            );
            $rewrite = array(
                'slug'                  => 'doc',
                'with_front'            => true,
                'pages'                 => true,
                'feeds'                 => true,
            );
            $args = array(
                'label'                 => __( 'Doc', 'whimsy-docs' ),
                'description'           => __( 'Documentation for The Fanciful', 'whimsy-docs' ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes', ),
                'taxonomies'            => array( 'whimsy_docs_category', 'post_tag' ),
                'hierarchical'          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'menu_icon'             => 'dashicons-sos',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => 'doc',
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'rewrite'               => $rewrite,
                'capability_type'       => 'page',
            );
            register_post_type( 'whimsy_doc', $args );

        }
        add_action( 'init', 'whimsy_docs_post_type', 0 );

        // Add custom doc taxonomy
        if ( ! function_exists( 'whimsy_docs_taxonomy' ) ) {

        // Register Custom Taxonomy
        function whimsy_docs_taxonomy() {

            $labels = array(
                'name'                       => _x( 'Doc Categories', 'Taxonomy General Name', 'whimsy-docs' ),
                'singular_name'              => _x( 'Doc Category', 'Taxonomy Singular Name', 'whimsy-docs' ),
                'menu_name'                  => __( 'Category', 'whimsy-docs' ),
                'all_items'                  => __( 'All Items', 'whimsy-docs' ),
                'parent_item'                => __( 'Parent Item', 'whimsy-docs' ),
                'parent_item_colon'          => __( 'Parent Item:', 'whimsy-docs' ),
                'new_item_name'              => __( 'New Category Name', 'whimsy-docs' ),
                'add_new_item'               => __( 'Add Item', 'whimsy-docs' ),
                'edit_item'                  => __( 'Edit Item', 'whimsy-docs' ),
                'update_item'                => __( 'Update Item', 'whimsy-docs' ),
                'view_item'                  => __( 'View Item', 'whimsy-docs' ),
                'separate_items_with_commas' => __( 'Separate items with commas', 'whimsy-docs' ),
                'add_or_remove_items'        => __( 'Add or remove items', 'whimsy-docs' ),
                'choose_from_most_used'      => __( 'Choose from the most used', 'whimsy-docs' ),
                'popular_items'              => __( 'Popular Items', 'whimsy-docs' ),
                'search_items'               => __( 'Search Items', 'whimsy-docs' ),
                'not_found'                  => __( 'Not Found', 'whimsy-docs' ),
                'items_list'                 => __( 'Items list', 'whimsy-docs' ),
                'items_list_navigation'      => __( 'Items list navigation', 'whimsy-docs' ),
            );
            $rewrite = array(
                'slug'                       => 'docs',
                'with_front'                 => true,
                'hierarchical'               => true,
            );
            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => true,
                'public'                     => true,
                'show_ui'                    => true,
                'show_admin_column'          => true,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => true,
                'rewrite'                    => $rewrite,
            );
            register_taxonomy( 'whimsy_docs_category', array( 'whimsy_doc' ), $args );

        }
        add_action( 'init', 'whimsy_docs_taxonomy', 0 );

        }

}
	} // End load_whimsy_docs_post_type ()

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
        
		if ( is_post_type_archive( 'whimsy-docs' ) ) {
		
            wp_register_style( $this->_token . '-docs', esc_url( $this->assets_url ) . 'css/docs.css', array(), $this->_version );
            wp_enqueue_style( $this->_token . '-docs' );
        }
        
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		if ( is_post_type_archive( 'whimsy-docs' ) ) {
            wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
            wp_enqueue_script( $this->_token . '-frontend' );
        }
	} // End enqueue_scripts ()
	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'whimsy-docs', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'whimsy-docs';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Whimsy_Docs Instance
	 *
	 * Ensures only one instance of Whimsy_Docs is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Whimsy_Docs()
	 * @return Main Whimsy_Docs instance
	 */
	public static function instance ( $file = '', $version = '1.0.1' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}