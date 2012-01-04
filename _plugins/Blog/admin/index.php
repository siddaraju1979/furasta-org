<?php

/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->add( 'title', 'Blog Post' );
$Template->loadJavascript( '_inc/js/tiny_mce.js' );
$Template->loadJavascript( '_plugins/Blog/admin/admin.js' );
$Template->add( 'head', '<script type="text/javascript" src="' . SITEURL . '_inc/tiny_mce/tiny_mce.js"></script>' );
$Template->loadCss( '_plugins/Blog/admin/style.css' );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Blog/img/post.png"/></span>
<h1 class="image-left">' . $Template->e( 'blog_admin_post' ) . '</h1>
<br style="clear:both"/>
<form id="blog-form" method="post">
<table>
	<tr>
		<td style="width:8%">' . $Template->e( 'blog_admin_post_title' ) . ':</td>
		<td><input type="text" name="Subject"/></td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="Content" id="message-body" style="width:100%"></textarea>
		</td>
	</tr>
	<tr>
		<td class="small">' . $Template->e( 'blog_tags' ) . ':</td>
		<td><input type="text" id="tags-in"/><a class="input" id="tags-add">' . $Template->e( 'blog_add_tags' ) . '</a></td>
	</tr>
	<tr>
		<td colspan="2" id="tags-put"></td>
	<tr>
		<td colspan="2">
			<input name="blog-delete" class="submit right" type="submit" value="' . $Template->e( 'delete' ) . '"/>
			<input name="blog-submit" class="submit" type="submit" value="' . $Template->e( 'save' ) . '"/>
		</td>
	</tr>
</table>
</form>
';
$Template->add( 'content', $content );
?>
