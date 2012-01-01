<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template->add( 'title', $Template->e( 'menu_mailing_list_subscribers' ) . ' - ' . $Template->e( 'menu_mailing_list' ) );
$Template->loadJavascript( '_plugins/Mailing-List/admin/admin.js' );

$content = '

<div id="add-subscriber-dialog" style="display:none">
	<form id="new-sub-form">
	<table>
		<tr id="form-error"></tr>
		<tr>';

if( $mailing_list_options[ 'use_names' ] == 'Yes' )
	$content .= '<td>' . $Template->e( 'name' ) . ':</td><td><input type="text" id="sub_name"/></td></tr><tr>';

$content .= '
		<td>' . $Template->e( 'email' ) . ':</td>
		<td><input name="Email" type="text" id="sub_email"/></td>
	</tr>
	</table>
	</form>
</div>

<span class="header-img"><img src="' . SITEURL . '_plugins/Mailing-List/img/list-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_list_subscribers' ) . '</h1>

<a class="link add-subscriber right">
	<span class="header-img" id="header-Add">&nbsp;</span>
	<h1 class="image-left">' . $Template->e( 'mailing_list_add_subscriber' ) . '</h1>
</a>
';

$subs = rows( 'select * from ' . PREFIX . 'mailing_list' );

if( count( $subs ) == 0 ){
	$content .= '<p><i>' . $Template->e( 'mailing_list_no_subscribers' ) . '</i></p>';
}
else{
	$content .= '<table id="subscribers" class="row-color">
		<tr>';
	if( $mailing_list_options[ 'use_names' ] == 'Yes' )
		$content .= '<th>' . $Template->e( 'name' ) . '</th>';
	$content .= '	<th>' . $Template->e( 'email' ) . '</th>
			<th>' . $Template->e( 'delete' ) . '</th>
		</tr>';

	foreach( $subs as $sub ){
		$content .= '<tr>';
		if( $mailing_list_options[ 'use_names' ] == 'Yes' )
			$content .= '<td>' . $sub[ 'name' ] . '</td>';
		$content .= '<td>' . $sub[ 'email' ] . '</td>
			<td><a class="link delete" id="' . $sub[ 'id' ] . '">
				<span class="admin-menu-img" id="delete-img">&nbsp;</span>
			</a></td>
		</tr>';
	}

	$content .= '<tr>';
	if( $mailing_list_options[ 'use_names' ] == 'Yes' )
		$content .= '<th></th>';
	$content .= '<th colspan="3"></th></tr>
	</table>';
}

$Template->add( 'content', $content );

?>
