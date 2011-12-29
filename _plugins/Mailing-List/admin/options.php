<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->add( 'title', $Template->e( 'menu_mailing_options' ) . ' - ' . $Template->e( 'menu_mailing_list' ) );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Mailing-List/img/options-large.png"/></span>
<h1>' . $Template->e( 'menu_mailing_options' ) . '</h1>
';

$Template->add( 'content', $content );

?>
