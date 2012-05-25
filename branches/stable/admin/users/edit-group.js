/**
 * Edit Group Javascript, Furasta.Org
 *
 * javascript for the edit group page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$( document ).ready( function( ){

	$( "#users-edit" ).submit( function( ){

		var perms_array = [];

		$( "input[name=\'perms\']:checked" ).each( function( i ){

			perms_array[ i ] = $( this ).val( );

		});

		perms_array = perms_array.join( "," );

		$( "input[ name=\'perm\' ]" ).val( perms_array );

	});

});
