<?php

/**
 * Install Header, Furasta.Org
 *
 * Header which is loaded on all the install pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package	   installer
 */

/**
 * get the home dir 
 */
define( 'HOME', substr( dirname( __FILE__ ), 0, -7) );
define( 'SITEURL', calculate_url( ) );

/**
 * cannot install if .settings.php exists
 */
if( file_exists( HOME . '.settings.php' ) ){
	require HOME . '_inc/define.php';
	error( 'You can\'t install Furasta CMS because it is already installed. If you would like to re-install then simply remove the <i>../.settings.php</i> file and reload this page.', 'Already Installed!' );
}

/**
 * start session 
 */
session_start( );

/**
 * load javascript 
 */
echo '
<html>
<head>
<noscript><meta http-equiv="refresh" content="0;url=../_inc/noscript.php"></noscript>
<title>Furasta.Org Installation</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script type="text/javascript" src="../_inc/js/jquery/validate.js"></script>
<script type="text/javascript" src="install.js"></script>
<link rel="stylesheet" href="../_inc/css/admin.css" type="text/css"/>
</head>
<body>
<div id="dialog">&nbsp;</div>
<div id="wrapper">
        <div id="top">
                <div id="right-error">&nbsp;</div>
        </div>
        <div id="header">
                <div id="menu">
                        <ul>
                                <li><a href="../" id="title">&nbsp;</a></li>
                        </ul>
                </div>
        </div>
        <div id="container">
                <div id="container-right">
                        <div id="main">
                                        <div id="right">';

/**
 * calculate_url
 *
 * Accurately calculates the URL where the CMS
 * is being installed. Removes the install
 * dir and the file name from the URL, it also
 * has support for https and different ports.
 * 
 * @access public
 * @return string
 */
function calculate_url( ){
        $url = 'http';

        if( @$_SERVER[ 'HTTPS' ] == 'on' )
                $url .= 's';

        $url .= '://' . $_SERVER[ 'SERVER_NAME' ];

        if( $_SERVER[ 'SERVER_PORT' ] != '80' )
                $url .= ':' . $_SERVER[ 'SERVER_PORT' ];

        $end = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
        array_pop( $end );
        $url .= implode( '/', $end );

        $url = substr( $url, 0, -7 );

        return $url;
}

?>
