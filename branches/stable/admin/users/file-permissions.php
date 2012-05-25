<?php

/**
 * Group File Permissions, Furasta.Org
 *
 * Accessed via AJAX, this page returns page permissions
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

if( ( int ) @$_GET[ 'id' ] != 0 ){
	$id = $_GET[ 'id' ];
	$perms = single( 'select perm from ' . PAGES . ' where id=' . $id, 'perm' );
	$perms = explode( '|', $perms );

	if( isset( $perms[ 0 ] ) && !empty( $perms[ 0 ] ) ){

               if( strpos( $perms[ 0 ], "#" ) !== false ){

                        $see = explode( '#', $perms[ 0 ] );

                        /**
                         *  users and groups arrays
                         */
                        $see_users = explode( ',', $see[ 0 ] );

                        $see_groups = explode( ',', $see[ 1 ] );


                }
                else
                        $see_users = explode( ',', $perms[ 0 ] );

	}

        if( isset( $perms[ 1 ] ) && !empty( $perms[ 1 ] ) ){

               if( strpos( $perms[ 1 ], "#" ) !== false ){

                        $edit = explode( '#', $perms[ 1 ] );

                        /**
                         *  users and groups arrays
                         */
                        $edit_users = explode( ',', $edit[ 0 ] );
                        
                        $edi_groups = explode( ',', $edit[ 1 ] );


                }
                else    
                        $edit_users = explode( ',', $perms[ 1 ] );

	}

}

$groups = rows( 'select id,name from ' . GROUPS );
$users = rows( 'select id,name,user_group from ' . USERS );

for( $n = 0; $n < count( $groups ); $n++ ){

	for( $i = 0; $i < count( $users ); $i++ ){

		if( $users[ $i ][ 'user_group' ] == $groups[ $n ][ 'id' ] )
			$groups[ $n ][ 'users' ][ $users[ $i ][ 'id' ] ] = $users[ $i ][ 'name' ];
	}

}

$Template = Template::getInstance( );

$javascript =  '
/**
 * toggleAttrs
 *  
 * internal function for toggling checked/disabled
 * attributes on certain elements
 * 
 * @param string selector  
 * @access public
 * @return void
 */
function toggleAttrs( selector ){

	if( $( selector ).hasClass( "toggled" ) ){
		$( "input", $( selector ).parent( ).next( ) ).each( function( ){

			$( this ).attr( "checked", "" );
			$( this ).attr( "disabled", "" );

		} );

	}
	else{
		$( "input", $( selector ).parent( ).next( ) ).each( function( ){

			$( this ).attr( "disabled", "disabled" );
			$( this ).attr( "checked", "checked" );

		} );

	}

	$( selector ).toggleClass( "toggled" );

}

$( document ).ready( function( ){
	rowColor( );

        /**
         * page permissions radio button
         * this code changes the checkboxes
         * of groups to be disabled when
         * unavailable 
         */
        $( "input[name=\'who-can-see\']" ).change( function( ){

                toggleDisabled( "input[name=\'see-groups\']" );

		toggleDisabled( "input[name=\'see-users\']" );

		$( ".perms-see" ).toggleClass( "opacity" );

        });

        $( "input[name=\'who-can-edit\']" ).change( function( ){

                toggleDisabled( "input[name=\'edit-groups\']" );

                toggleDisabled( "input[name=\'edit-users\']" );

                $( ".perms-edit" ).toggleClass( "opacity" );

        });

	$( ".groups" ).click( function( ){

		toggleAttrs( $( this ) );

	});

	/**
	 * set up tabs
	 */
	$("#tabbed").tabs( );

	/**
	 * The remainder if for setting initial checked/disabled values
	 */

	var edit = "' . @$edit_users[ 0 ] . @$edit_groups[ 0 ] . '";
	var see = "' . @$see_users[ 0 ] . @$see_groups[ 0 ] . '";

	if( edit == "" ){

		$( ".edit-everyone" ).attr( "checked", "checked" );

		toggleDisabled( $( "input[name=\'edit-users\']" ) );

                $( "input[name=\'edit-groups\']" ).each( function( ){ $( this ).attr( "disabled", "disabled" ); });

                $( ".perms-edit" ).addClass( "opacity" );

	}
	else{
		$( ".edit-selected" ).attr( "checked", "checked" );

		/**
		 * toggle users of selected groups
		 */
		$( "input[name=\'edit-groups\']:checked" ).each( function( ){

			toggleAttrs( $( this ) );

		} );

	}

        if( see == "" ){

                $( ".see-everyone" ).attr( "checked", "checked" );

                toggleDisabled( $( "input[name=\'see-users\']" ) );

		$( "input[name=\'see-groups\']" ).each( function( ){ $( this ).attr( "disabled", "disabled" ); });

                $( ".perms-see" ).addClass( "opacity" );

        }
        else{
                $( ".see-selected" ).attr( "checked", "checked" );

                /**
                 * toggle users of selected groups
                 */
                $( "input[name=\'see-groups\']:checked" ).each( function( ){

                        toggleAttrs( $( this ) );

                } );
	}
	

});';

$Template->loadJavascript( 'FURASTA_ADMIN_PAGES_PERMISSIONS', $javascript );

$content = '
<div id="tabbed">
        <ul>
                <li><a href="#tabs-1">View Files</a></li>
                <li><a href="#tabs-2">Edit Files</a></li>
        </ul>
        <div id="tabs-1" style="border:none">
                <p style="margin:1.5%"><i>Who can view users/groups private files:</i></p>
		<p style="margin:1.5%"><i>Select by User or by Group</i></p>
                <table id="perms-table" class="row-color perms-edit">
                        <tr><th>' . $Template->e( 'groups' ) . '</th><th>' . $Template->e( 'users' ) . '</th></tr>
';

for( $i = 0; $i < count( $groups ); $i++ ){

	$checked = ( isset( $edit_groups ) && in_array( $groups[ $i ][ 'id' ], $edit_groups ) ) ? ' checked="checked"' : '';

        $content .= '<tr>
                        <td style="border-right:1px solid #999;width:30%"><input type="checkbox"' . $checked . ' class="checkbox groups" name="edit-groups" value="' . $groups[ $i ][ 'id' ] . '"
/> ' . $groups[ $i ][ 'name' ] . '</td>
                        <td><ul style="list-style-type:none"';

        foreach( $groups[ $i ][ 'users' ] as $id => $name ){

		$checked = ( isset( $edit_users ) && in_array( $id, $edit_users ) ) ? ' checked="checked"' : '';

                $content .= '  <li style="float:left"><input ' . $checked . ' type="checkbox" class="checkbox" name="edit-users" value="' . $id . '"/> ' . $name . '</li>';
        }
        $content .= '</td></tr>';

}

$content .= '          </table>
        </div>
        <div id="tabs-2" style="border:none">
                <p style="margin:1.5%"><i>Who can edit users/groups public and private files:</i></p>
                <p style="margin:1.5%"><i>Select by User or by Group</i></p>
		<table id="perms-table" class="row-color perms-see">
			<tr><th>' . $Template->e( 'groups' ) . '</th><th>' . $Template->e( 'users' ) . '</th></tr>
';

for( $i = 0; $i < count( $groups ); $i++ ){

        $checked = ( isset( $see_groups ) && in_array( $groups[ $i ][ 'id' ], $see_groups ) ) ? ' checked="checked"' : '';

        $content .= '<tr>
                        <td style="border-right:1px solid #999;width:30%"><input type="checkbox"' . $checked . ' class="checkbox groups" name="see-groups" value="' . $groups[ $i ][ 'id' ] . '"
/> ' . $groups[ $i ][ 'name' ] . '</td>
                        <td><ul style="list-style-type:none"';

        foreach( $groups[ $i ][ 'users' ] as $id => $name ){

                $checked = ( isset( $see ) && in_array( $id, $see ) ) ? ' checked="checked"' : '';

                $content .= '  <li style="float:left"><input ' . $checked . ' type="checkbox" class="checkbox" name="see-users" value="' . $id . '"/> ' . $name . '</li>';
        }
        $content .= '</td></tr>';

}

$content .= '          </table>
        </div>
</div>
';

$Template->add( 'content', $content );

?>
