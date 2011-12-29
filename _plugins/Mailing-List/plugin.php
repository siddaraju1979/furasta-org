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
function mailing_list_filter_menu( $menu, $url ){

	$menu[ 'menu_mailing_list' ] = array( 'name' => 'Mailing List', 'url' => $url );

	return $menu;
}

/**
 * mailing_list_admin
 *
 * echos the admin area content areas page
 */
function mailing_list_admin( ){
	echo 'test';
}

function mailing_list_admin_widget_mailing_list( ){
	$Template = Template::getInstance( );
	$Template->add( 'content', 'test' );
}

function mailing_list_frontend_widget_mailing_list( ){
	echo '<h2>Mailing List</h2>';
	echo '<div id="mailing-list">
		<input type="text" style="border:1px solid #000;padding:4px;width:150px" value="Enter your email"/>
		<input type="submit" style="width:30px" value="Go"/>
	</div>';
}
?>
