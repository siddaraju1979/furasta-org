/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	rowColor();
	$( '.add-subscriber' ).click(add_subscriber);
	$( '.delete' ).live( 'click', delete_subscriber );
	tinymce_changeConfig( '#message-body', 'Normal' ); 
});

function add_subscriber( ){
	var content = 'test';

	$( '#add-subscriber-dialog' )
		.attr( 'title', trans( 'mailing_list_add_subscriber' ) + ' - ' + trans( 'menu_mailing_list' ) )
		.dialog({
			height: 200,
			width: '40%',
			modal : true,
			buttons : {
				'Cancel' : function( ){
					$( this ).dialog( 'close' );
				},
				'Save' : function( ){
		                        var conds = {
        		                        "Name" : {
		                                        "required" : true,
		                                },
		                                "Email" : {
		                                        "email" : true,
		                                        "required" : true
		                                }
		                        };

					$( "#new-sub-form" ).validate( conds, function( error ){

						$( "#form-error" ).html( error );

					});

					var result = $( "#new-sub-form" ).validate( "execute" );

		                        if( result == false )

		                                return;

					var sub_name = ( $( '#sub_name' ) ) ? $( '#sub_name' ).val( ) : '';
					var sub_email = $( '#sub_email' ).val( );
					$.ajax({
						url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Mailing-List/new-sub.php',
						data : {
							name : sub_name,
							email : sub_email
						},
						type : 'POST',
						success : function( id ){
							var content = '<tr>';
							if( $( '#sub_name' ) )
								content += '<td>' + sub_name + '</td>';
							content += '<td>' + sub_email + '</td>';
							content += '<td><a class="link delete" id="' + id + '">'
								+ '<span class="admin-menu-img" id="delete-img">&nbsp;</span></a></td>';
							var num = $( '#subscribers tr' ).length -1;
							$( '#subscribers tr:nth-child(' + num + ')' ).after( content );
						}
					});
					$( this ).dialog( 'close' );
				}
			}
		});

}

function delete_subscriber( ){
	var sub_id = $( this ).attr( 'id' );
	fConfirm( trans( 'prompt_confirm_delete' ), function( ){
		$.ajax({
			url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Mailing-List/admin/delete-sub.php',
			type : 'POST',
			data : {
				id : sub_id
			},
			success : function( ){
				$( '#' + sub_id ).parent( ).parent( ).remove( );
				rowColor( );
			}
		});
	});
}
