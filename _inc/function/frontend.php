<?php

/**
 * frontend_error
 *
 * issues an error using the frontend template
 *
 * @param string $error
 * @param string $title
 * @return void
 */
function frontend_error( $error, $title = 'Error' ){

	// setup required vars
	$User = User::getInstance( );
	$Plugins = Plugins::getInstance( );
	global $SETTINGS;

	// create fake page array
	$Page = array(
		'id' => 0,
		'name' => $title,
		'content' => $error,
		'slug' => $title,
		'edited' => '',
		'user' => '',
		'parent' => 0,
		'type' => 'Normal',
		'template' => 'Default',
	);

	// run smarty
	require HOME . '_inc/smarty.php';
	exit;

}

/**
 * Frontend Functions, Furasta.Org
 *
 * Contains functions which are loaded in
 * the frontend of the CMS only.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    frontend
 */

function frontend_menu(){
        $cache_file=md5( 'FURASTA_FUNCTION_FRONTEND_MENU' );

        if(cache_is_good($cache_file,'120*60*12','DB_PAGES'))
                $order=json_decode(cache_get($cache_file,'DB_PAGES'));
        else{
		$pages=array();
		$query=query('select id,name,parent,slug,home from '.DB_PAGES.' where display=1 order by position,name desc');

		while($row=mysql_fetch_assoc($query))
		        $pages[$row['parent']][]=$row;

		$order=frontend_list_pages(0,$pages,0,SITE_URL);

                cache($cache_file,json_encode($order),'DB_PAGES');
        }
        return $order;
}

function frontend_list_pages($id,$pages,$level,$url){
        $num=0;
        if(!isset($pages[$id]))
                return;
        $list='<ul class="furasta-menu-'.$level.'">';
        foreach($pages[$id] as $page){
		$_url=$url.$page['slug'].'/';
		$home=($page['home']==1)?' homepage':'';
		$list.='<li class="level_'.$level.'" id="li_'.$page['slug'].'"><a href="'.$_url.'" class="link_level_'.$level.' menu_link'.$home.'" id="link_'.$page['slug'].'">'.$page['name'].'</a>';
                $list.=frontend_list_pages($page['id'],$pages,$level+1,$_url);
		$list.='</li>';
        }
        return $list.='</ul>';
}

/**
 * frontend_breadcrumbs
 *
 * returns a page tree with default css formatting 
 * 
 * @access public
 * @return void
 */
function frontend_breadcrumbs( $params ){

	global $SETTINGS;

	$page = @$_GET[ 'page' ];
        $array = explode( '/', $page );

        if( end( $array ) == '' )
                array_pop( $array );

	if( count( $array ) == 0 ){
		$home = single( 'select slug from ' . DB_PAGES . ' where home=1 limit 1', 'slug' );
		$array[ 0 ] = $home;
	}

	$seperator = isset( $params[ 'seperator' ] ) ? $params[ 'seperator' ] : ' > ';

	/**
	 * create breadcrumbs array in form $name => $url
	 */ 
	$breadcrumbs = array( );
	for( $i = 0; $i < count( $array ); $i++ ){
		$name = str_replace( '-', ' ', $array[ $i ] );

		$url = SITE_URL;
		for( $n = 0; $n < ( $i + 1 ); $n++ )
			$url .= $array[ $n ] . '/';

		$breadcrumbs[ $name ] = $url;

	}

	/**
	 * filter breadcrumbs array through plugins
	 */ 
	$Plugins = Plugins::getInstance( );
	$breadcrumbs = $Plugins->filter( 'frontend', 'filter_breadcrumbs', $breadcrumbs );


	/**
	 * generate html
	 */
	$content = '<ul id="page-tree" style="list-style-type:none">
			<li style="display:inline"><a href="' . SITE_URL . '">' . $SETTINGS[ 'site_title' ] . '</a></li>';

	$i = 0;
	foreach( $breadcrumbs as $name => $url ){
		$name = ( ( $i + 1 ) == count( $array ) ) ? '<b>' . $name . '</b>' : $name;

		$content .= '<li style="display:inline">' . $seperator . '<a href="' . $url . '">' . $name . '</a></li>';
		++$i;
	}

	$content .= '</ul>';

	return $content;

}

/**
 * frontend_metadata
 *
 * generates page metadata such as meta tags,
 * and javascript/css links that should be contained
 * in the header. This function also has a plugin
 * filter.
 *
 * @todo manually cache css/js files added here
 * @return string of metadata
 */
function frontend_metadata( $params, $smarty ){
	$content = strip_html_tags( frontend_page_content( $params, $smarty, true ) );
	$keywords = meta_keywords( $content );
	$description = substr( $content, 0, 250 ) . '...';

	$Template = Template::getInstance( );
	$metadata='
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="' . cache_js( 'FURASTA_FRONTEND_MULTIDDM', '_inc/js/jquery/multi-ddm.min.js' ) . '"></script>
	<script type="text/javascript" src="' . cache_js( 'FURASTA_FRONTEND_FRONTENDJS', '_inc/js/frontend.js' ) . '"></script>
	<link rel="stylesheet" type="text/css" href="' . cache_css( 'FURASTA_FRONTEND_FRONTENDCSS', '_inc/css/frontend.css' ) .'"/>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="Furasta.Org ' . VERSION . '" />
	<meta name="description" content="' . $description . '" />
	<meta name="keywords" content="' . $keywords . '" />
	<link rel="shortcut icon" href="' . SITE_URL . '_inc/img/favicon.ico" />
	<script type="text/javascript">
		window.furasta = {
			site : { 
				url : "' . SITE_URL . '",
				title : "' . $smarty->getTemplateVars( 'site_title' ) . '",
				subtitle : "' . $smarty->getTemplateVars( 'site_subtitle' ) . '"
			},
			page : {
				id : "' . $smarty->getTemplateVars( 'page_id' ) . '" ,
				name : "' . $smarty->getTemplateVars( 'page_name' ) . '" ,
				slug : "' . $smarty->getTemplateVars( 'page_slug' ) . '" ,
				parent_id : "' . $smarty->getTemplateVars( 'parent_id' ) . '"
			},
			postdata : ' . json_encode( $_POST );

	if( User::verify( ) ){
		/**
		 * @todo update this to new multiple groups
		$User = User::getInstance( );
		$metadata .= ',
			user:{
				id: ' . $User->id( ) . ',
				name: "' . $User->name( ) . '",
				group: "' . implode( '', $User->groups( ) ) . '",
				group_name: "' . implode( $User->groupNames( ) . '"	
			}';
		*/
	}

	$metadata .= '
	};</script>';

	$Plugins = Plugins::getInstance( );
	$metadata = $Plugins->filter( 'frontend', 'filter_metadata', $metadata );	

	return $metadata;
}

/**
 * frontend_page_load_time
 *
 * simple function which returns the amount of time
 * it has taken for the page to load until
 * this point. This can be assumed to be a reasonably
 * accurate page load time.
 *
 * @return int
 */
function frontend_page_load_time( ){
	$time = microtime( true );
	$runtime = $time - START_TIME;
	return round( $runtime, 2 );
}

/**
 * frontend_css_load
 *
 * returns a url to the compressed css
 * passed in the file parameter 
 * 
 * smarty parameters:
 * string file
 *
 * example: {cache_css file="path/to/file"}
 * 
 * @param array $params
 * @access public
 * @return url or bool
 */
function frontend_css_load( $params ){

	if( !$params[ 'file' ] )
		return;

	$name = 'FURASTA_FRONTEND_CSS_' . $params[ 'file' ];

	return cache_css( $name, $params[ 'file' ] );

}

/**
 * frontend_javascript_load
 * 
 * returns a url to the comprssed javascript
 * passed in the file parmeter 
 * 
 * smarty parameters:
 * string file
 *
 * example: {cache_js file="path/to/file"}
 *
 * @param array $params 
 * @access public
 * @return bool or string
 */
function frontend_javascript_load( $params ){
        
	if( !$params[ 'file' ] )
		return;

        $name = 'FURASTA_FRONTEND_JS_' . $params[ 'file' ];

	return cache_js( $name, $params[ 'file' ] );

}

/**
 * frontend_page_content
 *
 * gets the page content for the frontend
 *
 * @params object $smarty
 * @params array $params
 * @return void
 */
function frontend_page_content( $params, $smarty, $silent = false ){
	$type = $smarty->getTemplateVars( 'page_type' );

	// capture output to variable
	ob_start( );

	if( $type == 'Normal' )
		echo $smarty->getTemplateVars( '__page_content' );
	else{
		$page_id = $smarty->getTemplateVars( 'page_id' );
		$Plugins = Plugins::getInstance( );
		$Plugins->frontendPageType( $type, $page_id );
	}

	$content = ob_get_contents( );
	ob_end_clean( );

	/**
	 * parse the page content as a smarty template
	 */
	$content = $smarty->fetch( 'eval:' . $content );

	if( $silent )
		return $content;

	echo $content;
	
}

function strip_html_tags( $text ){
	$text = preg_replace(
	array(
		// Remove invisible content
		'@<head[^>]*?>.*?</head>@siu',
		'@<style[^>]*?>.*?</style>@siu',
		'@<script[^>]*?.*?</script>@siu',
		'@<object[^>]*?.*?</object>@siu',
		'@<embed[^>]*?.*?</embed>@siu',
		'@<applet[^>]*?.*?</applet>@siu',
		'@<noframes[^>]*?.*?</noframes>@siu',
		'@<noscript[^>]*?.*?</noscript>@siu',
		'@<noembed[^>]*?.*?</noembed>@siu',
		// Add line breaks before and after blocks
		'@</?((address)|(blockquote)|(center)|(del))@iu',
		'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
		'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
		'@</?((table)|(th)|(td)|(caption))@iu',
		'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
		'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
		'@</?((frameset)|(frame)|(iframe))@iu',
	),
	array( ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );

	$text = strip_tags( $text );
	$text = str_replace( array( "\n", "\t", "  ", "   ", "    ", "     ", "      ", "       ", "        " ), ' ', $text );
	return $text;
}
?>
