<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->loadCSS( '_plugins/Content-Areas/style.css' );
$ContentAreas = ContentAreas::getInstance( );

$content = '
<div id="content-areas">
	<iframe id="content-right" src="' . SITEURL . '"></iframe>
';

$content .= '
	</div>
	<div id="content-left">
		<div id="widgets">
			<h2>Widgets</h2>';

	foreach( $ContentAreas->widgets( ) as $widget => $functions ){
		$content .= '<div id="widget-' . $widget . '" class="widget th">';
		$content .= '<span>' . $widget . '</span>';
		$content .= '</div>';
	}

$content .= '
	</div>
	</div>
	<br style="clear:both"/>
</div>';

$Template->add( 'title', 'Content Areas' );
$Template->add( 'content', $content );

?>
