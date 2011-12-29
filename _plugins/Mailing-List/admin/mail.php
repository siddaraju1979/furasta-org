<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->add( 'title', $Template->e( 'menu_mailing_send_mail' ) . ' - ' . $Template->e( 'menu_mailing_list' ) );
$Template->loadJavascript( '_inc/js/tiny_mce.js' );
$Template->add( 'head', '<script type="text/javascript" src="' . SITEURL . '_inc/tiny_mce/tiny_mce.js"></script>' );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Mailing-List/img/send-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_send_mail' ) . '</h1>

<form id="mail-form">
<table>
	<tr>
		<td>' . $Template->e( 'mailing_list_subject' ) . ':</td>
		<td><input type="text" name="Subject"/></td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="Content" id="message-body"></textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="' . $Template->e( 'menu_mailing_send_mail' ) . '"/></td>
	</tr>
</table>
</form>
';

$Template->add( 'content', $content );

?>
