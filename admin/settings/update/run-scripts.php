<?php

/**
 * Run Update Scripts, Furasta.Org
 *
 * Runs whatever script is contained in the update package.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_settings
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        exit;

$script=HOME.'_inc/update.php';

if(!file_exists($script))
	die( 'ok' );

require $script;

cache_clear();

unlink(USERS_DIR.'update.zip');

die('ok');
?>
