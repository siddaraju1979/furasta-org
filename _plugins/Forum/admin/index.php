<?php

/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->loadJavascript( '_plugins/Forum/admin/admin.js' );

$users = single( 
	'select count(id) from ' . USERS . ' where user_group=( select id from ' . GROUPS . ' where name="Forum Users" )',
	'count(id)'
);
$moderators = single( 
	'select count(id) from ' . USERS . ' where user_group=( select id from ' . GROUPS . ' where name="Forum Moderators" )',
	'count(id)'
);
$forums = single(
	'select count(id) from ' . PREFIX . 'forums',
	'count(id)'
);

$content = '
<div id="new-forum-dialog" style="display:none" title="Add Forum">
	<form id="new-forum-form">
		<table>
			<tr>
				<td colspan="2" id="form-error"></td>
			</tr>
			<tr>
				<td>' . $Template->e( 'name' ) . ':</td>
				<td><input type="text" name="forum-name"/></td>
			</tr>
			<tr>
				<td>' . $Template->e( 'forum_description' ) . ':</td>
				<td><textarea name="forum-desc"></textarea></td>
			</tr>
		</table>
	</form>
</div>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">' . $Template->e( 'forum_overview' ) . '</a></li>
		<li><a href="#tabs-2">' . $Template->e( 'forum_forums' ) . '</a></li>
		<li><a href="#tabs-3">' . $Template->e( 'forum_options' ) . '</a></li>
	</ul>
	<div id="tabs-1">
		<table class="tabs-content row-color" style="border:1px solid #bbb;width:25%">
			<tr>
				<td>' . $Template->e( 'users' ) . ':</td>
				<td>' . $users . '</td>
			</tr>
			<tr>
				<td>' . $Template->e( 'forum_moderators' ) . ':</td>
				<td>' . $moderators . '</td>
			</tr>
			<tr>
				<td>' . $Template->e( 'forum_forums' ) . ':</td>
				<td>' . $forums . '</td>
			</tr>
		</table>
	</div>
	<div id="tabs-2">
		<div class="tabs-content">
			<a class="link right add-forum">
				<span id="header-Add" class="header-img">&nbsp;</span>
				<h1 class="image-left">Add Forum</h1>
			</a>
			<br style="clear:both"/>
			<table class="row-color" id="forums">
				<tr class="th">
					<th>' . $Template->e( 'name' ) . '</th>
					<th>' . $Template->e( 'forum_description' ) . '</th>
					<th>' . $Template->e( 'forum_posts_count' ) . '</th>
					<th>' . $Template->e( 'delete' ) . '</th>
				</tr>
	';

$forums = rows( 'select id, name, description from ' . PREFIX . 'forums' );
if( count( $forums ) == 0 )
	$content .= '<tr class="empty-table"><td colspan="4">No forums yet! <a class="link add-forum">Add one</a>.</td></tr>';
foreach( $forums as $forum ){
	$posts = single( 'select count(id) from ' . PREFIX . 'forum_posts where id=' . $forum[ 'id' ], 'count(id)' );
	$content .= '<tr>
		<td>' . $forum[ 'name' ] . '</td>
		<td>' . $forum[ 'description' ] . '</td>
		<td>' . $posts . '</td>
		<td><a class="link delete" id="' . $forum[ 'id' ] . '"><span id="delete-img" class="admin-menu-img">&nbsp;</span></a></td>
	</tr>';
}

$content .= '
				<tr class="th">
					<td colspan="4">&nbsp;</td>
				</tr>
			</table>
		</div>
	</div>
	<div id="tabs-3">
		<div class="tabs-content">
			options
		</div>
	</div>
</div>';

$Template->add( 'content', $content );
?>
