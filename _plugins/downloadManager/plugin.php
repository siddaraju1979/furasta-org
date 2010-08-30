<?php

class downloadManager{
        var $name='Download Manager';
	var $description='Adds a downloads page type and manages downloads.';
	var $version=1;
	var $adminPageType='Download';
        var $email='conormacaoidh@gmail.com';
        var $href='http://blog.macaoidh.name';

	function adminPageType($page_id){
		require 'admin/adminPageType.php';
		return $content;
	}

	function adminOverviewItem(){
		$downloads=rows('select name,count from '.PREFIX.'downloadManager');

		if(count($downloads)==0)
			return 'No download items yet.';

		$content='<table class="row-color">';
		foreach($downloads as $download){
			$content.='<tr><td>'.$download['name'].'</td><td>'.$download['count'].'</td></tr>';
		}

		return $content.='</table>';
	}

	function frontendPageType($Page){
                $downloads=rows('select name,filename,count from '.PREFIX.'downloadManager where page_id='.$Page->about('id'));

                if(count($downloads)==0)
                        return stripslashes($Page->about('content'));

		$content='<h1>Downloads</h1><div id="download-manager-content">
		<table id="config-table" class="row-color">
	        <col width="50%"/>
        	<col width="50%"/>
		<tr><th colspan="2">Downloads</th></tr>';

                foreach($downloads as $download)
                        $content.='<tr><td><a href="/_plugins/downloadManager/frontend/download.php?filename='.$download['filename'].'">'.$download['name'].'</a></td><td><strong>'.$download['count'].'</strong> Downloads</td></tr>';

		$content.='<tr><th colspan="2">&nbsp;</th></tr>
		</table></div><br/>'.stripslashes($Page->about('content'));

		return $content;
	}
}

$Plugins->register(new downloadManager);
?>
