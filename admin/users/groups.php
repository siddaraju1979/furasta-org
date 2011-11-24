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

$javascript = '
$(document).ready(function(){

        $(".delete").live( "click", function(){

                fConfirm("Are you sure you want to delete this group? <br/><b>ALL USERS IN THE GROUP WILL BE DELETED</b>",function(element){

                        element.parent().parent().fadeOut("slow", function( ){

				$( this ).remove( );

				rowColor( );

			});

                        fetch("/_inc/ajax.php?file=admin/users/delete-group.php&id="+element.attr("id"));

                },$(this));

        });

	$( "#new-group" ).click( function( ){

		var saveFunction = function( ){

                        var name = $( "input[name=\'Name\']" ).val( );

                        if( name == "" ){

				$( "#form-error" ).html( "The Name field is a required field." );

				return;
	
			}

                        $( "#form-error" ).html( "<img src=\'' . SITEURL . '_inc/img/loading.gif\'/> Loading.." );

                	var perms_array = [];

                        $( "input[name=\'perms\']:checked" ).each( function( i ){

                                perms_array[ i ] = $( this ).val( );

                        });

			perms_array = perms_array.join( "," );

			$.ajax({

				type: "POST",

				url : "' . SITEURL . '_inc/ajax.php?file=admin/users/new-group.php",

				data: "name=" + name + "&perms=" + perms_array,

                                success :       function( html ){

                                                        if( html == "error" )

                                                                $( "#form-error" ).html( "The details you provided are not valid. Please try creating a group again. The group name may already be taken." );

                                                        else{

								$( "#new-group-dialog" ).dialog( "close" );

								$( "#form-error" ).html( "" );

								var num = $( "#config-table tr" ).length - 1; 

                                                                $( "#config-table tr:nth-child(" + num + ")" ).after( "<tr><td class=\'first\'><a class=\'list-link\' href=\'users.php?page=edit-groups&id=" + html + "\'>" + name + "</a></td><td><a class=\'list-link\' href=\'users.php?page=edit-group&id=" + html + "\'>0</a></td><td><a id=\'" + html + "\' class=\'delete link\'><span id=\'delete-img\' class=\'admin-menu-img\' alt=\'Delete Group\' title=\'Delete Group\'>&nbsp;</span></a></td></tr>");

                                                                rowColor( );

                                                        }
                                          
                                },

                                error   :       function( ){

                                                $( "#form-error" ).html( "There has been an error processing your request. Please try again." );

                                }

			});

		};

	        $( "#new-group-dialog" ).dialog({

	                modal:  true,

	                width:  "500px",

	                buttons: {
	
				"Close": function( ) { $( this ).dialog( "close" ); },

				"Save": saveFunction,

			},

	                hide:   "fade",

	                show:   "fade",

	                resizeable:     false

	        });

		$( "#new-group-dialog" ).dialog( "open" );

	});

});
';

$Template->loadJavascript( 'FURASTA_ADMIN_GROUPS_LIST', $javascript );

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
