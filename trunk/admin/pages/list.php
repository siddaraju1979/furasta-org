<?php

/**
 * List Pages, Furasta.Org
 *
 * Lists pages in the PAGES table.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

/**
 * load the treeTable jquery plugin and some page specific javascript 
 */
$Template->loadJavascript( '_inc/js/jquery/treeTable.min.js' );
$Template->loadJavascript( 'admin/pages/list.js' );

$content = '
<div id="list-pages-dropbox"></div>

<span style="float:right">
	<a href="pages.php?page=new">
		<span class="header-img" id="header-New-Page">&nbsp;</span>
		<h1 class="image-left">
			' . $Template->e( 'menu_new_page' ) . '
		</h1>
	</a>
</span>

<span>
	<span class="header-img" id="header-Edit-Pages">&nbsp;</span>
	<h1 class="image-left">
		' . $Template->e( 'pages' ) . '
	</h1>
</span>

<br/>

<div id="options-bar" style="margin-top:20px">
        <div id="options-bar-right" style="float:right">
		<select name="action" class="trash-select select-p_1">
			<option default="default">---</option>
			<option value="Trash">' . $Template->e( 'trash' ) . '</option>
		</select> 
		<input id="p_1" class="p-submit submit" type="submit" value="' . $Template->e( 'pages_list_go_button' ) . '"/>
        </div>
	<div id="options-bar-left">
		<h2><a class="link" id="treeTable-toggle">
			' . $Template->e( 'pages_list_expand_collapse_all' ) . '
		</a></h2>
	</div>
</div>

<table id="pages" class="row-color expanded">

	<tr class="top_bar">
		<th class="pages-table-left">
			<input type="checkbox" class="checkbox-all" all=""/>
		</th>
		<th>' . $Template->e( 'name' ) . '</th>
		<th>' . $Template->e( 'author' ) . '</th>
		<th>' . $Template->e( 'type' ) . '</th>
		<th>' . $Template->e( 'edited' ) . '</th>
		<th>' . $Template->e( 'new' ) . '</th>
		<th>' . $Template->e( 'trash' ) . '</th>
	</tr>
';

/**
 * fetch the pages info from the db and list the pages 
 */

$pages=array();
$query=query('select id,name,type,edited,user,parent,home,perm from '.PAGES.' order by position,name asc');
while($row=mysql_fetch_assoc($query)){
	$pages[$row['parent']][]=$row;
}

$content .= list_pages( 0, $pages );

$content .= '
	<tr>
		<th class="pages-table-left">
			<input type="checkbox" class="checkbox-all" all=""/>
		</th>
		<th colspan="6">&nbsp;</th>
	</tr>

</table>

<div style="float:right;margin-top:10px">
	<select name="action" class="trash-select select-p_2">
		<option default="default">---</option>
		<option value="Trash">' . $Template->e( 'trash' ) . '</option>
	</select> 
	<input id="p_2" class="p-submit submit" type="submit" value="' . $Template->e( 'pages_list_go_button' ) . '"/>
</div>

<br style="clear:both"/>
';


/**
 * add the $content variable to the template class for output later 
 */
$Template->add( 'content', $content );
?>
