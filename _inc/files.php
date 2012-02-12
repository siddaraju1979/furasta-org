<?php

/**
 * User Files Access, Furasta.Org
 *
 * This file is loaded whenever a file contained in the _user
 * directory is accessed. It loads the file, even if the _user
 * dir is placed outside the web root.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @todo update this file
 */

require '../_inc/define.php';
require HOME . '_inc/function/files.php';

$name=@$_GET['name'];

// make sure name is valid
if($name==''||strpos( $name, '..' ) !== false )
	error( 'The file request is invalid.','Invalid Filename', false );

$name = explode( '/', $name );
$section = ( @$name[ 0 ] == 'users' ) ? 'users' : 'groups'; // users or groups
$id = ( int ) @$name[ 1 ]; // user/group id
$status = ( @$name[ 2 ] == 'public' ) ? 'public' : 'private'; // public or private
$filename = @$name[ 3 ];

// if filename or id is 0 then
if( empty( $filename ) || $id == 0 )
	error( 'The file request is invalid.','Invalid Filename', false );

// if private file, check users are logged in
if( $status == 'private' && !User::verify( ) )
	error( 'You do not have sufficient privellages to view this item.', 'Permissions Error', false );

$path = USER_FILES . 'files/' . $section . '/' . $id . '/' . $status . '/' . $filename;
if( !file_exists( $path ) )
	error( 'The requested file does not exist.', '404 - File Not Found', false );

/**
 * determine how to load file
 */
if( @getimagesize( $path ) ){ // image
	$width = ( empty( $_GET[ 'width' ] ) ) ? false : $_GET[ 'width' ];
	display_image( $path, $width );
}
else // download file
	download_file( $path );

exit;

?>
