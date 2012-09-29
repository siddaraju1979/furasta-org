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
function display_image( $path, $width = false, $height = false ){
	$type = mime_content_type( $path );

}


/**
 * crop_image
 *
 * crops an image to a given width and height. first
 * runs resize_image without height then crops to
 * the given height
 *
 * @param GDResource $image
 * @param int $width
 * @param int $height
 * @param bool $cache
 * @param string $path
 */
function crop_image( $image, $width, $height, $cache = false, $path = false ){

	$name = md5( 'CROP_' . $width . $height );
	if( $cache && cache_exists_image( $name, $path ) )
		return cache_get_image( $name, $path );

	$orig_width = imagesx( $image );
	$orig_height = imagesy( $image );
	$aspect = $orig_width / $orig_height;
	$desired_aspect = $width / $height;

	// resize dimensions
	if( $aspect >= $desired_aspect ){
		// If image is wider than thumbnail (in aspect ratio sense)
		$new_height = $height;
		$new_width = $orig_width / ( $orig_height / $height );
	}
	else {
		// If the thumbnail is wider than the image
		$new_width = $width;
		$new_height = $orig_height / ( $orig_width / $width );
	}

	// create new image
	$new_image = imagecreatetruecolor( $width, $height );

	imagecopyresampled(
		$new_image,
		$image,
		0 - ( $new_width - $width ) / 2, // center horizontally
		0 - ( $new_height - $height ) / 2, // center vertically
		0,
		0,
		$new_width,
		$new_height,
		$orig_width,
		$orig_height
	);

	if( $cache ) // cache the image
		cache_image( $name, $new_image, $path );	
	
	return $new_image;
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
 * @params string $path
 * @return GDResource
 */
function resize_image( $image, $width, $height = false, $cache = false, $path = false ){
	$name = 'RESIZE' . $width . $height;
	if( $cache && cache_exists_image( $name, $path ) )
		return cache_get_image( $name, $path );

	$old_width = imagesx( $image );
	$old_height = imagesy( $image );

	if( $height == false ) // calculate height 
		list( $width, $height ) = resize_dimensions( $width, $width, $old_width, $old_height );

	// resize image
	$new_image = imagecreatetruecolor( $width, $height );
	imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height );

	if( $cache ) // cache the image
		cache_image( $name, $new_image, $path );	

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

?>
