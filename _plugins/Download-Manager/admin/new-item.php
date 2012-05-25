<?php



$page_id=addslashes(@$_GET['page_id']);

echo '
<script type="text/javascript">
$(document).ready(function(){
	$("#Upload").click(function(){
		return required(["Name","Upload-Package"]);
	});
	$("#uploader").change(function(){
		var ext=$("#uploader").val().split(".");
		ext=ext[ext.length-1];
		if(ext!="zip"){
			$("#uploader").attr("value","");
			fAlert("You may only upload packages with the .zip extention.");
		}
	});
});
</script>

<form method="post" action="/_inc/ajax.php?file=_plugins/Download-Manager/admin/upload.php&page_id='.$page_id.'" enctype="multipart/form-data">
<table id="config-table" class="row-color">
        <col width="50%"/>
        <col width="50%"/>
        <tr>
                <th colspan="2">New Download Item</th>
        </tr>
        <tr>
                <td>Name:</td>
                <td><input type="text" name="Name" value="" class="input" /></td>
        </tr>
	<tr>
		<td>Upload:</td>
		<td><input id="uploader" type="file" name="Upload-Package"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" id="Upload" value="Upload" name="Upload" style="width:80px"/></td>
	</tr>
</table>
</form>
';

?>
