<?php

/**
 * Trash List Pages, Furasta.Org
 *
 * Lists trash pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

$Template->loadJavascript( 'admin/pages/trash/trash.js' );

$content='
<span class="right">
	<select name="action" class="trash-select select-p_1">
		<option default="default">---</option>
		<option value="Restore">' . $Template->e( 'restore' ) . '</option>
		<option value="Delete">' . $Template->e( 'delete' ) . '</option>
	</select>
	<input id="p_1" class="p-submit submit" type="submit" value="' . $Template->e( 'trash_go_button' ) . '">
</span>
<span><span class="header-img" id="header-Trash">&nbsp;</span> <h1 class="image-left">' . $Template->e( 'menu_trash' ) . '</h1></span>
<br/>';

$rows=rows('select id,name,type,edited,user from '.DB_TRASH);
if(count($rows)==0)
	$content.='<br/><h2>' . $Template->e( 'trash_empty' ) . '<h2>';
else{
	$content.='<table id="trash" class="row-color">
	<tr class="top_bar">
		<th><input type="checkbox" class="checkbox-all" all=""/></th>
		<th>' . $Template->e( 'name' ) . '</th>
		<th>' . $Template->e( 'author' ) . '</th>
		<th>' . $Template->e( 'type' ) . '</th>
		<th>' . $Template->e( 'edited' ) . '</th>
		<th>' . $Template->e( 'restore' ) . '</th>
		<th>' . $Template->e( 'delete' ) . '</th>
	</tr>';
	foreach($rows as $row){
	        $id=$row['id'];
        	$content.='<tr>
				<td class="small"><input type="checkbox" value="'.$row['id'].'" name="trash-box"/></td>
                        	<td class="first">'.$row['name'].'</td>
	                        <td>'.$row['user'].'</td>
        	                <td>'.$row['type'].'</td>
                	        <td>'.$row['edited'].'</td>
                        	<td><a id="'.$row['id'].'" class="restore link"><span class="admin-menu-img" id="menu_new_page-img" title="Restore Page" alt="Restore Page">&nbsp;</span></a></td>
	                        <td><a id="'.$row['id'].'" class="delete link"><span class="admin-menu-img" id="delete-img" title="Delete Page" alt="Delete Page">&nbsp;</span></a></td>
        	        </tr>';
	}
	$content.='<tr><th><input type="checkbox" class="checkbox-all" all=""/><th></th><th colspan="6"></th></tr></table>';
}

$content.='<br/>

<span class="right">
<select name="action" class="trash-select select-p_2">
	<option default="default">---</option>
	<option value="Restore">' . $Template->e( 'restore' ) . '</option>
	<option value="Delete">' . $Template->e( 'delete' ) . '</option>
</select>
<input id="p_2" class="p-submit submit" type="submit" value="' . $Template->e( 'trash_go_button' ) . '"/></span>
<br style="clear:both"/>
';
$Template->add('content',$content);

?>
