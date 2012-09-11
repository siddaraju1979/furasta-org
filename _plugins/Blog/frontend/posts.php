<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$options = options( 'page_' . $id  );
$limit = ( @$options[ 'blog_items_per_page' ] == 0 ) ? 5 : $options[ 'blog_items_per_page' ];

/**
 * if date isset then filter posts by date
 */
if( $date ){
	// determine range of date - ex between 2000/01/01-2001/01/01 or 2000/03/01-2000/04/01 or 2000/05/05-2000/05/06
	switch( count( $date ) ){
		case 1:
			$max = $date[ 0 ] + 1;
			$min = $date[ 0 ];
		break;
		case 2:
			$min = $date[ 0 ] . '-' . $date[ 1 ] . '-01';
			$time = strtotime( $min );
			$newtime = strtotime( '+1 month', $time );
			$max = date( 'Y-m-d', $newtime );
		break;
		case 3:
			$min = $date[ 0 ] . '-' . $date[ 1 ] . '-' . $date[ 2 ];
			$time = strtotime( $min );
			$mintime = strtotime( '-1 day', $time );
			$maxtime = strtotime( '+1 day', $time );
			$min = date( 'Y-m-d', $mintime );
			$max = date( 'Y-m-d', $maxtime );
		break;
	}

	$posts = rows( 'select * from ' . DB_PREFIX . 'blog_posts where post_date > "' . $min . '" and post_date < "' . $max . '" order by post_date asc limit ' . $limit );
}
else // else display most recent posts
	$posts = rows( 'select * from ' . DB_PREFIX . 'blog_posts where blog_id=' . addslashes( $id ) . ' order by post_date asc limit ' . $limit );

echo '<div id="blog-content">';

if( count( $posts ) == 0 ){
	echo '<p><b>' . $Template->e( 'blog_no_posts' ) . '</b></p>';
}
else{
	$b_name = single( 'select slug from ' . DB_PAGES . ' where id=' . $id, 'slug' );
	foreach( $posts as $post ){
		$date = date( '/Y/m/d/', strtotime( $post[ 'post_date' ] ) );
		$name = str_replace( ' ', '-', $post[ 'title' ] );
		$link = '<a href="' . SITE_URL . $b_name . $date . $name . '">';
		$content = ( @$options[ 'blog_full_summary' ] == 'full' ) ? $post[ 'body' ] : substr( $post[ 'body' ], 0, 400 ) . ' [..]';
		echo '<div class="blog-post" id="blog_post_' . $post[ 'id' ] . '">';
		echo '<div class="blog-post-title">
			' . $link . '<h2>' . $post[ 'title' ] . '</h2></a>
		</div>';
		echo '<div class="blog-post-content">' . $content . '</div>';
		echo '<div class="blog-post-footer">';
		if( @$options[ 'blog_full_summary' ] == 'summary' ) 
			echo $link . $Template->e( 'blog_frontend_read_full' ) . '</a>';
		echo '</div></div>';	
	}
}


echo '</div>';

?>
