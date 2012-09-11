<?php

echo '
<script type="text/javascript">
$( document ).ready( function() {
                $("#tabs").tabs({
                        ajaxOptions:{
                                error:function(xhr,status,index,anchor) {
                                        $(anchor.hash).html("Could not load page. Please check your internet connection.");
                                }
                        }
                });
});
</script>

<div id="tabs">
        <ul>
                <li><a href="#tabs-1">Download Items</a></li>
                <li><a href="#tabs-2">Page Content</a></li>
		<li><a href="/_inc/ajax.php?file=_plugins/Download-Manager/admin/new-item.php&page_id='.$page_id.'">New Item</a></li>
        </ul>
        <div id="tabs-1">
                <div id="tabs-content">
			<table class="row-color">
			<tr><th>Items</th><th>Downloads</th></tr>';
			$downloads=rows('select name,count from '.DB_PREFIX.'downloadManager where page_id='.$page_id);
			if(count($downloads)==0)
				echo 'No download items.';
			else{
				foreach($downloads as $download){
					echo '<tr><td>'.$download['name'].'</td><td>'.$download['count'].'</td></tr>';
				}	
			}
			
echo '		</table>
                </div>
	</div>
        <div id="tabs-2">
		<div id="tabs-content">';

		if($page_id!=0){
		        $Page=row( 'select content from ' . DB_PAGES . ' where id=' . $page_id );
        		$page_content=stripslashes($Page['content']);
		}

echo '<script type="text/javascript" src="' . SITE_URL .'_inc/js/tinymce.jquery.min.js"></script>
		<script type="text/javascript" src="' . SITE_URL . '_inc/js/tiny_mce.js"></script>

		<p><textarea id="page-content" name="PageContent" class="tinymce" style="width:100%">'.@$page_content.'</textarea></p>
		</div>
	</div>
</div>
';
?>
