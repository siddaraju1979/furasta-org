<?php

/**
 * Files Read Dir Ajax, Furasta.Org
 *
 * lists the directory supplied by the ajax post
 * request
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */

if( !defined( 'AJAX_LOADED' ) && !defined( 'AJAX_VERIFIED' ) )
        return;

/**
 * get path from post data
 */
$path = addslashes( $_POST[ 'path' ] );

/**
 * read dir with file manager
 */
$FileManager = FileManager::getInstance( );
$files = $FileManager->readDir( $path );

/**
 * print results or error
 */
if( $files )
        echo json_encode( $files );
else
        echo json_encode( $FileManager->error( ) );

exit;
?>
