/**
 * List Pages Javascript, Furasta.Org
 *
 * javascript for list pages in the admin area
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * add all events and stuff on document ready
 */
$(function( ){
	var draggedItem;
	$( '.p-submit' ).click( submit_multiple );
	$( '.checkbox-all' ).click( checkbox_toggle );
	$( '.delete' ).live( 'click', delete_page );	
	$( '#treeTable-toggle' ).click( treetable_toggle );
	$( '#pages tbody' ).sortable( options ).disableSelection( );
	$( '#pages' ).tree( );
	$( '.expander' ).live( 'click', rowColor );
	$( '#help-reorder' ).click( function( ){ fHelp( trans( 'pages_help_reorder' ) ) } );
});

/**
 * options
 *
 * sortable plugin options
 */
var options = {
	forcePlaceholderSize: true,
	placeholder: 'holder',
	cancel: '.not-sortable',
	helper:	sortableHelper,
	start: sortableStarter,
	sort: sortableSorter,
	stop: sortableStoper
}

/**
 * sortableHelper
 *
 * fixes the width of items being sorted
 */
function sortableHelper( e, ui ){
	ui.children( ).each( function( ){
		$( this ).width( $( this ).width( ) );
	});

	return ui;
}

/**
 * sortableStarter
 *
 * determines if page has children, if so it adds
 * them to the dragging, if the previous page is expanded
 * and its parent then its parent status is removed ( only
 * if it has no other children ). also checks if the item
 * being dragged is a child, then removed child identification
 * from it
 */
function sortableStarter( e, ui ){

	// set placeholder height
	$( '.holder' ).css( 'height', ui.item.height( ) );

	// keep track of mouse position to check if creating new
	// parent/child relationship
	draggedItem = ui.item;
	$( window ).mousemove( moved );

	/**
	 * if this is parent, drag children along as well
	 */
	if( $( ui.item ).hasClass( "parent" ) ){
		toggle_grouped_children( $( ui.item ).attr( "id" ) );
		ui.item.data( "i", $( ".grouped" ).index( ui.item ) );

		$( ".grouped:not(.holder)" ).not( ui.item ).each( function( ){
			var content = $( this );

			content.children( ).each( function( ){
				$( this ).width( $( this ).width( ) );
			});

			$( this ).data( "n", $( "tr:not(.holder)" ).index( this ) );
			content.appendTo( "#list-pages-dropbox" );

		});
								
		$( "#pages tbody" ).sortable( "refresh" );
							
		$( "#list-pages-dropbox" ).css( {'padding':0,'margin':0,'cellspacing':0}).show( );

		if( $( ui.item ).hasClass( 'expanded' ) )				
			$( ".holder" ).css( "height", ( ( $( "#list-pages-dropbox tr" ).length + 1 ) * ui.item.outerHeight( ) ) + "px" );

	}

	/**
	 * if this is child, remove child-of-node tag and indentation
	 */
	if( $( ui.item ).attr( 'class' ).match( /child-of-node\-.+?\b/ ) ){
		var classes = $( ui.item ).attr( "class" ).split( /\s+/ );

		for( var i = 0; i < classes.length; i++ ){
			if( classes[ i ].match( /child-of-node\-.+?\b/ ) )
				$( ui.item ).removeClass( classes[ i ] );
		}
	}

	/**
	 * if previous is expanded parent
	 */
	var prev = $( ui.item ).prev( );
	if( prev.hasClass( "parent" ) && prev.hasClass( "expanded" ) ){

		var prevParentID = $( ui.item ).prev( ).attr( "id" );
		prevParentID = String( prevParentID ).split( "-" );
		prevParentID = prevParentID[ prevParentID.length -1 ];

		/**
		 * if has no children left, then remove arrow and parent class 
		 */
		if( $( ".child-of-node-" + prevParentID ).length == 0 ){
			$( ui.item ).prev( ).removeClass( "parent" );
			$( "td.first span.expander", $( ui.item ).prev( ) ).remove( );
		}

	}
}

/**
 * sortableSorter
 *
 * makes sure the items being sorted stick together
 * in the same layout they have in the list
 */
function sortableSorter( e, ui ){
	if ( $( "#list-pages-dropbox tr" ).length ){
		var offset = ui.item.offset( );

		$( "#list-pages-dropbox" ).css({
			left: ( offset.left ) + "px",
			top: ( offset.top + 30 ) + "px"
		});
	}

	return ui;
}

/**
 * sortableStopper
 *
 * sorts the pages back into the list, using
 * reclassify item to apply their new status
 */
function sortableStoper( e, ui ){

	$( window ).unbind( 'mousemove', moved );

	/**
	 * if an element is being hovered on, then this must
	 * be a child of that element
	 */
	if( $( '.hovered' ).length == 1 ){
		var insertIndex = $( '.hovered' )[0].rowIndex;
		var parent = $( '#pages tr:eq(' + insertIndex + ')' );
		var parentID = parent.attr( 'id' ).split( '-' );
		var parentID = parentID[ parentID.length -1 ];
		ui.item.addClass( 'child-of-node-' + parentID ); 
		parent.addClass( 'parent' );
		parent.after( ui.item );
	}
	else if( ui.item.next( ).attr( 'class' ).match( /child-of-node\-.+?\b/ ) ){ // if next is child, this is child
		id = get_parent_id( ui.item.next( ) );
		ui.item.addClass( 'child-of-node-' + id );
	}

	if ( $( "#list-pages-dropbox tr" ).length ){

		$( "#list-pages-sortable" ).hide( );
		ui.item.after( $( "#list-pages-dropbox tr" ) );					

		var pos = ui.item.data( "i" );
		if ( pos > 0 ){
			ui.item.insertAfter( $( "#pages tbody .grouped" ) [ pos ] );
		}

		toggle_grouped_children( $( ui.item ).attr( "id" ) );
	}

	// remove hovered class from all elements ( added in moved function )
	$( '#pages tr' ).each( function( ){ $( this ).removeClass( 'hovered' ); } );

	sortableUpdate( );

	return ui;
}

/**
 * sortableUpdate
 *
 * the update events fixes the UI of the pages table,
 * changing the row colors to reflect any changes
 * and refreshing the padding of the treetable in case
 * of new/deleted parents/children
 */
function sortableUpdate( ){
	$( '#pages tbody' ).sortable( 'refresh' );
	$( '#pages' ).tree( 'refresh' );
	rowColor( );

	/**
	 * add pages to array of form array[ position ] = { id: page_id, parent: page_parent }
	 */
	var order = { };
	$( '#pages tr' ).not( '.not-sortable' ).each( function( e ){
		page_id = $( this ).attr( 'id' ).split( '-' );
		page_id = page_id[ page_id.length - 1 ];
		if( $( this ).attr( 'class' ).match( /child-of-node\-.+?\b/ ) ){
			page_parent = get_parent_id( $( this ) );
		}
		else
			page_parent = 0;
		order[ 'node[' + e + ']' ] = {
			id : page_id,
			parent : page_parent
		};
	});
	$.ajax({
		url : window.furasta.site.url + '_inc/ajax.php?file=admin/pages/reorder.php',
		data : order,
		type : 'post'
	});
}

/**
 * toggle_grouped_children
 *
 * provided with an id for the parent, this function
 * will toggle the state of all children of that
 * parent between grouped and not grouped
 */
function toggle_grouped_children( id ){
	$( "#" + id ).toggleClass( "grouped" );

	// get id from node-id syntax
	var splitID = String( id ).split( "-" );
	splitID = splitID[ splitID.length -1 ];
	var children = $( ".child-of-node-" + splitID );

	// reccursively execute function on all children
	if( children ){
	        children.each(function( ){
			$( this ).toggleClass( "grouped" );

                	if( $( this ).hasClass( "parent" ) ){
                        	var parent = $( this ).attr( "id" );
	                        parent = String( parent ).split( "-" );
        	                parent = parent[ parent.length - 1 ];
                	        toggle_grouped_children( parent );
	                }
        	});
	}
}

/**
 * moved
 *
 * tracks the cursor position and adds a class
 * hovered to items being hovered over during
 * sorting
 */
function moved( e ) {

	//Dragged item's position++
	var dpos = draggedItem.position();
	var d = {
		top: dpos.top + 20,
		bottom:    dpos.top + draggedItem.height() - 20,
		left: dpos.left,
		right: dpos.left + draggedItem.width()
	};

	//Find sortable elements (li's) covered by draggedItem
	var hoveredOver = $( '#pages tr' ).not('.not-sortable, .holder' ).not(draggedItem).filter(function() {
		var t = $(this);
		var pos = t.position();

		//This li's position++
		var p = {
			top: pos.top,
			bottom:    pos.top + t.height(),
			left: pos.left,
			right: pos.left + t.width()
		};

		//itc = intersect
		var itcTop         = p.top <= d.bottom;
		var itcBtm         = d.top <= p.bottom ;
		var itcLeft     = p.left <= d.right;
		var itcRight     = d.left <= p.right;

		return itcTop && itcBtm && itcLeft && itcRight;
	});
        
	$( '.holder' ).css( 'display', 'block' );
	$( '#pages tr' ).each( function( ){ $(this).removeClass( 'hovered' ) } );

	hoveredOver.each(function() {
		$( '.holder' ).css( 'display', 'none' );
		$( this ).addClass( 'hovered' );
	});
}

/**
 * treetable_toggle
 *
 * toggles whether the tree table is expanded
 * or collapsed
 */
function treetable_toggle( ){
	$( '#pages' ).tree( 'toggle' );
	rowColor( );
}

/**
 * get_parent_id
 *
 * given an object, this function will return
 * the id of its parent
 */
function get_parent_id( obj ){
	classes = obj.attr( 'class' ).split( ' ' );
	for( var i in classes ){
		if( classes[ i ].match( /child-of-node\-.+?\b/ ) ){
			id = classes[ i ].split( '-' );
			return id[ id.length - 1 ];
		}
	}
	return 0;
}

/**
 * delete_page
 *
 * runs the event for deleting a page
 */
function delete_page( ){

	fConfirm( trans( 'prompt_confirm_delete' ), function( element ){

		fetch( "/_inc/ajax.php?file=admin/pages/delete.php&id="+element.attr("id"), function( element, html ){
			if( html == "perm" )
				fAlert( trans( 'prompt_insufficient_privellages' ) );
			else{
				element.parent().parent().fadeOut( "slow", function( ){
					$( this ).remove( );
					rowColor( );
				});
			}
		}, element );
	}, $( this ) );
}

/**
 * checkbox_toggle
 *
 * toggles all checkboxes on the page switching
 * between state checked and not checked
 */
function checkbox_toggle( ){

	if($(".checkbox-all").attr("all")=="checked"){
		$("input[type=checkbox]").attr("checked","");
		$(".checkbox-all").attr("all","");
	}
	else{
		$("input[type=checkbox]").attr("checked","checked");
		$(".checkbox-all").attr("all","checked");
	}       

}

/**
 * submit_multiple
 *
 * the event that triggers to submit the form
 * that performs mutiple trash actions on pages
 */
function submit_multiple( ){

	var action=$(".select-"+$(this).attr("id")).val();

	if(action=="---")
		return false;

	var boxes=[];

	$("#pages input[name=trash-box]:checked").each(function() {
		boxes.push($(this).val());
	});

	var boxes=boxes.join(",");

	if(boxes=="")
		return false;

	fConfirm(
		trans( 'prompt_confirm_multiple' ),
		function( ){
			window.location = "pages.php?page=list&action=multiple&act="+action+"&boxes="+boxes;
		}
	);
}
