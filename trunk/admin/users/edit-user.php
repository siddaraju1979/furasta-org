<?php

/**
 * Edit User, Furasta.Org
 *
 * A facility to edit the users details.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 * @todo	add email validation when email is updated
 */

$id = @$_GET[ 'id' ];
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
	'Email' => array(
		'required'	=>	true,
		'email'		=>	true
	)
);

$valid = validate( $conds, "#users-edit", 'Edit-User' );

/**
 * read post information and edit page if applicable
 */
if( isset( $_POST[ 'Edit-User' ] ) && $valid == true ){
        $name = addslashes( $_POST[ 'Name' ] );
        $email = addslashes( $_POST[ 'Email' ] );
        $group = addslashes( $_POST[ 'Group' ] );
        query( 'update ' . DB_USERS . ' set name="' . $name . '",email="' . $email . '", user_group="' . $group . '" where id=' . $id );
	cache_clear( 'USERS' );
}


$user = User::getInstance( $id );

$Template->loadJavascript( 'admin/users/edit-user.js' );

$content='
<span style="float:right" id="change-password"><span id="header-Login" class="header-img"></span><h1 class="image-left link">Reset Password</h1></span>
<span id="header-Users" class="header-img"></span><h1 class="image-left">Edit User</h1>

<br/>
	<form method="post" id="users-edit">
		<div id="tabs">
			<ul>
				<li><a href="#Options">General</a></li>
			</ul>
			<div id="#Options">
                                <table class="row-color">
					<tr>
						<td>Name:</td>
						<td><input type="text" class="input" name="Name" value="'.$user->name( ).'"/></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><input type="text" class="input" name="Email" value="'.$user->email( ).'"/></td>
					</tr>
					<tr>
						<td>Group:</td>
						<td>';

			/**
			 * iterate through perms array and print checkboxes 
			 *
			$i = 0;
			foreach( $perms as $value => $name ){

			        $content .= '<td><input type="checkbox" class="checkbox" name="group" value="' . $value . '"/> ' . $name . '</td>';

			        if( ( $i + 1 ) % 3 == 0 && ( $i + 1 ) < count( $perms ) )
			                $content .= '</tr><tr>';

				$i++;
                        }*/

if( $user->isSuperUser( ) )
	$content .= '<checkbox value="_superuser" selected="selected" disabled="disabled">_superuser</option>';
else{
	$groups=rows( 'select id,name from ' . DB_GROUPS );
        $user_groups = $user->groups( ); 
        foreach($groups as $group){
		if( in_array( $group[ 'id' ], $user_groups ) )
			$content.='<option selected="selected" value="'.$group['id'].'">'.$group['name'].'</option>';
		else
			$content.='<option value="'.$group['id'].'">'.$group['name'].'</option>';
	}
}

$content.='
						</td>
					</tr>
				</table>
			</div>
</div>
                        <br/>
<input type="submit" name="Edit-User" id="User" class="submit right" value="Save" style="margin-right:10%"/>
</form>
<br style="clear:both"/>
';

$Template->add('content',$content);


?>
