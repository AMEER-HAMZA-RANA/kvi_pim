<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$arrow_includes = array(
	// '/theme-setup/theme-settings.php',                  // Initialize theme default settings.
	// '/theme-setup/setup.php',                           // Theme setup and custom theme supports.
	// '/theme-setup/widgets.php',                         // Register widget area.
	// '/theme-setup/enqueue.php',                         // Enqueue scripts and styles.
	// '/theme-setup/template-tags.php',                   // Custom template tags for this theme.
	// '/theme-setup/pagination.php',                      // Custom pagination for this theme.
	// '/theme-setup/extras.php',                          // Custom functions that act independently of the theme templates.
	// '/theme-setup/customizer.php',                      // Customizer additions.
	// '/theme-setup/csf-theme-options.php',               // Codestart Framework Theme Options.
	// '/theme-setup/custom-comments.php',                 // Custom Comments file.
	// '/theme-setup/jetpack.php',                         // Load Jetpack compatibility file.
	// '/theme-setup/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/arrow/arrow/issues/567
	// '/categories/Categories.php',
	// '/state_manager/StateManager.php',
	'/settings/SettingsManager.php',
	'/notifications/NotificationsManager.php',
	// '/theme-setup/woocommerce.php',                     // Load WooCommerce functions.
	// '/theme-setup/editor.php',                          // Load Editor functions.
	// '/theme-setup/wp-admin.php',                        // /wp-admin/ related functions
	// '/theme-setup/deprecated.php',                      // Load deprecated functions.
	// '/utilities/FormBuilder.php',                      // Form Builder Utility Function.
	'/functions/redirects.php',
	//API
	// '/api/schemas/errorCodes.php',
	// '/api/ProductsAPIController.php',
	// '/api/PricebooksAPIController.php',
	// '/api/PriceCodesAPIController.php',
	// '/api/PricebookCodePricingAPIController.php',
	// '/api/ContractsAPIController.php',
	// '/api/ContractPricingAPIController.php',
	// '/api/TerritoriesAPIController.php',
	// '/api/SalesRepsAPIController.php',
	// '/api/RetailersAPIController.php',
	// '/api/RetailerSKUAPIController.php',
);

foreach ( $arrow_includes as $file ) {
	$filepath = locate_template( 'inc' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}

add_action( 'wp', 'load_sm' );
function load_sm() {
	$stm = SettingsManager::GI();
}
add_action( 'admin_init', 'load_sm' );


add_action( 'user_register', "send_admin_notification_for_user_register",10,1);
function send_admin_notification_for_user_register($user_id){
	$user = get_user_by( 'id', $user_id);
	update_user_meta($user_id,"kvi",1);
	$subject = "New User Registered on KVI-PIM";
	$message = "Hi Renee, <br><br> A new user has registered on KVI-PIM through SSO. Kindly confirm their role. <br>The user id is: ".$user_id;
	$message .= "<br>Submitted by $user->user_email";
	$message .= "<br>Link to edit: https://www.kvipim.com/wp-admin/user-edit.php?user_id=$user_id";
	$to = "reneesansevere@kvipim.com";
	$headers[] = 'Cc: AltProd <development@altprod.com>';
	wp_mail( $to, $subject, $message, $headers );
}



/* Disable WordPress Admin Bar for all users */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );