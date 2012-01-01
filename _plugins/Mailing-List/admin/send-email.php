<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
	exit;

$email = @$_POST[ 'email' ];
$subject = @$_POST[ 'subject' ];
$content = @$_POST[ 'content' ];
$options = options( 'mailing_list_options' );

/**
 * set up mail headers 
 */
$headers='From: '. $options[ 'from' ] . "\r\n".'Reply-To: ' . $options[ 'reply_to' ] ."\r\n";
$headers.='X-Mailer: PHP/' .phpversion()."\r\n";
$headers.='MIME-Version: 1.0'."\r\n";
$headers.='Content-Type: text/html; charset=ISO-8859-1'."\r\n";

$sent = mail( $email, $subject, $content, $headers );

if( $sent )
	die( print( $email ) );
die( 'failed' );

?>
