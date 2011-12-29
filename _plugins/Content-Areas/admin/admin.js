/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	update_template_areas( );
	$( '#content-template-select' ).change( update_template_areas );
	$( '#content-area-select' ).change( update_content_area );

	$( '.widget-left' ).draggable({
		connectToSortable : ".content-area-widgets",
		helper : 'clone',
		revert : 'invalid',
		stop : function( event, ui ){
			if( $( '.content-area-widgets' ).hasClass( 'empty' ) ){
				$( '.content-area-widgets p' ).remove( );
				$( '.content-area-widgets' ).removeClass( 'empty' );
			}
		}
	});
});

function update_template_areas( ){
	if( $( '#content-area-select' ).val( ) != '---' ){
		$( '#content-area-select' ).val( '---' );
		$( '#content-template' ).html( '<p><i>Please select a content area</i></p>' );
	}
	$.ajax({
		url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/admin/content-areas.php',
		type : 'POST',
		data : {
			template : $( '#content-template-select' ).val( )
		},
		dataType : 'json',
		success : function( areas ){
			if( areas.length != 0 ){
				var select = '<option>---</option>';
				for( var i = 0; i < areas.length; ++i )
					select += '<option>' + areas[ i ] + '</option>';
				$( '#content-area-select' ).html( select );
			}
		}
	});		
}

function update_content_area( ){
	var area_name = $( '#content-area-select' ).val( );
	if( area_name == '---' )
		return;
	$.ajax({
		url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/admin/get-area.php',
		type : 'POST',
		data : {
			name : area_name
		},
		success : function( html ){
			$( '#content-template' ).html( html );
			$( '.content-area-widgets' ).sortable({
				revert : true,
				stop : sortable_stop_function
			}).disableSelection( );
		}
	});
}

function save_area( ){
	var area_name = $( '#content-area-select' ).val( );
	if( area_name == '---' )
		return;
	var area_content = { 'all' : [ ] };
	$( '.content-area-widgets .widget' ).each(function(){
		var type = $( this ).attr( 'type' );
		area_content[ 'all' ].push( type );
	});
	$.ajax({
		url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/admin/save-area.php',
		type : 'POST',
		data : {
			name : area_name,
			content : area_content
		}
	});
}

function sortable_stop_function( event, ui ){
	if( ui.item.hasClass( 'widget-left' ) ){
		ui.item.removeClass( 'widget-left' );
		ui.item.addClass( 'widget' );
		// add to database
		save_area( );
	}
}
