<?php

/**
 * Admin Template, Furasta.Org
 *
 * This file contains the template for the admin area. Info is stored
 * in the $Template class during execution and then displayed here.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_template
 */

ob_start('ob_gzhandler');
header('Content-type: text/html; charset: UTF-8');

/**
 * load javascript and CSS files 
 */
$Template->loadJavascript( '_inc/js/jquery/multi-ddm.min.js' );
$Template->loadJavascript( '_inc/js/admin/admin.js' );
$Template->loadJavascript( '_inc/js/system.js' );
$Template->loadJavascript( '_inc/js/jquery/validate.js' );

$Template->loadCSS('_inc/css/admin.css');

/**
 * filter page content - plugins 
 */
$content = $Plugins->filter( 'admin', 'filter_page_content', $Template->display( 'content' ) );

/**
 * begin output 
 */
echo'
<html>
<head>
<noscript><meta http-equiv="refresh" content="0;url=' . SITEURL . '_inc/noscript.php"></noscript>';

foreach( $Template->cssUrls( ) as $url )
	echo '<link rel="stylesheet" href="' . $url . '"/>';

echo '
	<title>' . $Template->display( 'title' ) . ' - Furasta.Org</title>

	' . $Template->display( 'head' ) . '
</head>
<body>
<div id="dialog">&nbsp;</div>
	<div id="wrapper">
		<div id="header">
			<div id="menu">
				<ul id="full-menu">
                                        <li><a href="../" id="title">&nbsp;</a></li>
                                        '.$Template->display('menu').'
                                        <li id="right-item"><a href="logout.php" id="logout">&nbsp;</a></li>
				</ul>
			</div>
		</div>
		<div id="content-areas-content">
			' . $Template->displayErrors( ) . '
			<div id="right">
				'. $content .'
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
';

foreach( $Template->javascriptUrls( ) as $url )
	echo '<script type="text/javascript" src="' . $url . '"></script>';

echo '
<script type="text/javascript">
' . $Template->display( 'javascript' ) . '
</script>
</html>';

ob_end_flush();
?>
