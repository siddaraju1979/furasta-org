/**
 * Edit User Javascript, Furasta.Org
 *
 * javascript for the edit user page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_users
 */

$( document ).ready( function( ){

	$( "#tabs" ).tabs( );

	$( "#change-password" ).click( function( ){

		fConfirm( "To reset the password an email will be sent to " + window.furasta.user.email + " with reset details. Press \'Yes\' to confirm.", function( ){

			fetch( window.furasta.site.url + "ajax.php?file=admin/users/reminder.php&no_config&email=" . window.furasta.user.email );

		} );

	});

});
