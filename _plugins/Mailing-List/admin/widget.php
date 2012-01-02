<?php

/**
 * Mailing List Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$widget_options = options( 'mailing_list_widget' );
$opts = array( 'yes', 'no' );

$content = '
<span class="header-img"><img src="' . SITEURL . '_plugins/Mailing-List/img/options-large.png"/></span>
<h1 class="image-left">' . $Template->e( 'menu_mailing_options' ) . '</h1>

<form method="post" id="options-form">
	<table style="width:50%">
		<tr>
			<td>' . $Template->e( 'mailing_list_widget_name' ) . ': </td>
			<td><select name="collect_name">';

			foreach( $opts as $opt ){
				$content .= '<option value="' . $opt . '"';
				if( $opt == $widget_options[ 'collect_name' ] )
					$content .= ' selected="selected"';
				$content .= '>' . $Template->e( $opt ) . '</option>';
			}

			$content .= '</select></td>
		</tr>
	</table>
</div>
</form>';

$Template->add( 'content', $content );

?>
