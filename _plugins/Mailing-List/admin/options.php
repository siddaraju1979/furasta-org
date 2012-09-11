<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$conds = array(
	'from' => array(
		'name' => $Template->e( 'mailing_list_options_from' ),
		'required' => true
	),
	'reply_to' => array(
		'name' => $Template->e( 'mailing_list_options_reply_to' ),
		'required' => true
	)
);

$valid = validate( $conds, '#options-form', 'options-form-save' );

if( isset( $_POST[ 'options-form-save' ] ) && $valid ){
	$from = addslashes( @$_POST[ 'from' ] );
	$reply_to = addslashes( @$_POST[ 'reply_to' ] );
	$template = addslashes( @$_POST[ 'template' ] );

	if( $from != $mailing_list_options[ 'from' ] )
		query( 'update ' . DB_OPTIONS . ' set value="' . $from . '" where name="from" && category="mailing_list_options"' );

	if( $reply_to != $mailing_list_options[ 'reply_to' ] )
		query( 'update ' . DB_OPTIONS . ' set value="' . $reply_to . '" where name="reply_to" && category="mailing_list_options"' );

	if( $template != $mailing_list_options[ 'template' ] )
		query( 'update ' . DB_OPTIONS . ' set value="' . $template . '" where name="template" && category="mailing_list_options"' );

	$Template->runtimeError( '13' ); 
	$mailing_list_options = options( 'mailing_list_options' );
}

$Template->add( 'title', $Template->e( 'menu_mailing_options' ) . ' - ' . $Template->e( 'menu_mailing_list' ) );

$javascript = '
$(function(){
	$( "#tabs" ).tabs( );
	tinymce_changeConfig( "#template-content", "Normal" );
});
';

$Template->add( 'head', '<script type="text/javascript" src="' . SITE_URL . '_inc/tiny_mce/tiny_mce.js"></script>' );
$Template->add( 'javascript', $javascript );
$Template->loadJavascript( '_inc/js/tiny_mce.js' );

$content = '
<span class="header-img"><img src="' . SITE_URL . '_plugins/Mailing-List/img/options-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_options' ) . '</h1>

<form method="post" id="options-form">
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">' . $Template->e( 'mailing_list_options_general' ) . '</a></li>
		<li><a href="#tabs-2">' . $Template->e( 'mailing_list_options_template' ) . '</a></li>
	</ul>
	<div id="tabs-1">
		<table id="tabs-content">
			<tr>
				<td>' . $Template->e( 'mailing_list_options_from' ) . '</td>
				<td><input type="text" name="from" value="' . $mailing_list_options[ 'from' ] . '"/></td>
			</tr>
			<tr>
				<td>' . $Template->e( 'mailing_list_options_reply_to' ) . '</td>
				<td><input type="text" name="reply_to" value="' . $mailing_list_options[ 'reply_to' ] . '"/></td>
			</tr>
		</table>
	</div>
	<div id="tabs-2">
		<div class="tabs-content">
			<p><i>' . $Template->e( 'mailing_list_options_template_description' ) . '</i></p>
			<textarea name="template" id="template-content" style="width:100%">' . $mailing_list_options[ 'template' ] . '</textarea>
		</div>
	</div>
</div>
<br/>
<input type="submit" class="submit" value="' . $Template->e( 'save' ) . '" name="options-form-save"/>
</form> 
';

$Template->add( 'content', $content );

?>
