<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$plugin = array(
	'name' => 'Content Areas',
	'description' => 'Allows the use of content areas such as header, footer, sidebar.',
	'version' => 1,
	'href' => 'http://furasta.org',
	'email' => 'support@furasta.org',

	'admin' => array(
		'filter_menu' => 'content_areas_filter_menu',
		'page' => 'content_areas_admin'
	),

	'frontend' => array(
		'template_function' => array(
			'name' => 'content_area',
			'function' => 'content_areas_frontend_template_function'
		),
		'filter_metadata' => 'content_areas_frontend_filter_metadata'
	)
);

// add ContentAreas class
require 'ContentAreas.php';

/**
 * content_areas_frontent_filter_metadata
 *
 * adds the content areas javascript file to the frontend
 *
 * @params string $metadata
 * @return string
 */
function content_areas_frontend_filter_metadata( $metadata ){
	$url = cache_js( 'FURASTA_FRONTEND_CONTENT_AREAS', '_plugins/Content-Areas/frontend/areas.js' );
	return $metadata .= '<script type="text/javascript" src="' . $url . '"></script>';
}

/**
 * content_areas_frontend_template_function
 *
 * the template function for content areas, returns the
 * content for the specific content area
 *
 * @params array $params
 * @params object $smarty
 * @return void
 */
function content_areas_frontend_template_function( $params, &$smarty ){
	$ContentAreas = ContentAreas::getInstance( );
	$registered = ( $ContentAreas->isRegistered( $params[ 'name' ] ) ) ? 
		'' : ' content-area-unregistered';
	echo '<div class="content-area' . $registered . '" id="content-area-' . $params[ 'name' ] . '">';
	echo $ContentAreas->getContent( $params[ 'name' ], $smarty->_tpl_vars[ 'page_id' ] );
	echo '</div>';
}

/**
 * content_areas_filter_menu
 *
 * adds the content areas pane to the admin menu
 * 
 * @params string $menu
 * @params string $url
 * @return string
 */
function content_areas_filter_menu( $menu, $url ){

	foreach( $menu as $item => $value ){
		if( $item == 'Settings' )
			$menu[ $item ][ 'submenu' ][ 'Content Areas' ] = array( 'url' => $url );

	}

	return $menu;
}

/**
 * content_areas_admin
 *
 * echos the admin area content areas page
 *
 * 
 */
function content_areas_admin( ){
	$Template = Template::getInstance( );
	$Template->loadCSS( '_plugins/Content-Areas/style.css' );
	$ContentAreas = ContentAreas::getInstance( );

	$content = '
	<h1>Content Areas</h1>
	<div id="content-areas">
		<div id="content-right">
			<h2>Your Website</h2>
			<div id="content-site">';

	foreach( $ContentAreas->areas( ) as $area ){
		$content .= '<div class="content-area" id="content-area-' . $area[ 'name' ] . '" style="position:relative;';
		$content .= 'top:' . $area[ 'data' ][ 'top' ] . 'px;';
		$content .= 'left:' . $area[ 'data' ][ 'left' ] . 'px;';
		$content .= 'width:' . $area[ 'data' ][ 'width' ] . 'px;">';
		$content .= '<h3>' . $area[ 'name' ] . '</h3></div>';
	}

	$content .= '
			</div>
		</div>
		<div id="content-left">
			<h2>Widgets</h2>';

	$Plugins = Plugins::getInstance( );
	foreach( $Plugins::plugins( ) as $plugin ){
	}

	$content .= '
		</div>
		<br style="clear:both"/>
	</div>
	';

	$Template->add( 'title', 'Content Areas' );
	$Template->add( 'content', $content );
}
?>
