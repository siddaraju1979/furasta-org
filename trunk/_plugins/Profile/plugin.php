<?php

/**
 * Profile Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Profile',
	'description' => 'Adds a frontend profile for users and allows them to update it through a frontend interface.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'page_type' => array(
			'name' => 'Profile',
			'function' => 'profile_admin_page_type'
		)
	),

	'frontend' => array(
		'page_type' => 'profile_frontend_page_type'
	)
);

/**
 * profile_admin_page_type
 *
 * loads the admin page type for the profile plugin
 *
 * @params int $id
 * @return void 
 */
function blog_admin_page_type( $id ){
	require HOME . '_plugins/Profile/admin/page-type.php';
}

/**
 * profile_frontend_page_type
 *
 * load the frontend page type for the profile plugin
 * 
 * @params int $id
 * @return void
 */
function blog_frontend_page_type( $id ){
	require HOME . '_plugins/Blog/Profile/page-type.php';
}

?>
