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
	$( '#show-bcc' ).click( toggle_bcc );

	if( $( '#message-body' ).length )
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

function toggle_bcc( ){
	var bcc = $( '#bcc' );
	if( bcc.hasClass( 'hidden' ) ){
		bcc.slideDown( 'slow' );
		$( this ).html( trans( 'mailing_list_hide_bcc' ) );
		bcc.removeClass( 'hidden' );
	}
	else{
		bcc.slideUp( 'slow' );
		$( this ).html( trans( 'mailing_list_show_bcc' ) );
		bcc.addClass( 'hidden' );
	}
}

function send_emails( emails, email_subject, email_content ){
	var end = emails.length;
	var count = 0;
	for( var i = 0; i < end; ++i ){
		var email_address = emails[ i ];
		$.ajax({
			url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Mailing-List/admin/send-email.php',
			type : 'POST',
			data : {
				email : email_address,
				subject : email_subject,
				content : email_content
			},
			success : function( e ){
				if ( e == 'failed' ){
					$( '#sending-content' ).append(
						'<p class="error"><b>' + trans( 'mailing_list_sending_failed' ) + ' ' + e + '</b></p>'
					);
					count++;
				}
				else{
					$( '#sending-content' ).append( 
						'<p><i>' + trans( 'mailing_list_sent_to' ) + ' ' + e + '</i></p>'
					);
					count++;
				}
				if( count == end ){
					$( '.loading' ).html( '' );
					$( '#sending-content' ).append( '<p><b>' + trans( 'mailing_list_sending_complete' ) + '</b></p>' );
				}
			}
		});
	}
}
