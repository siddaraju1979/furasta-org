<?php

/**
 * Stats Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

$Template = Template::getInstance( );
$Template->add( 'title', 'Stats' );
$Template->loadCSS( '_plugins/Stats/admin/layout.css' );
$Template->loadJavascript( '_plugins/Stats/admin/general.js' );
$Template->loadJavascript( '_plugins/Stats/admin/highcharts.js' );
$Template->loadJavascript( '_plugins/Stats/admin/exporting.js' );

$content = '
<h1>Stats</h1>
<div id="stats-chart">

</div>
<div id="tabs">
	<ul>
		<li><a href="#today">Today</a></li>
		<li><a href="' . SITE_URL . '_inc/ajax.php?file=_plugins/Stats/admin/month.php">This Month</a></li>
		<li><a href="' . SITE_URL . '_inc/ajax.php?file=_plugins/Stats/admin/ever.php">Ever</a></li>
	</ul>
	<div id="today">
		<div id="today-total"></div>
		<div id="today-browser"></div>
		<div id="today-lang"></div>
		<div id="today-os"></div>
		<div id="today-referrer"</div>
		<div id="today-page"></div>
	</div>
</div>
<br style="clear:both"/>
';
$Template->add( 'content', $content );

?>
