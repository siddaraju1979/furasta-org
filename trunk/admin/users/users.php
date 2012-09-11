<?php

/**
 * List Users, Furasta.Org
 *
 * Lists all the users from the DB_USERS table.
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
				<td colspan="2"><h2>Groups</h2></td>
			</tr>
			<tr>';

			/**
			 * iterate through groups and print checkboxes 
			 */
			$groups = rows( 'select id,name from ' . DB_GROUPS );
			for( $i = 0; $i < count( $groups ); $i++ ){

			        $content .= '<td><input type="checkbox" class="checkbox" name="groups" value="' . $groups[ $i ][ 'id' ] . '"/> ' . $groups[ $i ][ 'name' ] . '</td>';
			        if( ( $i + 1 ) % 3 == 0 && ( $i + 1 ) < count( $groups ) )
			                $content .= '</tr><tr>';
			}


$content.='
			</tr>
		</table>
	</form>
</div>


<span style="float:right" id="new-user"><span id="header-New-User" class="header-img">&nbsp;</span><h1 class="image-left link">New User</h1></span>
<span><span id="header-Users" class="header-img">&nbsp;</span> <h1 class="image-left">Users</h1></span>
<br/>';

// check if superuser, saves checki$super = ( $User->groups( ) == '_superuser' );
$super = $User->isSuperUser( );

$content .= '
<table id="users" class="row-color">
	<tr class="top_bar">
		<th>Name</th>
		<th>Email</th>
		<th>Groups</th>';
if( $super )
	$content .= '<th>Login</th>';

$content .= '
		<th>Delete</th>
	</tr>
';

/**
 * get users from database and create instances of the user class
 */
$users = rows( 'select id from ' . DB_USERS );
$ids = array( );
foreach( $users as $user )
	array_push( $ids, $user[ 'id' ] );
User::createInstances( $ids );

foreach( $ids as $id ){
	$user = User::getInstance( $id );

	/**
	 * get names of groups the user is a member of
	 */
	$group = '';

	if( $user->isSuperUser( ) ) // if is super user, show in groups
		$group .= '<b>_superuser</b><br/>';
	
	foreach( $user->groups( ) as $id ){
		$g = Group::getInstance( $id );
		$group .= $g->name( ) . '<br/>';
	}

	$delete = '<a id="' . $user->id( ) . '" class="delete link"><span class="admin-menu-img" id="delete-img"'
		. ' title="Delete User" alt="Delete User"/></a>';

	$href = '<a href="users.php?page=edit-users&id=' . $user->id( ) . '" class="list-link">';
	$content .= '<tr>
			<td class="first">' . $href . $user->name( ) . '</a></td>
			<td>' . $href . $user->email( ) . '</a></td>
			<td>' . $href . $group . '</a></td>';
	if( $super )
		$content .= '<td><a class="link loginas" user_id="' . $user->id( ) . '" hashstr="' . $user->password( ) . '">Login</a></td>';	
	$content .= '
			<td>' . $delete . '</td>
		</tr>';
}

$content .= '<tr><th colspan="6"></th></tr></table>';

$Template->add( 'content', $content );

?>
