<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->loadJavascript( '_plugins/Blog/admin/page-type.js' );

$Page = row( 'select * from ' . PAGES . ' where id=' . $id );

$options = options( 'page_' . $id );
$title = ( empty( $options[ 'blog_title' ] ) ) ? $Page[ 'name' ] : $options[ 'blog_title' ];
$items = ( empty( $options[ 'blog_items_per_page' ] ) ) ? 5 : $options[ 'blog_items_per_page' ];
$summary = ( empty( $options[ 'blog_full_summary' ] ) ) ? 'summary' : $options[ 'blog_full_summary' ];

$Posts = rows( 'select * from ' . PREFIX . 'blog_posts where blog_id=' . $Page[ 'id' ] );

$content = '
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">' . $Template->e( 'blog_admin_blog_posts' ) . '</a></li>
		<li><a href="#tabs-2">' . $Template->e( 'blog_admin_blog_options' ) . '</a></li>
	</ul>
	<div id="tabs-1">
		<div class="tabs-content">		
			<a class="right" href="plugin.php?p_name=Blog&blog_id=' . $Page[ 'id' ] . '">
				<span class="header-img"><img src="' . SITEURL . '_plugins/Blog/img/post.png"/></span>
				<h1 class="image-left">' . $Template->e( 'blog_admin_add_post' ) . '</h1>
			</a>
			<br style="clear:both"/>
			<table>
				<tr>
					<th>' . $Template->e( 'title' ) . '</th>
					<th>' . $Template->e( 'date' ) . '</th>
					<th>' . $Template->e( 'user' ) . '</th>
					<th>' . $Template->e( 'delete' ) . '</th>
				</tr>';

				if( count( $Posts ) == 0 ){
					$content .= '<tr>
						<td colspan="4" id="empty"><p><i>No blog posts yet!</i></p></td>
					</tr>';
				}
				else{
					foreach( $Posts as $post ){
						$link = '<a href="plugin.php?p_name=Blog&blog_id=' . $Page[ 'id' ] . '" class="list-link">';
						$user = single( 'select name from ' . USERS . ' where id=' . $post[ 'user' ], 'name' );
						$content .= '<tr>
							<td>' . $link . $post[ 'title' ] . '</a></td>
							<td>' . $link . date( "d/m/y", strtotime( $post[ 'post_date' ] ) ) . '</a></td>
							<td>' . $link . $user . '</a></td>
							<td>
								<a class="link delete" id="' . $post[ 'id' ] . '">
									<span id="delete-img" class="admin-menu-img">&nbsp;</span>
								</a>
							</td>
						</tr>';
					}
				}
	$content .= '
				<tr>
					<th colspan="4">&nbsp;</th>
				</tr>
			</table>
		</div>
	</div>
	<div id="tabs-2">
		<table class="tabs-content">
			<tr>
				<td>' . $Template->e( 'blog_title' ) . ': <a class="link help" id="help-blog-title">&nbsp;</a></td>
				<td><input type="text" name="options[blog_title]" value="' . $title . '"/></td>
			</tr>
			<tr>
				<td>' . $Template->e( 'blog_items_per_page' ) . ':</td>
				<td><input type="text" name="options[blog_items_per_page]" value="' . $items . '"/></td>
			</tr>
			<tr>
				<td>' . $Template->e( 'blog_full_summary' ) . ': </td>
				<td><select name="options[blog_full_summary]">';

			$opts = array( 'full' => $Template->e( 'blog_full_items' ), 'summary' => $Template->e( 'blog_summary_items' ) );
			foreach( $opts as $opt => $val ){
				$content .= '<option value="' . $opt . '"';
				if( $opt == $summary )
					$content .= ' selected="selected"';
				$content .= '>' . $val . '</option>';
			}

			$content .= '
				</select</td>
			</tr>
		</table>
	</div>
</div>
';

$Template->add( 'content', $content );
?>
