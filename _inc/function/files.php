<?php

/**
 * File Functions, Furasta.Org
 *
 * a number of functions for dealing with files
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_template
 */

/**
 * display_image
 *
 * displays an image from a given path
 * has support for jpeg, png, gif
 * can resize an image if height is given,
 * will also cache resized images. only
 * outputs in png format
 *
 * @params string $path
 * @return bool
 */
function display_image( $path, $width = false ){
	$type = mime_content_type( $path );

	// create WBMP
	switch( $type ){
		case 'image/png':
			$image = imagecreatefrompng( $path );
		break;
		case 'image/jpeg':
			$image = imagecreatefromjpeg( $path );
		break;
		case 'image/gif':
			$image = imagecreatefromgif( $path );
		break;
		default:
			return false;
	}

	if( $width != false ) // resize WBMP
		$image = resize_image( $image, $width, false, true );

	// display WBMP as png
	header( 'Content-Type: image/png' );
	imagepng( $image );
	imagedestroy( $image );

	return true;
}

/**
 * resize_image
 *
 * resizes images to a given width and height. if
 * width is omitted height will be auto calculated
 * caching is also performed if cache is set to
 * true
 *
 * @params GDResource $image
 * @params int $width
 * @params int $height optional
 * @params bool $cache optional
 * @return GDResource
 * @todo add cropping support
 */
function resize_image( $image, $width, $height = false, $cache = false ){
	$cache_file = md5( 'FURASTA_FILES_IMAGES_CACHE_' . $image . $width . $height );
	if( cache_exists( $cache_file, 'IMAGES' ) )
		return cache_get_image( $cache_file, 'IMAGES' );

	$old_width = imagesx( $image );
	$old_height = imagesy( $image );

	if( $height == false ) // calculate height 
		list( $width, $height ) = resize_dimensions( $width, $width, $old_width, $old_height );

	// resize image
	$new_image = imagecreatetruecolor( $width, $height );
	imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height );

	if( $cache ) // cache the image
		cache_image( $cache_file, $new_image, 'IMAGES' );	

	return $new_image;
}

/**
 * resize_dimensions
 *
 * calculates the new dimensions of an image, given
 * target width, height and original width, height.
 *
 * @params int $width
 * @params int $height
 * @params int $old_width
 * @params int $old_height
 * @return array
 */
function resize_dimensions( $width, $height, $old_width, $old_height ){ 
    $return = array( $width, $height); 

    if ( $old_width / $old_height > $width / $height && $old_width > $width){
        $return[ 0 ] = $width; 
        $return[ 1 ] = $width / $old_width * $old_height; 
    }
    elseif( $old_height > $height ){ 
        $return[ 0 ] = $height / $old_height * $old_width; 
        $return[ 1 ] = $height; 
    } 

    return $return; 
}

/**
 * download_file
 *
 * sends a given file to the user
 *
 * @params string $file
 * @return void
 */
function download_file( $file ){

	$type = mime_content_type( $file );
	$name = basename( $file );	

	header( 'Content-type: ' . $type );
	header( 'Content-Disposition: attachment; filename="' . $name . '" ');

	echo file_get_contents( $file );

}

?>
