/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	rowColor( );
	$( '#tabs' ).tabs( );
	$( '.add-forum' ).click( add_forum_dialog );
	$( '.delete' ).live( 'click', delete_forum );
});

function add_forum_dialog( ){
	var dialogButtons = {};
	dialogButtons[ trans( 'prompt_close' ) ] = function( ){
		$( this ).dialog( 'close' );
	};
	dialogButtons[ trans( 'prompt_save' ) ] = function( ){
		var name = $( 'input[name="forum-name"]' ).val( );
		var desc = $( 'textarea[name="forum-desc"]' ).val( );

		if( name == '' ){
			$( '#form-error' ).html( trans( 'forum_add_name' ) );
			return false;
		}
		$( '#form-error' ).html( '' );

		$.ajax({
			url : window.furasta.site.url + 'ajax.php?file=_plugins/Forum/admin/new-forum.php',
			type : 'post',
			data : {
				forum_name : name,
				forum_desc : desc,
				forum_id : window.furasta.page.id 
			},
			success : function( html ){
				$( '.empty-table' ).remove( );
				var insert = $( '#forums tr' ).length - 2;
				$( '#forums tr:eq(' + insert + ')' ).after( '<tr>'
					+ '<td>' + name + '</td>'
					+ '<td>' + desc + '</td>'
					+ '<td>0</td>'
					+ '<td>'
						+ '<a class="link delete" id="' + html + '">'
							+ '<span id="delete-img" class="admin-menu-img">&nbsp;</span>'
						+ '</a>'
					+ '</td>'
				+ '</tr>' );
				rowColor( );
			}
		});
	
		$( 'input[name="forum-name"]' ).val( '' );
		$( 'textarea[name="forum-desc"]' ).val( '' );
		$( this ).dialog( 'close' );
	};

	$( '#new-forum-dialog' ).dialog({
		width : '400px',
		buttons: dialogButtons
	});
}

function delete_forum( ){
	var $this = $( this );
	var forum_id = $this.attr( 'id' );
	fConfirm( trans( 'prompt_confirm_delete' ), function( ){
		$.ajax({
			url : window.furasta.site.url + 'ajax.php?file=_plugins/Forum/admin/delete-forum.php',
			data : {
				id : forum_id
			},
			type : 'post',
			success : function( ){
				$this.parent( ).parent( ).fadeOut( 'fast', rowColor );
			}
		});
	});
}
