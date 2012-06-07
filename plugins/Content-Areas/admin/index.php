<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$Template = Template::getInstance( );
$Template->loadCSS( '_plugins/Content-Areas/style.css' );
$Template->loadJavascript( '_plugins/Content-Areas/admin/admin.js' );
$ContentAreas = ContentAreas::getInstance( );



$content = '
<div id="edit-dialog" style="display:none"></div>
<div id="content-areas">
	<div id="content-right">
		<table>
			<tr>
				<td style="width:15%">Template:</td>
				<td><select id="content-template-select">
					<option value="default">Default</option>';

$files = scandir( TEMPLATE_DIR );

foreach($files as $file){
	if(substr(strrchr($file,'.'),1)=='html'){
		$file_n=basename($file,'.html');
		if($file_n=='index')
			continue;
		$content.='<option value="'.$file_n.'">'.$file_n.'</option>';
	}
}

$content .= '		
				</select></td>
			</tr>
			<tr>
				<td style="width:15%">Content Areas:</td>
				<td><select id="content-area-select">
					<option>---</option>
				</select></td>
			</tr>
		</table>
		<div id="content-template">
			<p><i>Please select a content area</i></p>
		</div>
	</div>
';

$content .= '
	<div id="content-left">
		<div id="widgets">
			<h2>Widgets</h2>';

	foreach( $ContentAreas->widgets( ) as $widget => $functions ){
		$content .= '<div class="widget-left" type="' . $widget . '">';
		$content .= '<span>' . $widget . '</span>';
		$content .= '</div>';
	}

$content .= '
		</div>
	</div>
	<br style="clear:both"/>
</div>';

$Template->add( 'title', 'Content Areas' );
$Template->add( 'content', $content );

?>
