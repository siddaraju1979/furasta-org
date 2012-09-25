<?php

/**
 * User Files Access, Furasta.Org
 *
 * This file is loaded whenever a file contained in the users files
 * directory is accessed. It loads the file, even if the user
 * dir is placed outside the web root.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */


/**
 * load default libs
 */
require '_inc/define.php';
require HOME . '_inc/function/files.php';


/**
 * get path and create instance of
 * file manager
 */
$path = '/' . addslashes( $_GET[ 'path' ] );
$func = addslashes( @$_POST[ 'func' ] );
$FileManager = FileManager::getInstance( );

/**
 * switch between file manager functions
 */
switch( $func ){
        case 'readDir':

                $files = $FileManager->readDir( $path );

                /**
                 * print results or error
                 */
                if( $files )
                        echo json_encode( $files );
                else
                        echo json_encode( $FileManager->error( ) );
        
                break;
        case 'addDir':

                $success = $FileManager->addDir( $path );

                if( !$success )
                        echo json_encode( $FileManager->error( ) );

        break;
        default: // assume this is a user access, so print errors not in json
                 
                $contents = $FileManager->readFile( $path );

                // change to switch by file type, display appropriatly
                
                echo $contents;
                        
}

/**
 * determine how to load file
 *
if( @getimagesize( $path ) ){ // image
	$width = ( empty( $_GET[ 'width' ] ) ) ? false : $_GET[ 'width' ];
	$height = ( empty( $_GET[ 'height' ] ) ) ? false : $_GET[ 'height' ];
	display_image( $path, $width, $height );
}
else // download file
	download_file( $path );
 */

exit;

?>
