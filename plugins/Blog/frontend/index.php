<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$qstring = @$_GET[ 'blog_post' ];

switch( $qstring ){

	case ( strpos( $qstring, '/' ) !== -1 ):
		$qstring = explode( '/', rtrim( $qstring, '/' ) );

		// assume its a post title for now
		$name = addslashes( str_replace( '-', ' ', $qstring[ ( count( $qstring ) - 1 ) ] ) );

		$post_id = single( 'select id from ' . PREFIX . 'blog_posts where title="' . $name . '"', 'id' );
		if( $post_id )
			require HOME . '_plugins/Blog/frontend/post.php';
		else{

			// get date from qstring if present
			$date = array( );
			for( $i = 0; $i < 3; ++$i ){
				if( isset( $qstring[ $i ] ) )
					array_push( $date, $qstring[ $i ] );
			}

			if( count( $date ) == 0 )
				$date = false;

			require HOME . '_plugins/Blog/frontend/posts.php';
		}
	break;
	default:
		$date = false;
		require HOME . '_plugins/Blog/frontend/posts.php';

}

?>
