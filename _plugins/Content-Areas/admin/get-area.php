<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 * @todo expand beyond just $widgets[ 'all' ]
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$name = addslashes( @$_POST[ 'name' ] );

if( $name == '' )
	exit;

$widgets = row( 'select content from ' . PREFIX . 'content_areas where name="' . $name . '"' );

$widgets = json_decode( $widgets[ 'content' ], true );

echo '<h2> ' . $name . '</h2>';
echo '<div class="content-area-widgets';

if( count( $widgets[ 'all' ] ) == 0 ){
	echo ' empty"><p><b>Drag widgets here!</b></p></div></div>';
	exit;
}

echo '">';

foreach( $widgets[ 'all' ] as $widget ){
	echo '<div class="widget" type="' . $widget . '"><span>' . $widget . '</span></div>';
}

exit;
?>
