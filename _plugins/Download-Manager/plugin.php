<?php

$plugin = array(
	'name' => 'Download Manager',
	'description' => 'Adds a download page type.',
	'version' => 1,

	'admin' => array (
		'page_type' => array( 
			'name' => 'Download Manager',
			'function' => 'dm_admin_page_type'
		),

		'overview_item' => array(
			'name' => 'Download Manager',
			'function' => 'dm_admin_overview_item'
		),
	),

	'frontend' => array( 
		'page_type' => 'dm_frontend_page_type'
	)
);

function dm_admin_page_type( $page_id ){
	require 'admin/adminPageType.php';
}

function dm_admin_overview_item( ){
	$downloads=rows('select name,count from '.PREFIX.'downloadManager');

	if(count($downloads)==0)
		return 'No download items yet.';

	$content='<table class="row-color">';
	foreach($downloads as $download){
		$content.='<tr><td>'.$download['name'].'</td><td>'.$download['count'].'</td></tr>';
	}

	return $content.='</table>';
}

function dm_frontend_page_type( $page ){

	$downloads=rows('select name,filename,count from '.PREFIX.'downloadManager where page_id='.$page[ 'id' ]);

	$page_content = stripslashes( $page[ 'content' ] );

	if(count($downloads)==0)
		return $page_content;

	$content='<h1>Downloads</h1><div id="download-manager-content">
                <table id="config-table" class="row-color">
                <col width="50%"/>
                <col width="50%"/>
                <tr><th colspan="2">Downloads</th></tr>';

	foreach($downloads as $download)
		$content.='<tr><td><a href="' . SITEURL . '_plugins/Download-Manager/frontend/download.php?filename='.$download['filename'].'">'.$download['name'].'</a></td><td><strong>'
.$download['count'].'</strong> Downloads</td></tr>';

	$content.='<tr><th colspan="2">&nbsp;</th></tr>
                </table></div><br/>'.$page_content;

	return $content;
}

?>
