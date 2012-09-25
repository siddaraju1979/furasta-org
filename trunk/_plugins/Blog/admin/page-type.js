/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	$( '#tabs' ).tabs( );
	$( '#help-blog-title' ).click( function( ){ fHelp( trans( 'blog_title_help' ) ); });
	$( '.delete' ).click( delete_post );
});

function delete_post( ){
	var $this = $( this );
	fConfirm( trans( 'prompt_confirm_delete' ), function( ){
		var post_id = $this.attr( 'id' );
		$.ajax({
			url : window.furasta.site.url + 'ajax.php?file=_plugins/Blog/admin/delete-post.php',
			type : 'post',
			data : {
				id : post_id
			},
			success : function( ){
				$this.parent( ).parent( ).remove( );
			}
		});
	});
}
