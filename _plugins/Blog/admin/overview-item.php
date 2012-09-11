<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );

$recent = rows( 'select id, blog_id, title, body, post_date from ' . DB_PREFIX . 'blog_posts order by post_date desc limit 5' ); 

if( count( $recent ) == 0 )
	$content = '<p><i>No recent posts</i></p>';
else{
	$content = '<table class="row-color">';
	foreach( $recent as $post ){	
        	$content .= '<tr><td><span>' . date( "F j,Y", strtotime( $post[ 'post_date' ] ) ) . '</span><a
		href="plugin.php?p_name=Blog&blog_id=' . $post[ 'blog_id' ] . '&post=' . $post[ 'id' ] . '"><h3>' . $post[ 'title' ] . '</h3></a>
		<p>' . strip_tags( substr( $post[ 'body' ], 0, 125 ) ) . ' [...]</p></td></tr>';
	}
	$content .= '</table>';
}

$Template->add( 'content', $content );

?>
