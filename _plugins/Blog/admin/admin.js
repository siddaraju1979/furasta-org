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
});

function add_tags( ){
	tags = $( '#tags-in' ).val( );
	if( tags == '' ){
		return;
	}
	if( tags.indexOf( ',' ) !== -1 ){
		tags = tags.split( ',' );
	}
	else{
		tags = [ tags ];
	}
	
	var content = '';
	for( var i = 0; i < tags.length; ++i ){
		content += '<span class="tag">' + tags[ i ] + '</span>';
	}

	$( '#tags-put' ).append( content );
}
