/**
 * List Groups Javascript, Furasta.Org
 *
 * javascript for the list groups page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$(document).ready(function(){

        $(".delete").live( "click", function(){

                fConfirm(trans( 'groups_delete_prompt' ),function(element){

                        element.parent().parent().fadeOut("slow", function( ){

				$( this ).remove( );

				rowColor( );

			});

                        fetch(window.furasta.site.url + "ajax.php?file=admin/users/delete-group.php&id="+element.attr("id"));

                },$(this));

        });

	$( "#new-group" ).click( function( ){

		var saveFunction = function( ){

                        var name = $( "input[name=\'Name\']" ).val( );

                        if( name == "" ){

				$( "#form-error" ).html( trans_error( 5, trans( 'name' ) ) );

				return;
	
			}

                        $( "#form-error" ).html( '<img src="' + window.furasta.site.url + '_inc/img/loading.gif"/>' +trans( 'loading' ) + '..' );

                	var perms_array = [];

                        $( "input[name=\'perms\']:checked" ).each( function( i ){

                                perms_array[ i ] = $( this ).val( );

                        });

			perms_array = perms_array.join( "," );

			$.ajax({

				type: "POST",

				url : window.furasta.site.url+"ajax.php?file=admin/users/new-group.php",

				data: "name=" + name + "&perms=" + perms_array,

                                success : function( html ){

					if( html == "error" )
						$( "#form-error" ).html( trans( 'groups_invalid_details' ) );
					else{

						$( "#new-group-dialog" ).dialog( "close" );
						$( "#form-error" ).html( "" );
						var num = $( "#config-table tr" ).length - 1; 

						$( "#config-table tr:nth-child(" + num + ")" ).after(
							'<tr>'
							+ '<td class="first">'
								+ '<a class="list-link" href="users.php?page=edit-groups&id=' + html + '">'
								+ name
								+ '</a></td><td><a class="list-link" href="users.php?page=edit-group&id=' + html
								+ '">0'
								+ '</a>'
							+ '</td>'
							+ '<td>'
								+ '<a id="' + html + '" class="delete link">'
									+ '<span id="delete-img" class="admin-menu-img" alt="Delete Group"'
									+ ' title="Delete Group">&nbsp;</span>'
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
