<?php

/**
 * Login Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Login',
	'description' => 'Allows users to login/register through a content areas widget',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

  'admin' => array( 
    'content_area_widgets' => array(
      array(
        'name' => 'Login',
        'function' => 'login_admin_widget'
      )
    )
  ),

  'admin' => array( 
    'content_area_widgets' => array(
      array(
        'name' => 'Login',
        'function' => 'login_frontend_widget'
      )
    )
  )

);


/**
 * login_admin_widget
 *
 * loads the admin widget for the login
 * plugin
 *
 * @return void
 */
function login_admin_widget( $area_name, $widget_id, $pages ){
  // configuration options for plugin for example enable/disable facebook/google login
}

/**
 * login_frontend_widget
 *
 * loads the frontend widget for the
 * login plugin
 *
 * @return void
 */
function login_frontend_widget( ){
  // check user login, if not logged in show login page
}
?>
