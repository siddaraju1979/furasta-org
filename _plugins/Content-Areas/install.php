<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 * @todo add new permissions so that all users are able to use this plugin
 */

switch( $version ){ 
	case 1:
		// add content areas table to database
		query( 'create table if not exists ' . PREFIX . 'content_areas ('
			. 'id int auto_increment primary key,'
			. 'name text,'
			. 'content text,'
			. 'data text'
		. ')' );

		// add widgets options table
		query( 'create table if not exists ' . PREFIX  . 'widget_options ('
			. 'id int auto_increment primary key,'
			. 'name text,'
			. 'value text,'
			. 'area text'
		. ')' );
	break;
}

?>
