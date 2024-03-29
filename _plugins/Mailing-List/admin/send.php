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

if( strpos( '; ', $_POST[ 'BCC' ] === false ) )
	$emails = json_encode( array( $_POST[ 'BCC' ] ) );
else
	$emails = json_encode( explode( '; ', $_POST[ 'BCC' ] ) );

$subject = $_POST[ 'Subject' ];
$contents = $_POST[ 'Content' ];

$javascript = '
$(function(){
	send_emails( 
		' . $emails . ',
		"' . $subject . '"
	);
});
';

$Template->add( 'javascript', $javascript );

$content = '
<span class="header-img"><img src="' . SITE_URL . '_plugins/Mailing-List/img/send-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_send_mail' ) . '</h1>
<div id="contents-mail" style="display:none">' . $contents . '</div>
<div style="margin:10px">
	<p>
		<b>' . $Template->e( 'mailing_list_sending_email' ) . ' </b>
		<span class="loading"><img src="' . SITE_URL . '_inc/img/loading.gif"/></span>
	</p>
	<div id="sending-content"></div>
</div>
';

$Template->add( 'content', $content );

?>
