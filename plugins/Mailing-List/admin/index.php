<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$mailing_list_options = options( 'mailing_list_options' );

$page = @$_GET[ 'page' ];

switch( $page ){
	case 'options':
		require HOME . '_plugins/Mailing-List/admin/options.php';
	break;
	case 'mail':
	case 'send':
		$conds = array(
			'Subject' => array(
				'name' => $Template->e( 'mailing_list_subject' ),
				'required' => true
			),
			'BCC' => array(
				'name' => $Template->e( 'mailing_list_bcc' ),
				'required' => true
			),
			'Content' => array(
				'name' => $Template->e( 'mailing_list_content' ),
				'required' => true
			)
		);

		$valid = validate( $conds, '#mail-form', 'mail-form-submit' );

		if( $page == 'send' )
			require HOME . '_plugins/Mailing-List/admin/send.php';
		else
			require HOME . '_plugins/Mailing-List/admin/mail.php';
	break;
	default:
		require HOME . '_plugins/Mailing-List/admin/list.php';
}

?>
