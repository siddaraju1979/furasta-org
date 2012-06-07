<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$template = @$_POST[ 'template' ];

if( strpos( '..', $template ) !== false )
	exit;

if( $template == 'default' )
	$template = 'index';

if( !file_exists( TEMPLATE_DIR . $template . '.html' ) )
	exit;
/**
 * process the template, search for all content areas
 * and return json encoded array of areas
 */
$contents = file_get_contents( TEMPLATE_DIR . $template . '.html' );

preg_match_all('/\[content_area name="?([^"}]*)"?\]/', $contents, $areas );

echo json_encode( $areas[ 1 ] );

exit;
?>
