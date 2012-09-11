<?php

/*      
 *      Furasta.Org Version 1.0
 *      File: admin/settings/users/list.php
 *      Developer: Conor Mac Aoidh <http://macaoidh.name>
 *      Report Bugs: bugs@furasta.org
 */

$head='
<script type="text/javascript">
$(document).ready(function(){
	$(".delete").click(function(){ confirm_delete(this.href);return false; });
});
</script>
';

$Template->add('head',$head);

$content='
<span style="float:right"><a href="users.php?page=new" id="new-user"><img src="/_inc/img/new-user.png" style="float:left"/> <h1 class="image-left">New User</h1></a></span>
<span><img src="/_inc/img/users.png" style="float:left"/> <h1 class="image-left">Users</h1></span>
<br/>
<table id="users" class="row-color">
	<tr class="top_bar"><th>Name</th><th>Email</th><th>Status</th><th>Delete</th></tr>
';

$query=query('select id,name,email,user_group from '.DB_USERS.' order by id');
while($row=mysql_fetch_array($query)){
	$id=$row['id'];
	$perm=$row['user_group'];
	$href='<a href="settings.php?page=users&action=edit&id='.$id.'" class="list-link">';
	$content.='<tr>
			<td class="first">'.$href.$row['name'].'</a></td>
			<td>'.$href.$row['email'].'</a></td>
			<td>'.$href.$perm.'</a></td>
			<td><a href="settings.php?page=users&action=delete&id='.$id.'" id="delete-'.$id.'"class="delete"><img src="/_inc/img/delete.png" title="Delete User" alt="Delete User"/></a></td>
		</tr>';
}

$content.='<tr><th colspan="6"></th></tr></table>';

$Template->add('content',$content);


?>
