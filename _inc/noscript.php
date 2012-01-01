<?php

/**
 * No Javascript File, Furasta.Org
 *
 * Diplays an error message when no javascript is detected in the admin area.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

require 'define.php';

$Template = Template::getInstance( );

error(
	$Template->e( 'error_javascript_body' ),
	$Template->e( 'error_javascript_title' )
);

?>
