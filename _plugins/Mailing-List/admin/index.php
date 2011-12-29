<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$mailing_list_options = options( 'mailing_list_options' );

$page = @$_GET[ 'page' ];

switch( $page ){
	case 'options':
		require HOME . '_plugins/Mailing-List/admin/options.php';
	break;
	case 'mail':
		require HOME . '_plugins/Mailing-List/admin/mail.php';
	break;
	default:
		require HOME . '_plugins/Mailing-List/admin/list.php';
}

?>
