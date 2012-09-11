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

/**
 * load javascript and CSS files 
 */
$Template->loadJavascript( '_inc/js/jquery/multi-ddm.min.js' );
$Template->loadJavascript( '_inc/js/admin/admin.js' );
$Template->loadJavascript( '_inc/js/system.js' );
$Template->loadJavascript( '_inc/js/jquery/validate.js' );

$Template->loadCSS( '_inc/css/admin.css' );

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
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<noscript><meta http-equiv="refresh" content="0;url=' . SITE_URL . '_inc/noscript.php"></noscript>';

foreach( $Template->cssUrls( ) as $url )
	echo '<link rel="stylesheet" href="' . $url . '"/>' . "\n";

echo '
	<title>' . $Template->display( 'title' ) . ' - Furasta.Org</title>

	' . $Template->display( 'head' ) . '
</head>
<body>
<div id="dialog">&nbsp;</div>
	<div id="wrapper">
		<div id="top" class="system">
			<div id="top-wrapper">
	                        <div id="title"><a href="settings.php?page=general">'.$SETTINGS['site_title'].'</a><br/><span id="subtitle">'.$SETTINGS['site_subtitle'].'</span></div>
				<div id="right">&nbsp;</div>
			</div>
		</div>
		<div id="header">
			<div id="menu">
				<ul id="full-menu">
                                        <li><a href="../" id="title">&nbsp;</a></li>
                                        '.$Template->display('menu').'
                                        <li id="right-item"><a href="logout.php" id="logout">&nbsp;</a></li>
				</ul>
			</div>
		</div>
		<div id="container">
			<div id="container-right">
				<div id="main">
					' . $Template->displayErrors( ) . '
					<div id="right">
						'. $content .'
					</div>
				</div>
			</div>
			<div id="footer">
				<img src="' . SITE_URL . '_inc/img/footer-right.png" style="float:right"/>
				<img src="' . SITE_URL . '_inc/img/footer-left.png"/>
			</div>
		</div>
		<div id="bottom">
                        <p class="right"><a href="settings.php?page=edit-users&id=' . $User->id( ) . '">'. $User->name( ) .'</a> - ' 
			. $Template->e( 'group' ) . 's: ';

// print user groups
$gs = array( );
$Groups = $User->groups( );
foreach( $Groups as $id ){
	$Group = Group::getInstance( $id );
	array_push( $gs, '<a href="settings.php?page=edit-groups&id=' . $Group->id( ) .'">'. $Group->name( ) . '</a>' );
}
echo implode( ',', $gs );

echo '
			</p>
			<p class="left">&copy; <a href="http://furasta.org">Furasta.Org</a> | <a href="http://forum.furasta.org">' .
			$Template->e( 'forum' ) . '</a> | <a href="http://bugs.furasta.org">
			' . $Template->e( 'bug_tracker' ) . '</a></p>
			<br style="clear:both"/>
		</div>
	</div>
</body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
';

foreach( $Template->javascriptUrls( ) as $url )
	echo '<script type="text/javascript" src="' . $url . '"></script>' . "\n";

echo '
<script type="text/javascript">
' . $Template->display( 'javascript' ) . '
</script>
</html>';

?>
