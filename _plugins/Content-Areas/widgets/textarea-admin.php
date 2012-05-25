<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( $widget_id == 0 ){ // add new widget to database and return id
	$defaults = json_encode( array(

	) );
	query( 'insert into ' . PREFIX . 'widgets values( "", "textarea", "' . $area_name . '", "", "' . $defaults . '" )' );
	echo mysql_insert_id( );
	exit;
}

require HOME . '_inc/function/admin.php';
$Template = Template::getInstance( );

$widget = row( 'select * from ' . PREFIX . 'widgets where id=' . $widget_id );

$content = '<h2>Textarea</h2>';
$content .= tinymce( 'widget_textarea_content', @$widget[ 'content' ], 'Normal' );

$Template->add( 'content', $content );
?>
