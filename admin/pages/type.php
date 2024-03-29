<?php

/**
 * Page Type, Furasta.Org
 *
 * Accessed via AJAX, this page returns a page type which
 * is requested by the user, or suitable for this page. It
 * allows for page types to be created by plugins.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

require HOME . '_inc/function/admin.php';

/**
 * set up $_GET variables 
 */
$type = @$_GET[ 'type' ];
$id = (int) @$_GET[ 'id' ];
$normal = true;

if( $type != 'Normal' ){
        $normal = $Plugins->adminPageType( $type, $id );
}
if( $type == 'Normal' || !$normal ){
	if( $id != 0 )
		$content = stripslashes( single( 'select content from ' . DB_PAGES . ' where id= ' . $id, 'content' ) );

	$Template = Template::getInstance( );

	$javascript = '
	$(function(){
		tinymce_changeConfig( "#page-content", "Normal" );
	});';

	$Template->loadJavascript( 'FURASTA_ADMIN_DB_PAGES' . $id, $javascript );

	$content = '
	<textarea id="page-content" name="PageContent" class="tinymce" style="width:100%;display:block">
		' . @$content . '
	</textarea>';

	$Template->add( 'content', $content );

}
else{
}
?>
