<?php

/**
 * Edit Pages, Furasta.Org
 *
 * Displays a factility through which the user
 * can edit pages.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 * @todo       load all select boxes on this page via ajax
 */

/**
 * get page id - if none present redirect to 'new page' 
 */
$id = (int) @$_GET[ 'id' ];
if( $id == 0 )
        header( 'location pages.php?action=new' );

/**
 * get the error if isset 
 */
$error = @$_GET[ 'error' ];

if( $error != '' )
	$Template->runtimeError( $error );

/**
 * set up javascript and php form validation
 */
$conds = array(
	'Name' => array(
		'name' => $Template->e( 'name' ),
		'required'	=>	true,
		'minlength'	=>	2,
		'pattern'	=>	array(
			'^[A-Za-z0-9 ]{2,40}$',
			$Template->e( 'pages_name_pattern' )
		)
	)
);

$valid = validate( $conds, "#pages-edit", 'edit-save' );

/**
 * read post information and edit page if applicable
 */
if( isset( $_POST[ 'edit-save' ] ) && $valid ){

	/**
	 *  set up post variables
	 */
        $name = addslashes( $_POST[ 'Name' ] );
        $type = addslashes( $_POST[ 'Type' ] );
        $template = addslashes( $_POST[ 'Template' ] );
        $content = addslashes( $_POST[ 'PageContent' ] );
        $slug = str_replace( ' ', '-', $name );
        $home = ( int ) @$_POST[ 'Homepage' ];
        $navigation = ( @$_POST[ 'Navigation' ] == 1 ) ? 0 : 1;
        $parent = (int) $_POST[ 'Parent' ];
        $perm = addslashes( @$_POST[ 'perm' ] );

	/**
	 * update options if they exist
	 */
	update_options( @$_POST[ 'options' ], 'page_' . $id );

	/**
	 * get pages_array and remove current page
	 */
	$pages_array = pages_array( );
	unset( $pages_array[ $id ] );

        /**
         * check if pagename exists already or if page name
	 * clashes with system pagename
         */
        if( in_array( $name, $pages_array ) == false ){

		/**
		 * if page is set as home page remove home
		 * tag from previous home page
		 */
		if( $home == 1 )
			query( 'update ' . PAGES . ' set home=0 where home=1' );

		/**
		 * update database with edited page 
		 */
		query('update '.PAGES.' set
		name="'.$name.'",content="'.$content.'",slug="'.$slug.'",template="'.$template.'",type="'.$type.'",edited="'.date('Y-m-d
		G:i:s').'",user="'.$User->name( ).'",parent='.$parent.',perm="'.$perm.'",home='.@$home.',display='.$navigation.'
		where id='.$id,true);

		/**
		 * clear pages cache and set status as edited 
		 */
		cache_clear( 'PAGES' );

		$Template->runtimeError( '1' );
	}
	else
		$Template->runtimeError( '4', $name );
}

/**
 * get options for page if present
 */
$page_options = options( 'page_' . $id );

/**
 * get page rows in array and stripslashes 
 */
$Page = row( 'select * from fr_pages where id= ' . $id );
$Page = stripslashes_array( $Page );

/**
 * check user has permission to edit pages
 * in general, then check for this page
 */
$perm = explode( '|', $Page[ 'perm' ] );
if( !$User->hasPerm( 'e' ) && !$User->adminPagePerm( $perm[ 1 ] ) )
	error( $Template->e( 'pages_permissions_error' ), $Template->e( 'error_permissions_title' ) );

/**
 * load javascript files to Template class for output later 
 */
$Template->loadJavascript( '_inc/js/tiny_mce.js' );
$Template->loadJavascript( 'admin/pages/edit.js' );
// @todo find a way to cache this without breaking tinymce
$Template->add( 'head', '<script type="text/javascript" src="' . SITEURL . '_inc/tiny_mce/tiny_mce.js"></script>' );

$content='
<div id="page-permissions-dialog" title="Page Permissions" style="display:none">
        <div id="complete-message" style="display:none"></div>
        <form id="page-permissions-content">
		<p>' . $Template->e( 'loading' ) . '<img src="' . SITEURL . '_inc/img/loading.gif"/></p>
        </form>
</div>

<span style="float:right"><a href="pages.php?page=new&parent='.$Page['id'].'" id="new-subpage"><span class="header-img" id="header-New-Page">&nbsp;</span><h1 class="image-left">' . $Template->e( 'pages_new_subpage' ) . '</h1></a></span>
<span><span class="header-img" id="header-Edit-Pages">&nbsp;</span><h1 class="image-left">' . $Template->e( 'pages_edit_page' ) . '</h1></span>
<br/>
<form method="post" id="pages-edit">
	<table id="page-details">
		<tr>
                        <td class="small">' . $Template->e( 'name' ) . ':</td>
			<td><input type="text" name="Name" id="page-name" value="'.$Page['name'].'" autocomplete="off"/></td>
			<td class="options"><a id="options-link">' . $Template->e( 'pages_show_options' ) . '</a></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><a href="' . SITEURL . $Page[ 'slug' ] . '" id="slug-url">' . SITEURL . $Page[ 'slug' ] . '</a></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<input type="hidden" name="slug" id="slug-put" value="' . $Page[ 'slug' ] . '"/>
	<div id="options">
		<h2>' . $Template->e( 'options' ) . '</h2>
		<table id="page-details">
			<tr>
				<td class="small">Type:</td>
				<td><select name="Type" id="edit-type">
					<option value="Normal">' . $Template->e( 'normal' ) . '</option>
';

$options=$Plugins->adminPageTypes();
foreach($options as $option){
	if($option==$Page['type'])
		$content.='<option selected="selected" value="'.$option.'">'.$option.'</option>';
	else
		$content.='<option value="'.$option.'">'.$option.'</option>';
}

$homepage = ( @$Page[ 'home' ] == 1 ) ? '<input type="checkbox" checked="checked" disabled="disabled"/><input type="hidden" name="Homepage" value="1"/>' : '<input type="checkbox" value="1" name="Homepage" />';

$content.='
				</select></td>
				<td class="small">' . $Template->e( 'pages_is_home' ) . ':</td>
				<td>' . $homepage . '</td>
			</tr>
			<tr>
				<td class="small">' . $Template->e( 'template' ) . ':</td>
				<td><select name="Template">
					<option value="Default">' . $Template->e( 'default' ) . '</option>
';

$files = scandir( TEMPLATE_DIR );

foreach($files as $file){
	if(substr(strrchr($file,'.'),1)=='html'){
		$file_n=basename($file,'.html');
		if($file_n=='index')
			continue;
		if($file_n==$Page['template'])
			$selected=' selected="selected"';
		$content.='<option value="'.$file_n.'"'.@$selected.'>'.$file_n.'</option>';
	}
}

$navigation=($Page['display']==1)?'':' checked="checked"';

$content.='
				</select></td>
				<td class="small">' . $Template->e( 'pages_hide_in_navigation' ) . ':</td>
				<td><input type="checkbox" value="1" name="Navigation"'.$navigation.'/></td>
			</tr>
			<tr>
				<td class="small">' . $Template->e( 'parent' ) . ':</td>
				<td><select id="select-parent" name="Parent">
';

$pages=array();
$query=query('select id,name,parent from '.PAGES.' order by position,name desc');
while($row=mysql_fetch_assoc($query)){
	if( $row[ 'id' ] != $Page['id'] )
        	$pages[$row['parent']][]=$row;
}

$parent=$Page['parent'];
if($parent==0){
        $content.='<option selected="selected" value="0">---</option>';
        $content.=list_parents(0,$pages,0,0);
}
else{
        $content.='<option value="0">---</option>';
        $content.=list_parents(0,$pages,0,$parent);
}

$content.='
				<td class="small">
					<a class="link" id="page-permissions">' . $Template->e( 'permissions' ) . '</a>
					<input type="hidden" name="perm" value="' . $Page[ 'perm' ] . '"/></td>
			</tr>
		</table>
	</div>

	<div id="pages-type-content" type="'.$Page['type'].'" page-id="'.$Page['id'].'">&nbsp;</div>
<input type="submit" name="edit-save" value="' . $Template->e( 'save' ) . '" class="submit" id="edit-save"/>
</form>
';

$Template->add('content',$content);
?>
