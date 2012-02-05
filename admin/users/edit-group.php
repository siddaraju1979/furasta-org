<?php

/**
 * Edit Group, Furasta.Org
 *
 * A facility to edit the groups details.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$id = addslashes( @$_GET[ 'id' ] );
if( $id == 0 )
	header( 'location: users.php' );

/**
 * set up javascript and php form validation
 */
$conds = array(
        'Name' => array(
                'required'      =>      true,
                'pattern'       =>      "^[A-Z a-z]{1,50}$"
        ),
);

$valid = validate( $conds, "#users-edit", 'Edit-User' );

/**
 * read post information and edit page if applicable
 */
if( isset( $_POST[ 'Edit-User' ] ) && $valid == true ){

        $name = addslashes( $_POST[ 'Name' ] );
        $perm = addslashes( $_POST[ 'perm' ] );

        query( 'update ' . GROUPS . ' set name="' . $name . '",perm="' . $perm . '" where id=' . $id );

	if( $User->group == $id )
		$_SESSION[ 'user' ][ 'perm' ] = explode( ',', $perm );

	cache_clear( 'USERS' );
}

$Template->loadJavascript( 'admin/users/edit-group.js' );

// get default permissions
require_once HOME . 'admin/users/permissions.php';

$group = row( 'select name,perm from ' . GROUPS . ' where id=' . $id . ' limit 1' );

$group_perm = explode( ',', $group[ 'perm' ] );

$content='
<span id="header-Groups" class="header-img"></span><h1 class="image-left">Edit Group</h1>
<br/>
	<form method="post" id="users-edit">
		<table id="config-table" class="row-color">
			<col width="50%"/>
			<col width="50%"/>
			<tr>
				<th colspan="2">Group Options</th>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="Name" value="' . $group[ 'name' ] . '"/></td>
			</tr>
			<tr>
				<td colspan="2"><h3>General Permissions</h3></td>
			</tr>
                        <tr><td colspan="2">';

                        /**
                         * iterate through perms array and print checkboxes 
                         */
                        $i = 0;
                        foreach( $perms as $value => $name ){

				$checked = ( in_array( $value, $group_perm ) ) ? ' checked="checked"' : '';

                                $content .= '<span><input type="checkbox" class="checkbox" name="perms" value="' . $value . '"' . $checked . '/> ' . $name . '</span>';

                                if( ( $i + 1 ) % 3 == 0 && ( $i + 1 ) < count( $perms ) )
                                        $content .= '<br/>';

                                $i++;
                        }

$content .= '
                        </td></tr>

		</table>
<input type="hidden" name="perm" value="' . $group[ 'perm' ] . '"/>
<input type="submit" name="Edit-User" id="User" class="submit right" value="Save" style="margin-right:10%"/>
</form>
<br style="clear:both"/>
';

$Template->add('content',$content);


?>
