<?php

/**
 * Admin Footer, Furasta.Org
 *
 * Admin footer which is loaded on every page in the
 * admin area.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * capture all output so far, add to template class
 */
$Template->add( 'content', ob_get_contents( ) );

/**
 * start output
 */
header('Connection: close');
header('Content-type: text/html; charset: UTF-8');

require $admin_dir . 'layout/admin.php';

/**
 * echo output, cancel request connection,
 * execute admin on finish
 */
ignore_user_abort( true );
ob_end_flush();
flush();

// execute on finish plugin code
$Plugins->hook( 'admin', 'on_finish' );
?>
