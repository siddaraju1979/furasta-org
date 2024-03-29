<?php

/**
 * Pages Multiple Action, Furasta.Org
 *
 * Performs multiple actions on pages such as
 * trashing them.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

$function = @$_GET[ 'act' ];
$boxes = @$_GET[ 'boxes' ];
$boxes = explode( ',', $boxes );

switch( $function ){
	case 'Trash':
		foreach( $boxes as $id ){
			$children=rows('select id from '.DB_PAGES.' where parent='.$id);

			if(count($children)!=0){
                        	$parent=single('select parent from '.DB_PAGES.' where id='.$id,'parent');

			        foreach($children as $child)
			                query('update '.DB_PAGES.' set parent='.$parent.' where id='.$child['id']);
			}

			query('insert into '.DB_TRASH.' select NULL,name,content,slug,template,type,edited,user,position,parent,perm,home,display from '.DB_PAGES.' where id='.$id);

			query('delete from '.DB_PAGES.' where id='.$id);
		}

		cache_clear('DB_PAGES');
	break;
	default:
		error('Please contact bugs@macaoidh.name','Unknown Error');
}

header( 'location: pages.php?page=list&error=15' );
?>
