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
$post = row( 'select * from ' . PREFIX . 'blog_posts where id=' . $post_id );

echo '<div id="blog-content">';

$b_name = single( 'select slug from ' . PAGES . ' where id=' . $id, 'slug' );
$time = strtotime( $post[ 'post_date' ] );
$date = date( '/Y/m/d/', $time );
$show_date = date( 'F j, Y', $time );
$name = str_replace( ' ', '-', $post[ 'title' ] );
$link = '<a href="' . SITEURL . $b_name . $date . $name . '">';
$content = ( @$options[ 'blog_full_summary' ] == 'full' ) ? $post[ 'body' ] : substr( $post[ 'body' ], 0, 400 ) . ' [..]';
echo '<div class="blog-post" id="blog_post_' . $post[ 'id' ] . '">';
echo '<div class="blog-post-title">
	' . $link . '<h1>' . $post[ 'title' ] . '</h1></a>
	<p class="post_date">' . $show_date . '</p>
</div>';
echo '<div class="blog-post-content">' . $content . '</div>';
echo '<div class="blog-post-footer">';
echo '<p id="post_tags">Tags: ' . $post[ 'tags' ] . '</p>';
echo '</div></div>';

echo '</div>';

?>
