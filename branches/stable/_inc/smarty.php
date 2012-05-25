<?php

/**
 * Smarty Configuration File, Furasta.Org
 *
 * Loads the Smarty Template Engine and asigns
 * values to all of the template variables.
 * Assigns functions, then plugin functions and
 * displays the correct template file.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

require HOME . '_inc/Smarty/libs/Smarty.class.php';

$Smarty = new Smarty( );

$smarty_dir = USER_FILES . 'smarty/';
$Smarty->template_dir = $smarty_dir . 'templates';
$Smarty->compile_dir = $smarty_dir . 'templates_c';

/**
 * set smarty delimiters to "[" and "]"
 */
$Smarty->left_delimiter = '[';
$Smarty->right_delimiter = ']';

/**
 * register plugin functions 
 */
$plugin_functions = $Plugins->frontendTemplateFunctions( );
foreach( $plugin_functions as $name => $function )
	$Smarty->register_function( $name, $function );

/**
 * filter $Page before output, in case plugins want to change anything
 */
$Page = $Plugins->filter( 'frontend', 'filter_page_vars', $Page );

/**
 * assign other page vars 
 */
$Smarty->assign( '__page_content', $Page[ 'content' ] );
$Smarty->assign( 'page_name', $Page[ 'name' ] );
$Smarty->assign( 'page_id', $Page[ 'id' ] );
$Smarty->assign( 'page_slug', $Page[ 'slug' ] );
$Smarty->assign( 'page_edited', $Page[ 'edited' ] );
$Smarty->assign( 'page_user', $Page[ 'user' ] );
$Smarty->assign( 'page_parent_id', $Page[ 'parent' ] );
$Smarty->assign( 'page_type', $Page[ 'type' ] );

// assign other vars
$Smarty->assign( 'logged_in', User::verify( ) ); 
$Smarty->assign( 'home', HOME );

/**
 * assign site vars
 */
$Smarty->assign( 'site_url', SITEURL );
$Smarty->assign( 'site_title', $SETTINGS[ 'site_title' ] );
$Smarty->assign( 'site_subtitle', $SETTINGS[ 'site_subtitle' ] );

/**
 * register default template functions 
 */
$Smarty->register_function( 'page_content', 'frontend_page_content' );
$Smarty->register_function( 'menu', 'frontend_menu' );
$Smarty->register_function( 'breadcrumbs', 'frontend_breadcrumbs' );
$Smarty->register_function( 'metadata', 'frontend_metadata' );
$Smarty->register_function( 'cache_css', 'frontend_css_load' );
$Smarty->register_function( 'cache_js', 'frontend_javascript_load' );
$Smarty->register_function( 'page_load_time', 'frontend_page_load_time' );

/**
 * find correct template to use
 * and make sure it exists
 */
$file = ( $Page[ 'template' ] == 'Default' ) ? 
		TEMPLATE_DIR . 'index.html' :
		TEMPLATE_DIR . $Page[ 'template' ] . '.html';

if( !file_exists( $file ) )
        error( 'Template files could not be found.<br/><i>' . $file . '</i>', 'Template Error' );

/**
 * check if preview is being done, allows for
 * templates to be previewed. only works for
 * logged in users
 */
if( isset( $_GET[ 'preview' ] ) && User::verify( ) ){
	$preview = $_GET[ 'preview' ];

	if( strpos( '..', $preview ) === false && is_dir( HOME . '_www/' . $preview ) )
		$file = HOME . '_www/' . $preview . '/index.html';
}

$Smarty->display( $file );
?>
