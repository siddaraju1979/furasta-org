<?php

/**
 * Overview Items, Furasta.Org
 *
 * Accessed via AJAX, this file loads the content
 * of overview items.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_overview
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

$overview_item = @$_GET['overview_item'];

if( $overview_item == '' )
	die( 'There has been an error loading the page.' );

$Template = Template::getInstance( );
$content = '';

/**
 * switch
 *
 * switches between the possible values of
 * $overview_item and echos the content of the
 * associated item. the default switch checks
 * plugins and if it exists in a plugin it also
 * echos the content. 
 */
switch( $overview_item ){

	case 'website-overview':
		require_once HOME . '_inc/function/admin.php';

		$template = parse_template_file(TEMPLATE_DIR.'style.css');

		$content .= '
		<table class="row-color">
		        <tr><td>' . $Template->e( 'pages' ) . ':</td><td>' . single( 'select count(id) from ' . DB_PAGES, 'count(id)' ) . '</td></tr>
		        <tr><td>' . $Template->e( 'trash' ) . ':</td><td>' . single( 'select count(id) from ' . DB_TRASH, 'count(id)' ) . '</td></tr>
		        <tr><td>' . $Template->e( 'users' ) . ':</td><td>' . single( 'select count(id) from ' . DB_USERS, 'count(id)' ) . '</td></tr>
			<tr><td>' . $Template->e( 'groups' ) . ':</td><td>' . single( 'select count(id) from ' . DB_GROUPS, 'count(id)' ) . '</td></tr>
		        <tr><td>' . $Template->e( 'template' ) . ':</td><td>' . $template[ 'Name' ] . '</td></tr>
		        <tr><td>Furasta.Org ' . $Template->e( 'version' ) . ':</td><td>' . VERSION . '</td></tr>
		</table>';
	break;
	case 'recently-edited':
		$content .= '<table class="row-color">';

		$pages = rows( 'select id,name,content,edited,perm from ' . DB_PAGES . ' order by edited desc limit 5' );

		if( count( $pages ) == 0 ){
			$content .= '<p><i>No items recently edited!</i></p>';
			break;
		}

		foreach( $pages as $page ){

			/**
			 * get admin perm from page perm string
			 * perm rules are in _inc/class/User.php
			 */
			$perm = explode( '|', $page[ 'perm' ] );

			/**
			 * check if user has permission to view page
			 */
			if( $User->adminPagePerm( $perm[ 1 ] ) )
		        		$content .= '<tr><td><span>' . date( "F j, Y", strtotime( $page[ 'edited' ] ) ) . '
		        		</span><a href="pages.php?page=trash"><h3>' . $page[ 'name' ] . '</h3></a>
		        		<p>' . strip_tags( substr( $page[ 'content' ], 0, 125 ) ) . ' [...]</p></td></tr>';
		}

		$content .= '</table>';
	break;
	case 'recently-trashed':
		$content .= '<table class="row-color">';
		$pages = rows( 'select id,name,content,edited from ' . DB_TRASH . ' order by edited desc limit 5' );
		
		if( count( $pages ) == 0 ){
			$content .= '<p><i>No items in trash!</i></p>';
			break;
		}

		foreach( $pages as $page ){
        		$content .= '<tr><td><span>' . date( "F j,Y", strtotime( $page[ 'edited' ] ) ) . '</span><a
		        href="pages.php?page=edit&id=' . $page[ 'id' ] . '"><h3>' . $page[ 'name' ] . '</h3></a>
		        <p>' . strip_tags( substr( $page[ 'content' ], 0, 125 ) ) . ' [...]</p></td></tr>';
		}

		$content .= '</table>';
	break;
	case 'furasta-devblog':
		$cache_file = md5( 'FURASTA_RSS_DEVBLOG' );

		if( cache_is_good( $cache_file, '60 * 60 * 24 * 3', 'RSS' ) )
        		$items = json_decode( cache_get( $cache_file, 'RSS' ), true );
		else{
			$elements = rss_fetch( 'http://blog.macaoidh.name/tag/furasta-org/feed/' );

			if( $elements == false ){
				$content .= '<p><i>Could not fetch feed.</i></p>';
				break;
			}

			$items = array( );

			for( $i=0; $i<=2; $i++ ){
			        foreach( $elements[ $i ] as $element=>$value ){
		  	        	switch( $element ){
                        			case 'pubDate':
                                			$items[ $i ][ 'pubDate' ] = date( "F j, Y", strtotime( $value ) );
                        			break;
                        			case 'title':
                                			$items[ $i ][ 'title' ] = iconv( "UTF-8", "UTF-8//IGNORE", $value );
                        			break;
                        			case 'link':
                                			$items[ $i ][ 'link' ] = $value;
                        			break;
                        			case 'description':
                                			$items[ $i ][ 'description' ] = substr( $value, 0, 125) . ' [...]';
                        			break;
                			}
        			}
			}

			cache( $cache_file, json_encode( $items ), 'RSS' );
		}

		$content .= '<table class="row-color">';

		foreach( $items as $item )
		        $content .= '<tr><td><span>' . $item[ 'pubDate' ] . '</span><a
		        href="' .$item[ 'link' ] . '"><h3>' . $item[ 'title' ] . '</h3></a><p>' . $item[ 'description' ] . '</p></td></tr>';

		$content .= '</table>';
	break;
	default:
		/**
		 * the template class is used here more as a matter
		 * of consistency than anything else
		 */
		$Template = Template::getInstance( );
		$Plugins->adminOverviewItemContent( $overview_item );
}

$Template->add( 'content', $content );
?>
