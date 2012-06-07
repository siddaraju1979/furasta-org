<?php

/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

switch( $version ){
	case 0:
		// add forums table
		query( 'create table if not exists ' . PREFIX . 'forums ( '
			. 'id int auto_increment primary key,'
			. 'page_id int,'
			. 'name text,'
			. 'description text,'
			. 'perm text'
		. ')' );

		// add topics table
		query( 'create table if not exists ' . PREFIX . 'forum_topics ('
			. 'id int auto_increment primary key,'
			. 'forum_id int,'
			. 'name text,'
			. 'body text,'
			. 'user_id int,'
			. 'post_date date,'
			. 'sticky text'
		. ')' );

		// add forum posts table
		query( 'create table if not exists ' . PREFIX . 'forum_posts ('
			. 'id int auto_increment primary key,'
			. 'forum_id int,'
			. 'topic_id int,'
			. 'user_id int,'
			. 'body text,'
			. 'post_date date'
		. ')' );
	break;
}

?>
