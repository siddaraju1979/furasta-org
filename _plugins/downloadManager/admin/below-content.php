<?php



if($id!=0){
        $Page=new Page($id);
        $page_content=$Page->about('content');
}

echo '
<script type="text/javascript" src="/_inc/js/tinymce.jquery.min.js"></script>
<script type="text/javascript" src="/_inc/js/tiny_mce.js"></script>

<p><textarea id="page-content" name="PageContent" class="tinymce" style="width:100%">'.@$page_content.'</textarea></p>
';

?>