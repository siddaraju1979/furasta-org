<?php

/**
 * List Users, Furasta.Org
 *
 * Lists all the users from the USERS table.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$Template->loadJavascript( 'admin/users/users.js' );

$content='
<div id="new-user-dialog" title="New User" style="display:none">
	<div id="complete-message" style="display:none"></div>
	<form id="new-user-content">
		<table style="margin-top:0">
			<tr>
				<td colspan="2" id="form-error" class="php-error"></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="Name" value=""/></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type="text" name="Email" value=""/></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="Password" value=""/></td>
			</tr>
			<tr>
				<td>Repeat Password:</td>
				<td><input type="password" name="Repeat-Password" value=""/></td>
			</tr>
			<tr>
				<td>Group:</td>
				<td><select name="Group" id="group">';

$groups = rows( 'select id,name from ' . GROUPS );
foreach( $groups as $group )
	$content .= '<option value="' . $group[ 'id' ] . '">' . $group[ 'name' ] . '</option>';

$content.='
				</td>
			</tr>
		</table>
	</form>
</div>


<span style="float:right" id="new-user"><span id="header-New-User" class="header-img">&nbsp;</span><h1 class="image-left link">New User</h1></span>
<span><span id="header-Users" class="header-img">&nbsp;</span> <h1 class="image-left">Users</h1></span>
<br/>';

$super = ( $User->groupName( ) == '_superuser' );

$content .= '
<table id="users" class="row-color">
	<tr class="top_bar">
		<th>Name</th>
		<th>Email</th>
		<th>Group</th>';
if( $super )
	$content .= '<th>Login</th>';

$content .= '
		<th>Delete</th>
	</tr>
';

$query=query('select id,name,email,password,user_group,hash from '.USERS.' order by id');
while($row=mysql_fetch_array($query)){
	$id=$row['id'];
	$group_id=$row['user_group'];
	$group = ( $group_id == '_superuser' ) ? '_superuser' : single( 'select name from ' . GROUPS . ' where id=' . $group_id, 'name' );
        $delete = ( $group_id == '_superuser' ) ? '&nbsp;' : '<a id="'.$id.'" class="delete link"><span class="admin-menu-img" id="delete-img" title="Delete User" alt="Delete User"/></a>';
	$href = '<a href="users.php?page=edit-users&id='.$id.'" class="list-link">';
	$content.='<tr>
			<td class="first">'.$href.$row['name'].'</a></td>
			<td>'.$href.$row['email'].'</a></td>
			<td>'.$href.$group.'</a></td>';
	if( $super && $row[ 'hash' ] == 'activated' )
		$content .= '<td><a class="link loginas" user_id="' . $id . '" hashstr="' . $row[ 'password' ] . '">Login</a></td>';
	$content .= '
			<td>' . $delete . '</td>
		</tr>';
}

$content.='<tr><th colspan="6"></th></tr></table>';

$Template->add('content',$content);

?>
