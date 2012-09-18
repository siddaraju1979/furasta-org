<?php

/**
 * Files Manager Ajax, Furasta.Org
 *
 * provides access to the file manager methods via ajax
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */

/**
 * make sure logged in and verified
 */
if( !defined( 'AJAX_LOADED' ) && !defined( 'AJAX_VERIFIED' ) )
        return;


/**
 * switch to desired function
 */
$func = addslashes( $_POST[ 'func' ] );
switch( $func ){
        case 'readDir':
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
        break;
}
exit;
?>
