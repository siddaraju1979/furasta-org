<?php

/**
 * Delete Page, Furasta.Org
 *
 * Deletes a page from the DB_PAGES table and adds it to
 * DB_TRASH
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * make sure ajax script was loaded and user is
 * logged in 
 */
if( !defined( 'AJAX_LOADED' ) || !defined( 'AJAX_VERIFIED' ) )
        die( );

/**
 * check if user has permission to delete pages
 */
if( !$User->hasPerm( 'd' ) )
	die( 'perm' );

$id=addslashes(@$_GET['id']);
if($id=='')
	exit;

$parent=single('select parent from '.DB_PAGES.' where id='.$id,'parent');

$children=rows('select id from '.DB_PAGES.' where parent='.$id);

if(count($children)!=0){
	foreach($children as $child)
		query('update '.DB_PAGES.' set parent='.$parent.' where id='.$child['id']);
}

query('insert into '.DB_TRASH.' select NULL,name,content,slug,template,type,edited,user,position,parent,perm,home,display from '.DB_PAGES.' where id='.$id);

query('delete from '.DB_PAGES.' where id='.$id);

cache_clear('DB_PAGES');
?>
