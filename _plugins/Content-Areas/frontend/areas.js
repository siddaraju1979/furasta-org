/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 */

// document.ready
$(function(){
	/**
	 * search for unregistered content areas,
	 * collect data on them and then send data
	 * back to server
	 */
	$( '.content-area-unregistered' ).each( function( ){
		var $this = $( this );
		var name = $this.attr( 'id' ).split( '-' );
		var name = name[ name.length - 1 ];
		var width = $this.width( );
		var position = $this.position( );
		var left = position.left;
		var top = position.top;
		
		// save data on server
		$.ajax({
			type: 'POST',
			url: $furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/frontend/register.php',
			data: {
				'id' : $furasta.page.id,
				'name' : name,
				'width' : width,
				'left' : left,
				'top' : top
			}
		});
	});
});
