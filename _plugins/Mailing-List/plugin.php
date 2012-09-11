<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Mailing List',
	'description' => 'Adds a Mailing List widget which collects emails. Allows the sending of emails to the database of subscribers.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_menu' => 'mailing_list_filter_menu',
		'filter_lang' => 'mailing_list_lang',
		'page' => 'mailing_list_admin',
		'content_area_widgets' => array(
			array(
				'name' => 'Mailing List',
				'function' => 'mailing_list_admin_widget_mailing_list'
			)
		)
	),

	'frontend' => array(
		'content_area_widgets' => array(
			array(
				'name' => 'Mailing List',
				'function' => 'mailing_list_frontend_widget_mailing_list'
			)
		)
	)
);

/**
 * mailing_list_filter_menu
 *
 * adds the mailing list pane to the admin menu
 * 
 * @params string $menu
 * @params string $url
 * @return string
 */
function mailing_list_filter_menu( $items, $url ){

	$Template = Template::getInstance( );

	$menu = array( );

	foreach( $items as $item => $val ){
		if( $item == 'menu_users_groups' ){
			$menu[ 'menu_mailing_list' ] = array(
				'name' => $Template->e( 'menu_mailing_list' ),
				'url' => $url,
				'submenu' => array(
					'menu_mailing_list_subscribers' => array(
						'name' => $Template->e( 'menu_mailing_list_subscribers' ),
						'url' => $url . 'page=list',
						'image' => SITE_URL . '_plugins/Mailing-List/img/list.png'
					),
					'menu_mailing_send_mail' => array(
						'name' => $Template->e( 'menu_mailing_send_mail' ),
						'url' => $url . 'page=mail',
						'image' => SITE_URL . '_plugins/Mailing-List/img/send.png'
					),
					'menu_mailing_options' => array(
						'name' => $Template->e( 'menu_mailing_options' ),
						'url' => $url . 'page=options',	
						'image' => SITE_URL . '_plugins/Mailing-List/img/options.png'
					)
				)
			);
		}
		$menu[ $item ] = $val;
	}
		
	return $menu;
}

/**
 * mailing_list_admin
 *
 * echos the admin area content areas page
 */
function mailing_list_admin( ){
	require HOME . '_plugins/Mailing-List/admin/index.php';
}

/**
 * mailing_list_lang
 *
 * handles languages for the mailing list
 * plugin
 * 
 * @params array $lang
 * @return array
 */
function mailing_list_lang( $lang ){
	switch( LANG ){
		case 'Gaeilge' :
			$new_lang = array(
			);
		break;
		default:
			$new_lang = array(
				// menu
				'menu_mailing_list' => 'Mailing List',
				'menu_mailing_list_subscribers' => 'List Subscribers',
				'menu_mailing_send_mail' => 'Send Mail',
				'menu_mailing_options' => 'Options',

				// list page
				'mailing_list_no_subscribers' => 'No subscribers yet!',
				'mailing_list_add_subscriber' => 'Add Subscriber',

				// send page
				'mailing_list_subject' => 'Subject',
				'mailing_list_content' => 'Content',
				'mailing_list_show_bcc' => 'Show BCC',
				'mailing_list_hide_bcc' => 'Hide BCC',
				'mailing_list_bcc' => 'BCC',
				'mailing_list_sending_email' => 'Sending email to recepients..',
				'mailing_list_sent_to' => 'Sent to',
				'mailing_list_sending_complete' => 'Sending Complete',
				'mailing_list_sending_failed' => 'Sending Failed',

				// options
				'mailing_list_options_general' => 'General',
				'mailing_list_options_template' => 'Email Template',
				'mailing_list_options_from' => 'From Address',
				'mailing_list_options_reply_to' => 'Reply-to Address',
				'mailing_list_options_template_description' => 'You can create a template here which will appear in the content section on the send mail page when you are sending an email.',

				// widget				
				'mailing_list_widget_name' => 'Collect Subscriber Names'
			);
	}

	return array_merge( $lang, $new_lang );
}

/**
 * mailing_list_admin_widget_mailing_list
 *
 * adds widget options to the admin area
 *
 * @params int $id
 * @return void
 */
function mailing_list_admin_widget_mailing_list( $area_name, $widget_id, $pages ){
	require HOME . '_plugins/Mailing-List/admin/widget.php';
}

function mailing_list_frontend_widget_mailing_list( ){
	echo '<h2>Mailing List</h2>';
	echo '<div id="mailing-list">
		<input type="text" style="border:1px solid #000;padding:4px;width:150px" value="Enter your email"/>
		<input type="submit" style="width:30px" value="Go"/>
	</div>';
}
?>
