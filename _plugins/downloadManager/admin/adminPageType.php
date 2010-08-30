<?php



$content='
<script type="text/javascript">
        $(function() {
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
		<li><a href="/_inc/ajax.php?file=_plugins/downloadManager/admin/new-item.php&page_id='.$page_id.'">New Item</a></li>
        </ul>
        <div id="tabs-1">
                <div id="tabs-content">
			<table class="row-color">
			<tr><th>Items</th><th>Downloads</th></tr>';
			$downloads=rows('select name,count from '.PREFIX.'downloadManager where page_id='.$page_id);
			if(count($downloads)==0)
				$content.='No download items.';
			else{
				foreach($downloads as $download){
					$content.='<tr><td>'.$download['name'].'</td><td>'.$download['count'].'</td></tr>';
				}	
			}
			
$content.='		</table>
                </div>
	</div>
        <div id="tabs-2">
		<div id="tabs-content">';

		if($page_id!=0){
		        $Page=new Page($page_id);
        		$page_content=stripslashes($Page->about('content'));
		}

	$content.='<script type="text/javascript" src="/_inc/js/tinymce.jquery.min.js"></script>
		<script type="text/javascript" src="/_inc/js/tiny_mce.js"></script>

		<p><textarea id="page-content" name="PageContent" class="tinymce" style="width:100%">'.@$page_content.'</textarea></p>
		</div>
	</div>
</div>
';


?>