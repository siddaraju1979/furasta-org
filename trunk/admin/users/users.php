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

$javascript = '
$(document).ready(function(){

        $(".delete").live( "click", function(){

                fConfirm("Are you sure you want to delete this user?",function(element){

                        element.parent().parent().fadeOut("slow", function( ){

                                $( this ).remove( );

                                rowColor( );

                        });

                        fetch("/_inc/ajax.php?file=admin/users/delete-user.php&id="+element.attr("id"));

                },$(this));

        });

	$( "#new-user" ).click( function( ){

		var saveFunction = function( ){

                        var conds = {
                                "Name" : {
                                        "required" : true,
                                },
                                "Email" : {
                                        "email" : true,
                                        "required" : true
                                },
                                "Password" : {
                                        "required" : true,
                                        "match" : "Repeat-Password",
                                        "minlength" : 5
                                },
                                "Repeat-Password" : {
                                        "required" : true
                                },
                                "Group":{
                                        "required" : true
                                }
                        };

			$( "#new-user-content" ).validate( conds, function( error ){

				$( "#form-error" ).html( error );

			});

			var result = $( "#new-user-content" ).validate( "execute" );

                        if( result == false )

                                return;

                        $( "#form-error" ).html( "<img src=\'' . SITEURL . '_inc/img/loading.gif\'/> Loading.." );

			var name = $( "input[name=\'Name\']" ).val( );

                        var email = $( "input[name=\'Email\']" ).val( );

                        var password = $( "input[name=\'Password\']" ).val( );

                        var repeat = $( "input[name=\'Repeat-Password\']" ).val( );

                        var group = $( "select[name=\'Group\']" ).val( );

			var group_name = $( "select[name=\'Group\'] option:selected" ).text( );

			$.ajax({

				type: "POST",

				url : "' . SITEURL . '_inc/ajax.php?file=admin/users/new-user.php",

				data: "name=" + name + "&email=" + email + "&password=" + password + "&repeat=" + repeat + "&group=" + group,

                                success :       function( html ){

                                                        if( html == "error" )

                                                                $( "#form-error" ).html( "The details you provided are not valid. Please try creating a user again. The cause may be that email adresses may only be used by one user." );

                                                        else{
								$( "#new-user-dialog" ).dialog({ hide: null, buttons:{ "Close": function( ) { 

									$( "#new-user-dialog" ).dialog( "close" );

									$( "#form-error" ).html( "" );

									$( "#complete-message" ).css({ "display":"none" });

									$( "#new-user-content" ).css({ "display":"block" });

								 } } });

								$( "#new-user-content" ).css({ "display":"none" });

								$( "#complete-message" ).html( "An email has been sent to " + email + " for validation. This user will not be able to login until the email is validated." ).fadeIn( "fast" );

								var num = $( "#users tr" ).length - 1; 

                                                                $( "#users tr:nth-child(" + num + ")" ).after( "<tr><td class=\'first\'><a class=\'list-link\' href=\'users.php?page=edit-users&id=" + html + "\'>" + name + "</a></td><td><a class=\'list-link\' href=\'users.php?page=edit-users&id=" + html + "\'>" + email + "</a></td><td><a class=\'list-link\' href=\'users.php?page=edit-users&id=" + html + "\'>" + group_name + "</a></td><td><a id=\'" + html + "\' class=\'delete link\'><span class=\'admin-menu-img\' id=\'delete-img\' title=\'Delete User\' alt=\'Delete User\'/></a></td></tr>");

                                                                rowColor( );

                                                        }
                                          
                                },

                                error   :       function( ){

                                                $( "#form-error" ).html( "There has been an error processing your request. Please try again." );

                                }

			});

		};

	        $( "#new-user-dialog" ).dialog({

	                modal:  true,

	                width:  "400px",

	                buttons: {
	
				"Close": function( ) { $( this ).dialog( "close" ); },

				"Save": saveFunction,

			},

	                hide:   "fade",

	                show:   "fade",

	                resizeable:     false

	        });

		$( "#new-user-dialog" ).dialog( "open" );

	});

});
';

$Template->loadJavascript( 'FURASTA_ADMIN_USERS_LIST', $javascript );

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
<br/>
<table id="users" class="row-color">
	<tr class="top_bar"><th>Name</th><th>Email</th><th>Group</th><th>Delete</th></tr>
';

$query=query('select id,name,email,user_group from '.USERS.' order by id');
while($row=mysql_fetch_array($query)){
	$id=$row['id'];
	$group_id=$row['user_group'];
	$group = ( $group_id == '_superuser' ) ? '_superuser' : single( 'select name from ' . GROUPS . ' where id=' . $group_id, 'name' );
	$href='<a href="users.php?page=edit-users&id='.$id.'" class="list-link">';
        $delete = ( $group_id == '_superuser' ) ? '&nbsp;' : '<a id="'.$id.'" class="delete link"><span class="admin-menu-img" id="delete-img" title="Delete User" alt="Delete User"/></a>';
	$content.='<tr>
			<td class="first">'.$href.$row['name'].'</a></td>
			<td>'.$href.$row['email'].'</a></td>
			<td>'.$href.$group.'</a></td>
			<td>' . $delete . '</td>
		</tr>';
}

$content.='<tr><th colspan="6"></th></tr></table>';

$Template->add('content',$content);

?>
