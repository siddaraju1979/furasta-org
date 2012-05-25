<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->add( 'title', 'Blog post' );
$Template->loadJavascript( '_plugins/Blog/admin/admin.js' );
$Template->loadCss( '_plugins/Blog/admin/style.css' );

$blog_id = (int) @$_GET[ 'blog_id' ];
$id = (int) @$_GET[ 'post' ];

// validate blog form with these conds
$conds = array(
	'Subject' => array(
		'name' => $Template->e( 'blog_admin_post_title' ),
		'required' => true,
		'pattern' => array(
			'^[A-Za-z0-9 ]{2,40}$',
			$Template->e( 'blog_admin_pattern' )
		)
	),
	'Content' => array(
		'name' => $Template->e( 'blog_body' ),
		'required' => true
	)
);

$valid = validate( $conds, '#blog-form', 'blog-submit' );

/**
 * handle post stuff if valid
 */
if( isset( $_POST[ 'blog-submit' ] ) && $valid ){
	$title = addslashes( $_POST[ 'Subject' ] );
	$body = addslashes( $_POST[ 'Content' ] );
	$tags = addslashes( $_POST[ 'tags-hidden' ] );
	$blog = addslashes( $_POST[ 'blog_id' ] );
	$User = User::getInstance( );
	$user = $User->about( 'id' );
	$date = date( 'Y-m-d' );

	/**
 	 * if no blog is set throw error, keep content
	 */
	if( $blog == '---' ){
		// store content in $post array to emulate real post info
		$post[ 'title' ] = $title;
		$post[ 'body' ] = $body;
		$post[ 'tags' ] = $tags;
		
		$Template->runtimeError( $Template->e( 'blog_admin_error_no_blog' ) );
	}
	elseif( $id == 0 ){ // create new page

		query( 'insert into ' . PREFIX . 'blog_posts values'
			. '( "", ' . $blog . ', "' . $title . '", "' . $body . '", "' . $date . '", ' . $user . ',"' . $tags . '")'
		);

		$id = mysql_insert_id( );
		htaccess_rewrite( );
		header( 'location: plugin.php?p_name=Blog&blog_id=' . $blog . '&post=' . $id . '&error=13' );

	}
	else{
		query( 'update ' . PREFIX . 'blog_posts set title="' . $title . '", body="' . $body . '", tags="' . $tags . '" where id=' . $id );
		htaccess_rewrite( );
		$Template->runtimeError( '13' );
	}
}

if( $id != 0 ){
	$post = row( 'select * from ' . PREFIX . 'blog_posts where id=' . $id ); 
	if( $blog_id == 0 )
		$blog_id = $post[ 'blog_id' ];
}

$blogs = rows( 'select id, name from ' . PAGES . ' where type="Blog"' );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Blog/img/post.png"/></span>
<h1 class="image-left">' . $Template->e( 'blog_admin_post' ) . '</h1>
<br style="clear:both"/>
<form id="blog-form" method="post">
<table>
	<tr>
		<td style="width:8%">' . $Template->e( 'blog_admin_post_title' ) . ':</td>
		<td><input type="text" name="Subject" value="' . @$post[ 'title' ] . '"/></td>
		<td style="width:10%">' . $Template->e( 'blog_admin_blog_page' ) . ': <a class="help link" id="help-page">&nbsp;</a></td>
		<td><select name="blog_id">';

		if( count( $blogs ) == 0 ){
			$content .= '<option>---</option>';
		}
		else{
			foreach( $blogs as $blog ){
				$content .= '<option value="' . $blog[ 'id' ] . '"';
				if( $blog[ 'id' ] == $blog_id )
					$content .= ' selected="selected"';
				$content .= '>' . $blog[ 'name' ] . '</option>';
			}
		}

$content .= '
		</select></td>
	</tr>
	<tr>
		<td colspan="4">
			' . tinymce( 'Content', @$post[ 'body' ] ) . '
		</td>
	</tr>
	<tr>
		<td class="small">' . $Template->e( 'blog_tags' ) . ': <a class="link help" id="help-tags">&nbsp;</a></td>
		<td colspan="3"><input type="text" id="tags-in"/><a class="input" id="tags-add">' . $Template->e( 'blog_add_tags' ) . '</a></td>
	</tr>
	<tr>
		<input name="tags-hidden" value="' . @$post[ 'tags' ] . '" type="hidden"/>
		<td colspan="4" id="tags-put">';

		if( isset( $post[ 'tags' ] ) ){
			$tags = $post[ 'tags' ];
			if( $tags != '' ){
				if( strpos( ',', $tags ) !== -1 )
					$tags = explode( ',', $tags );
				else
					$tags = array( $tags );
				foreach( $tags as $tag ){
					$content .= '<span class="tag">';
					$content .= '<span>' . $tag . ' </span>';
					$content .= '<a class="link delete-small-img blog-delete">&nbsp;</a>';
					$content .= '</span>';
				}
			}
		}

$content .= '
		</td>
	<tr>
		<td colspan="4">';
			if( isset( $post[ 'id' ] ) )
				$content .= '<input name="blog-delete" id="' . $id . '" blog_id="' . $blog_id . '" class="submit right" type="submit" value="' . $Template->e( 'delete' ) . '"/>';
$content .= '
			<input name="blog-submit" class="submit" type="submit" value="' . $Template->e( 'save' ) . '"/>
		</td>
	</tr>
</table>
</form>
';
$Template->add( 'content', $content );
?>
