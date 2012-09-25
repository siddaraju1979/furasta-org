/**
 * List Users Javascript, Furasta.Org
 *
 * Contains the javascript for the admin users file
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$(document).ready(function(){

        $(".delete").live( "click", function(){

                fConfirm(trans( 'prompt_confirm_delete' ),function(element){

                        element.parent().parent().fadeOut("slow", function( ){

                                $( this ).remove( );

                                rowColor( );

                        });

                        fetch( window.furasta.site.url + "ajax.php?file=admin/users/delete-user.php&id="+element.attr("id"));

                },$(this));

        });

	$( "#new-user" ).click( function( ){

		var saveFunction = function( ){

			$( '#form-error' ).html( '' );

                        var conds = {
                                "Name" : {
					'name' : trans( 'name' ),
                                        "required" : true,
                                },
                                "Email" : {
					'name' : trans( 'email' ),
                                        "email" : true,
                                        "required" : true
                                },
                                "Password" : {
					'name' : trans( 'users_password' ),
                                        "required" : true,
                                        "match" : "Repeat-Password",
                                        "minlength" : 5
                                },
                                "Repeat-Password" : {
					'name' : trans( 'users_repeat_pass' ),
                                        "required" : true
                                }
                        };

			$( "#new-user-content" ).validate( conds, function( error ){

				$( "#form-error" ).html( error );

			});

			var result = $( "#new-user-content" ).validate( "execute" );

                        if( result == false )
                                return;

                        $( "#form-error" ).html( '<img src="' + window.furasta.site.url + '_inc/img/loading.gif"/> ' + trans( 'loading' ) + '..' );

			var name = $( "input[name='Name']" ).val( );

                        var email = $( "input[name='Email']" ).val( );

                        var password = $( "input[name='Password']" ).val( );

                        var repeat = $( "input[name='Repeat-Password']" ).val( );

			// get groups in array
			var groups = [];
                        $( "#new-user-dialog .checkbox:checked" ).each(function(){
				groups.push( $(this).val( ) );
			});
			groups.join( ',' );

			// throw error if no groups selected
			if( groups.length == 0 ){
				$( '#form-error' ).html( "You must select at least one group" );
				return;
			}

			$.ajax({

				type: "POST",

				url : window.furasta.site.url + "ajax.php?file=admin/users/new-user.php",

				data: "name=" + name + "&email=" + email + "&password=" + password + "&repeat=" + repeat + "&groups=" + groups,

                                success : function( html ){

					if( html == "error" )
						$( "#form-error" ).html( trans( 'users_invalid_details' ) );
					else{
						$( "#new-user-dialog" ).dialog({ hide: null, buttons:{ "Close": function( ) { 

							$( "#new-user-dialog" ).dialog( "close" );

							$( "#form-error" ).html( "" );

							$( "#complete-message" ).css({ "display":"none" });

							$( "#new-user-content" ).css({ "display":"block" });

						} } });

						$( "#new-user-content" ).css({ "display":"none" });

						$( "#complete-message" ).html( trans( 'users_user_added' ) ).fadeIn( "fast" );

						var num = $( "#users tr" ).length - 1; 

						$( "#users tr:nth-child(" + num + ")" ).after(
							'<tr>'
							+ '<td class="first">'
								+ '<a class="list-link" href="users.php?page=edit-users&id=' + html + '">'
								+ name
								+ '</a>'
							+ '</td>'
							+ '<td>'
								+ '<a class="list-link" href="users.php?page=edit-users&id=' + html + '">'
								+ email
								+ '</a>'
							+ '</td>'
							+ '<td>'
								+ '<a class="list-link" href="users.php?page=edit-users&id='+ html + '">'
								+ group_name 
								+ '</a>'
							+ '</td>'
							+ '<td>'
								+ '<a id="' + html + '" class="delete link"><span class="admin-menu-img" '
								+ 'id="delete-img" title="Delete User" alt="Delete User"/>'
								+ '</a>'
							+ '</td>'
							+ '</tr>'
						);

						rowColor( );

					}
                                          
                                },

                                error   :       function( ){

                                                $( "#form-error" ).html( trans( 'prompt_error' ) );

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

	$( '.loginas' ).click( loginas );

});

function loginas( ){
	id = $( this ).attr( 'user_id' );
	hash = $( this ).attr( 'hashstr' );
	$( 'body' ).append( 
		'<form id="loginhash" method="post" action="'
			+ window.furasta.site.url + 'ajax.php?file=admin/users/switch.php" style="display:none">'
			+ '<input type="hidden" name="userid" value="' + id + '"/>'
			+ '<input type="hidden" name="hash" value="' + hash + '"/>'
			+ '<input type="hidden" name="loginhash" value="true"/>'
		+ '</form>'
	);
	$( '#loginhash' ).submit( );
}
