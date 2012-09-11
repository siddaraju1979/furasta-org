<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template->add( 'title', $Template->e( 'menu_mailing_send_mail' ) . ' - ' . $Template->e( 'menu_mailing_list' ) );
$Template->loadJavascript( '_plugins/Mailing-List/admin/admin.js' );
$Template->loadJavascript( '_inc/js/tiny_mce.js' );
$Template->add( 'head', '<script type="text/javascript" src="' . SITE_URL . '_inc/tiny_mce/tiny_mce.js"></script>' );

$emails = rows( 'select email from ' . DB_PREFIX . 'mailing_list' );
$bcc = array( );
foreach( $emails as $email )
	array_push( $bcc, $email[ 'email' ] );
$bcc = implode( '; ', $bcc );

$content = '
<span class="header-img"><img src="' . SITE_URL . '_plugins/Mailing-List/img/send-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_send_mail' ) . '</h1>
<br style="clear:both"/>

<form id="mail-form" action="plugin.php?p_name=Mailing-List&page=send" method="post">
<table>
	<tr>
		<td class="small">' . $Template->e( 'mailing_list_subject' ) . ':</td>
		<td><input type="text" name="Subject"/></td>
		<td style="width:15%"><a class="link" id="show-bcc">' . $Template->e( 'mailing_list_show_bcc' ) . '</a></td>
	</tr>
	<tr style="display:none" id="bcc" class="hidden">
		<td class="small">' . $Template->e( 'mailing_list_bcc' ) . '</td>
		<td><textarea name="BCC">' . $bcc . '</textarea></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="Content" id="message-body" style="width:100%">' . $mailing_list_options[ 'template' ] . '</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3"><input name="mailing-list-submit" class="submit" type="submit" value="' . $Template->e( 'menu_mailing_send_mail' ) . '"/></td>
	</tr>
</table>
</form>
';

$Template->add( 'content', $content );

?>
