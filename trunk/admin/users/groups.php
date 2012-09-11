<?php

/**
 * List Groups, Furasta.Org
 *
 * Lists all the groups from the DB_GROUPS table.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$Template->loadJavascript( 'admin/users/groups.js' );

// load default permissions
require_once HOME . 'admin/users/permissions.php';

$content='
<div id="new-group-dialog" title="New Group" style="display:none">
	<form id="new-group-content">
		<table style="margin-top:0">
			<tr>
				<td colspan="2" id="form-error" class="php-error"></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td colspan="2"><input type="text" name="Name" value=""/></td>
			</tr>
			<tr>
				<td><h3>Group Permissions</h3></td>
			</tr>
			<tr>
				<td colspan="3"><p><i>Members of this group can do the following:</i></p></td>
			</tr>
			<tr>
			';

			/**
			 * iterate through perms array and print checkboxes 
			 */
			$i = 0;
			foreach( $perms as $value => $name ){

			        $content .= '<td><input type="checkbox" class="checkbox" name="group" value="' . $value . '"/> ' . $name . '</td>';

			        if( ( $i + 1 ) % 3 == 0 && ( $i + 1 ) < count( $perms ) )
			                $content .= '</tr><tr>';

				$i++;
			}

$content .= '
			</tr>
			<tr>
				<td><h3>File Permissions</h3></td>
			</tr>
			<tr>
				<td colspan="3"><p><i>Members of this group can view/edit files from the following groups/users:</i></p></td>
			</tr>

			<tr>
				<td colspan="3"><p><i>Note: All users own their own files and can always edit them.</i></p>
			</tr>
		</table>
	</form>
</div>


<span style="float:right" id="new-group"><span class="header-img" id="header-New-Group"/>&nbsp;</span><h1 class="image-left link">New Group</h1></span>
<span><span class="header-img" id="header-Groups">&nbsp;</span><h1 class="image-left">Groups</h1></span>
<br/>
<table id="config-table" class="row-color">
	<tr class="top_bar"><th>Name</th><th>Members</th><th>Delete</th></tr>
';

$query = query( 'select id,name from ' . DB_GROUPS . ' order by id' );

while($row=mysql_fetch_array($query)){
	$id=$row['id'];
	$num = num( 'select * from ' . DB_USERS . ' where user_group="' . $id .'"' );
	$href='<a href="users.php?page=edit-group&id='.$id.'" class="list-link">';
	$delete = '<a id="'.$id.'" class="delete link"><span class="admin-menu-img" id="delete-img" title="Delete Group" alt="Delete Group"/></a>';
	$content.='<tr>
			<td class="first">'.$href.$row['name'].'</a></td>
			<td>'.$href.$num.'</a></td>
			<td>' . $delete . '</a></td>
		</tr>';
}

$content.='<tr><th colspan="6"></th></tr></table>';

$Template->add('content',$content);

?>
