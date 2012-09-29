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
        case 'uploadFile':
                /**
                 * get file from postdata
                 */
                $name = $_POST[ 'upload-file' ];
                $file = $_FILES[ $name ];

                /**
                 * pass to file manager
                 */
                $success = $FileManager->uploadFile( $file, $path );

                if( !$success )
                        echo json_encode( $FileManager->error( ) );
        break;
        default: // assume this is not an ajax request and print errors in html

                /**
                 * get type, make sure is correct
                 */
                $type = $FileManager->getType( $path );
                if( !$type )
                        return false;

                switch( $type ){
                        // image
                        case IMAGE:
                                /**
                                 * get width and height from get
                                 */
                                $width = empty( $_GET[ 'width' ] ) ? false : $_GET[ 'width' ];
                                $height = empty( $_GET[ 'height' ] ) ? false : $_GET[ 'height' ];

                                $FileManager->displayImage( $path, $width, $height );
                        break;
                        // text
                        case TEXT:
                                $contents = $FileManager->readFile( $path );

                                // change to switch by file type, display appropriatly
                                
                                echo $contents;
                        break;
                        // default - download file
                        default:
                                $FileManager->downloadFile( $path );
                }

}

exit;

?>
