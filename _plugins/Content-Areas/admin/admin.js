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
	$( '.edit' ).live( 'click', edit_widget );
	$( '.delete' ).live( 'click', delete_widget );


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
		var a_id = $( this ).attr( 'id' );
		area_content[ 'all' ].push( { name : type, id : a_id } );
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
		var new_id = 1;
		$( '.widget' ).each(function( ){
			var id = parseInt( $( this ).attr( 'id' ) );
			if( id >= new_id )
				new_id = id + 1;
		});
		ui.item.attr( 'id', new_id );
		ui.item.prepend( 
			'<span class="right"><a class="link edit">'
			+ trans( 'edit' ) + 
			'</a><a class="link delete">'
			+ trans( 'delete' ) +
			'</a></span>'
		);
		ui.item.addClass( 'widget' );
	}
	save_area( );
}

function edit_widget( ){
	$this = $( this ).parent( ).parent( );
	var widget_name = $this.attr( 'type' );
	var widget_id = $this.attr( 'id' );
	var a_name = $( '#content-area-select' ).val( );
	$( '#dialog' )
		.attr( 'title', widget_name + ' Widget' )
		.html(
			trans( 'loading' ) + '... <img src="' + 
			window.furasta.site.url + '_inc/img/loading.gif"/>'
		)
		.dialog({
			width : '80%',
			height : 400,
			modal : true
		});
	$.ajax({
		url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/admin/widget-content.php',
		type : 'POST',
		data : {
			name : widget_name,
			area_name : a_name,
			id : widget_id
		},
		success : function( ht ){
			$( '#dialog' ).html( ht );
		}
	});
}

function delete_widget( ){
	var par = $( this ).parent( ).parent( );
	var widget_id = par.attr( 'id' );
	var area_name = $( '#content-area-select' ).val( );
	fConfirm( trans( "content_areas_confirm_delete" ), function( ){
		$.ajax({
			url : window.furasta.site.url + '_inc/ajax.php?file=_plugins/Content-Areas/admin/delete-widget.php',
			type : 'POST',
			data : {
				name : area_name,
				id : widget_id
			},
			success : function( ){
				par.fadeOut( "fast" );
			}
		});
	});
}
