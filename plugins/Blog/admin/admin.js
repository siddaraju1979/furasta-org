/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	tinymce_changeConfig( '#message-body', 'Normal' );
	$( '#tags-add' ).click( add_tags );
	$( '.blog-delete' ).live( 'click', delete_tag );
	$( '#help-tags' ).click( function( ){
		fHelp( trans( 'blog_help_tags' ) );
	});
	$( '#help-page' ).click( function( ){
		fHelp( trans( 'blog_help_page' ) );
	});
	$( 'input[name=blog-delete]' ).click( delete_post );
});

function add_tags( ){
	tags = $( '#tags-in' ).val( );
	if( tags == '' )
		return;

	var existing = $( 'input[name=tags-hidden]' ).val( );
	var cont = ( existing == '' ) ? tags : existing + ',' + tags;
	$( 'input[name=tags-hidden]' ).val( cont );	

	if( tags.indexOf( ',' ) !== -1 ){
		tags = tags.split( ',' );
	}
	else{
		tags = [ tags ];
	}
	
	var content = '';
	for( var i = 0; i < tags.length; ++i ){
		content += '<span class="tag">';
		content += '<span>' + tags[ i ] + ' </span>';
		content += '<a class="link delete-small-img blog-delete">&nbsp;</a>';
		content += '</span>';
	}

	$( '#tags-in' ).val( '' );
	$( '#tags-put' ).append( content );
}

function delete_tag( ){
	var tag = $( 'span', $( this ).parent( ) ).html( );
	tag = $.trim( tag );
	var tags = $( 'input[name=tags-hidden]' ).val( );
	$( this ).parent( ).fadeOut( 'slow' );
	if( tags.indexOf( ',' ) === -1 )
		tags = [ tags ];
	else
		tags = tags.split( ',' );

	var newtags = [];
	for( var i in tags ){
		var current = $.trim( tags[ i ] );
		if( current != tag )
			newtags.push( current );
	}
	newtags = newtags.join( ',' );

	$( 'input[name=tags-hidden]' ).val( newtags );
}

function delete_post( ){
	var $this = $( this );
	fConfirm( trans( 'prompt_confirm_delete' ), function( ){
		var post_id = $this.attr( 'id' );
		var blog_id = $this.attr( 'blog_id' );
		$.ajax({
			url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Blog/admin/delete-post.php',
			type : 'post',
			data : {
				id : post_id
			},
			success : function( ){
				window.location = 'pages.php?action=edit&id=' + blog_id;
			}
		});
	});
}
