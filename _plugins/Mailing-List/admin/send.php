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

$emails = json_encode( explode( '; ', $_POST[ 'BCC' ] ) );
$subject = $_POST[ 'Subject' ];
$content = $_POST[ 'Content' ];

$javascript = '
$(function(){
	send_emails( 
		' . $emails . ',
		"' . $subject . '",
		"' . $content . '"
	);
});
';
$Template->add( 'javascript', $javascript );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Mailing-List/img/send-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_send_mail' ) . '</h1>

<div style="margin:10px">
	<p>
		<b>' . $Template->e( 'mailing_list_sending_email' ) . ' </b>
		<span class="loading"><img src="' . SITEURL . '_inc/img/loading.gif"/></span>
	</p>
	<div id="sending-content"></div>
</div>
';

$Template->add( 'content', $content );

?>
