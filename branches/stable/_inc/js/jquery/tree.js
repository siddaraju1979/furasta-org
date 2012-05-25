/**
 * jQuery Tree Plugin
 *
 * Designed specifically for furasta.org based loosely
 * on the jQuery treeTable plugin located here:
 * http://ludo.cubicphuse.nl/jquery-plugins/treeTable/doc/
 *
 * @author Conor Mac Aoidh <conormacaoidh@gmail.com>
 */
(function($){

	$.fn.tree = function( command ){
		Tree.obj = this;
		switch( command ){
			case 'refresh':
				return Tree.refresh( );
			case 'toggle':
				return Tree.toggleExpand( );
		}

		// add events and return Tree.refresh( )
		$( '.expander' ).live( 'click', function( ){
			id = $( this ).parent( ).parent( ).attr( 'id' ).split( '-' );
			id = id[ id.length - 1 ];
			Tree.toggleExpand( id );
		});

		return Tree.refresh( );
	};

	var Tree = {
		obj : null,
		refresh : function( ){
			$( 'tr', this.obj ).not( '.not-sortable' ).each( function( ){
				if( !$( this ).attr( 'class' ).match( /child-of-node\-.+?\b/ ) ){
					if( $( this ).hasClass( 'parent' ) ){
						if( $( 'td:eq(1) span.expander', this ).length == 0 ){
							$( 'td:eq(1)', this ).prepend(
								'<span class="expander" style="margin-left: -19px; padding-left: 19px;"></span>'
							);
						}
						var parentid = $( this ).attr( 'id' ).split( '-' );
						parentid = parentid[ parentid.length - 1 ];
						Tree.updateChildren( parentid, 25 );
					}
					else{ // set padding to 0
						$( 'td:eq(1)', this ).css( 'padding-left', 6 );
					}
				}
			});
		},
		updateChildren : function( id, padding ){
			$( 'tr.child-of-node-' + id, this.obj ).each( function( ){
				$( 'td:eq(1)', this ).css( 'padding-left', padding );

				if( $( this ).hasClass( 'parent' ) ){
					if( $( 'td:eq(1) span.expander', this ).length == 0 ){
						$( 'td:eq(1)', this ).prepend(
							'<span class="expander" style="margin-left: -19px; padding-left: 19px;"></span>'
						);
					}
					var parentid = $( this ).attr( 'id' ).split( '-' );
					parentid = parentid[ parentid.length - 1 ];
					Tree.updateChildren( parentid, ( padding + 25 ) );
				}
			});
		},
		toggleExpand : function( id, expand ){
			if( id ){
				// toggle expanded/collapsed class to change image
				if( expand )
					$( '#node-' + id ).addClass( 'expanded' );
				else
					$( '#node-' + id ).toggleClass( 'expanded' ).toggleClass( 'collapsed' );
				if( arguments.length == 1 )
					expand = $( '#node-' + id ).hasClass( 'expanded' );
				// make all children visible / not visible
				$( '.child-of-node-' + id, this.obj ).each( function( ){
					$( this ).toggle( expand );
					if( $( this ).hasClass( 'parent' ) ){	
						id = $( this ).attr( 'id' ).split( '-' );
						id = id[ id.length - 1 ];
						Tree.toggleExpand( id, expand );
					}
				});
			}
			else{
				// toggle visibility of every element
				$( 'tr', this.obj ).not( '.not-sortable' ).each( function( ){
					if( $( this ).hasClass( 'parent' ) ){
						$( this ).toggleClass( 'expanded' ).toggleClass( 'collapsed' );
					}
					if( $( this ).attr( 'class' ).match( /child-of-node\-.+?\b/ ) ){ 
						$( this ).toggle( );
					}
				});
			}
			rowColor( );
		}
	};

})(jQuery);
