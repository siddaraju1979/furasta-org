<?php 

/**
 * Admin Functions, Furasta.Org
 *
 * Contains admin functions which are available
 * in the admin area only.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

/**
 * parse_template_file 
 * 
 * Parses the template file indicated, and returns
 * an array of details about the template, depending
 * on what keys are present in the template's style.css
 *
 * Possible array keys:
 * 
 * Name
 * Description
 * Author
 * URL
 * Author URL
 *
 * @param mixed $file to be parsed
 * @access public
 * @return array
 */
function parse_template_file($file){
	$allowed = array( 'Name', 'URL', 'Description', 'Author', 'Author URL' );

	$contents=file_get_contents($file);

	if(!preg_match("/\/\*(.*?)\*\//s",$contents,$match))
		return false;
	$vals=preg_split("/[\s]*[\n][\s]*/",$match[0]);
	array_pop($vals);
	array_shift($vals);

	$array=array();
	foreach($vals as $val){
		$val=preg_split("/[\s]*[:] [\s]*/",$val);
		if( !in_array( $val[ 0 ], $allowed ) )
			continue;
		$array[$val[0]]=$val[1];
	}

	return $array;
}

/**
 * list_parents 
 * 
 * orderes the parent select box in order of parents
 * with indentation
 *
 * @param int $id 
 * @param array $pages 
 * @param int $level 
 * @param mixed $default 
 * @access public
 * @return void
 */
function list_parents($id,$pages,$level,$default){
        $num=0;
        if(!isset($pages[$id]))
                return;
        $list='';
	$margin=$level*10;
        foreach($pages[$id] as $page){
		$def=($default!=0&&$page['id']==$default)?' selected="selected"':'';
		$list.='<option value="'.$page['id'].'"'.$def.' style="margin-left:'.$margin.'px;" class="' . $page['parent'] . '">'.$page['name'].'</option>';;
                $list.=list_parents($page['id'],$pages,$level+1,$default);
        }
        return $list;
}

function get_page_url($pages,$id){
	$url='';
	foreach($pages as $page){
		if($page['id']==$id){
			$url.=$page['slug'];
			if($page['parent']!=0)
				$url.=','.get_page_url($pages,$page['parent']);
			break;
		}
	}
	return $url;
}

function list_pages( $id, $pages, $level=0 ){

	$User = User::getInstance( );

        $num = 0;

        if( !isset( $pages[ $id ] ) )
                return;

        $list='';
        foreach( $pages[ $id ] as $page ){
		$perm = explode( '|', $page[ 'perm' ] );

		if( $User->adminPagePerm( $perm[ 1 ] ) ){
	                $num++;
			$parent = ( isset( $pages[ $page[ 'id' ] ] ) ) ? ' parent' : '';
        	        $href = '<a href="pages.php?page=edit&id='.$page['id'].'" class="list-link">';
                	$class = ( $level == 0 ) ? ' ':' child-of-node-'.$page['parent'].'';
			$delete = ( $page[ 'home' ] == 1 ) ? '' : '<a id="' . $page[ 'id' ] . '" class="delete link"><span class="admin-menu-img" id="menu_trash-img" title="Delete Page" alt="Delete Page">&nbsp;</span></a>';
	                $list .= '<tr id="node-'.$page['id'].'" class="expanded odd '.$class.$parent.'">
        	                	<td class="pages-table-left"><input type="checkbox" value="' . $page[ 'id' ] . '" name="trash-box"/></td>
                	                <td class="first">'.$href.$page['name'].'</a></td>
                        	        <td>'.$href.$page['user'].'</a></td>
	                                <td>'.$href.$page['type'].'</a></td>
        	                        <td>' . $href. date( "d/m/y", strtotime( $page[ 'edited' ] ) ) . '</a></td>
                	                <td><a href="pages.php?page=new&parent='.$page['id'].'"><span class="admin-menu-img" id="menu_new_page-img" title="New Sub Page" alt="New Sub Page">&nbsp;</span></a></td>
	                	        <td>' . $delete . '</td>
	                        </tr>';
		}

                $list.=list_pages($page['id'],$pages,$level + 1);
        }

        return $list;
}

/**
 * display_menu
 *
 * changes the $menu_items array into a html formatted
 * menu
 *
 * @params array $menu_items
 * @return string
 */
function display_menu($menu_items){

	/**
	 * remove items if user has insufficent permissions
	 */
	$User = User::getInstance( );

	if( !$User->hasPerm( 't' ) )
		unset( $menu_items[ 'menu_pages' ][ 'submenu' ][ 'menu_trash' ] );

	if( !$User->hasPerm( 'c' ) )
		unset( $menu_items[ 'menu_pages' ][ 'submenu' ][ 'menu_new_page' ] );

	if( !$User->hasPerm( 'u' ) )
		unset( $menu_items[ 'menu_users_groups' ] );

	if( !$User->hasPerm( 's' ) )
		unset( $menu_items[ 'menu_settings' ] );

//	die( print_r( $menu_items ) );

	$list='';
	foreach( $menu_items as $name => $item ){
		$list .= '<li><a href="' . $item[ 'url' ] . '" id="' . $name . '">' . $item[ 'name' ] . '</a>';
		if( isset( $item[ 'submenu' ] ) ){
			$list .= '<ul>';
			foreach( $item[ 'submenu' ] as $n => $ite ){
				$img = ( isset( $ite[ 'image' ] ) ) ?
					'<img src="' . $ite[ 'image' ] . '"/>':
					'';
                		$list .= '<li>
					<a href="' . $ite[ 'url' ] . '" id="' . $n . '">
						<span id="' . $n . '-img" class="admin-menu-img">' . $img . '</span>
						<span class="admin-menu-link">' . $ite[ 'name' ] . '</span>
					</a>
				</li>';

			}
			$list .= '</ul>';
		}
		$list .= '</li>';

	}
	return $list;
}

/**
 * pages_array
 *
 * returns a cached version of the pages array in the format
 * $pages[ id ] = name; it also has system restricted pagenames
 * at the end of the array
 * 
 * @access public
 * @return array
 */
function pages_array( ){

	$cache_file = md5( 'FURASTA_ADMIN_PAGES_ARRAY' );

	if( cache_is_good( $cache_file, 'PAGES' ) )
		return json_decode( cache_get( $cache_file, 'PAGES' ) );	

	/**
	 * get page names from database 
	 */
	$pages = array( );
	$query = mysql_query( 'select id,name from ' . DB_PAGES );

	while( $row = mysql_fetch_array( $query ) ){
		$pages[ $row[ 'id' ] ] = $row[ 'name' ];	
	}

	/**
	 * system names already in use 
	 */
	$pages[] = 'admin';
	$pages[] = 'install';
	$pages[] = '_inc';
	$pages[] = '_www';
	$pages[] = '_plugins';
	$pages[] = '_user';

	/**
	 * cache and return pages array
	 */
	cache( $cache_file, json_encode( $pages ), 'PAGES' ); 

	return $pages;
}

?>
