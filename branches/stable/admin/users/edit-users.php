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
        query( 'update ' . USERS . ' set name="' . $name . '",email="' . $email . '", user_group="' . $group . '" where id=' . $id );
	cache_clear( 'USERS' );
}


$user = row( 'select name,user_group,email,password from ' . USERS . ' where id=' . addslashes( $id ) . ' limit 1', true );

$javascript='
$( document ).ready( function( ){

	$( "#change-password" ).click( function( ){

		fConfirm( "To reset the password an email will be sent to ' . $user[ 'email' ] . ' with reset details. Press \'Yes\' to confirm.", function( ){

			fetch( "' . SITEURL . '_inc/ajax.php?file=admin/users/reminder.php&no_config&email=' . $user[ 'email' ] . '" );

		} );

	});

});
';

$Template->loadJavascript( 'FURASTA_ADMIN_USERS_EDIT', $javascript );

$content='
<span style="float:right" id="change-password"><span id="header-Login" class="header-img"></span><h1 class="image-left link">Change Password</h1></span>
<span id="header-Users" class="header-img"></span><h1 class="image-left">Edit User</h1>

<br/>
	<form method="post" id="users-edit">
		<table id="config-table" class="row-color">
			<col width="50%"/>
			<col width="50%"/>
			<tr>
				<th colspan="2">User Options</th>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="Name" value="'.$user['name'].'"/></td>
			</tr>
                	<tr>
				<td>Email:</td>
				<td><input type="text" name="Email" value="'.$user['email'].'"/></td>
			</tr>
			<tr>
				<td>Group:</td>
				<td><select name="Group" id="group">';

if( $user[ 'user_group' ] == '_superuser' )
	$content .= '<option value="_superuser" selected="selected">_superuser</option>';
else{
	$groups=rows( 'select id,name from ' . GROUPS );
	foreach($groups as $group){
		if($group['id']==$user['user_group'])
			$content.='<option selected="selected" value="'.$group['id'].'">'.$group['name'].'</option>';
		else
			$content.='<option value="'.$group['id'].'">'.$group['name'].'</option>';
	}
}

$content.='
				</select></td>
			</tr>
		</table>
<input type="submit" name="Edit-User" id="User" class="submit right" value="Save" style="margin-right:10%"/>
</form>
<br style="clear:both"/>
';

$Template->add('content',$content);


?>
