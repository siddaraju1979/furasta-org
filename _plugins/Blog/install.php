<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */


switch( $version ){
	case 1:
		// add blog_posts table to database
		query( 'create table if not exists ' . DB_PREFIX . 'blog_posts ('
			. 'id int auto_increment primary key,'
			. 'blog_id int,'
			. 'title text,'
			. 'body text,'
			. 'post_date date,'
			. 'user int,'
			. 'tags text'
		. ')' );
	break;
}

?>
