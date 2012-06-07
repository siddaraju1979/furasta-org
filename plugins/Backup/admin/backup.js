/**
 * Backup Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	$( '#tabs' ).tabs();
	$( 'input[name="restore"]' ).click( function( ){
		fConfirm( 'Are you sure you wish to restore from a backup? If the file you have selected to upload'
		 + ' is incorrect it could damage the system.', function( ){
			$( '#backup-form' ).trigger( 'submit' );
		} );
		return false;
	});
});
