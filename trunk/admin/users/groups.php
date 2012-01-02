<?php

/**
 * List Groups, Furasta.Org
 *
 * Lists all the groups from the GROUPS table.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$Template->loadJavascript( 'admin/users/groups.js' );

/**
 * get perm checkboxes into array so that
 * plugins can alter/expand them 
 */
$perms = array(
		'e' => 'Edit Pages',
		'c' => 'Create Pages',
		'd' => 'Delete Pages',
		't' => 'Manage Trash',
		's' => 'Edit Settings',
		'u' => 'Edit Users'
);

$perms = $Plugins->filter( 'admin', 'filter_group_permissions', $perms );

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
			<tr>';

			/**
			 * iterate through perms array and print checkboxes 
			 */
			$i = 0;
			foreach( $perms as $value => $name ){

			        $content .= '<td><input type="checkbox" class="checkbox" name="perms" value="' . $value . '"/> ' . $name . '</td>';

			        if( ( $i + 1 ) % 3 == 0 && ( $i + 1 ) < count( $perms ) )
			                $content .= '</tr><tr>';

				$i++;
			}

$content .= '
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

$query = query( 'select id,name from ' . GROUPS . ' order by id' );

while($row=mysql_fetch_array($query)){
	$id=$row['id'];
	$num = num( 'select * from ' . USERS . ' where user_group="' . $id .'"' );
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
