/**
 * Forum Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

alert( 'te' );

$(function(){
	alert( 'test' );
	rowColor( );
	$( '#tabs' ).tabs( );
	$( '.add-forum' ).click( add_forum_dialog );
});

function add_forum_dialog( ){
	var dialogButtons = {};
	dialogButtons[ trans( 'prompt_save' ) ] = function( ){
		alert( 'test' );
		$( this ).dialog( 'close' );
	});
	dialogButtons[ trans( 'prompt_close' ) ] = function( ){
		$( this ).dialog( 'close' );
	});
	$( '#new-forum-dialog' ).dialog({
		width : '400px',
		buttons: dialogButtons
	});
}
