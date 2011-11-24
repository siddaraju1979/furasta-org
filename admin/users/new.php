<?php

/*      
 *      Furasta.Org Version 1.0
 *      File: admin/settings/users/new.php
 *      Developer: Conor Mac Aoidh <http://macaoidh.name>
 *      Report Bugs: bugs@furasta.org
 */

$head='
<script type="text/javascript">
$(document).ready(function(){
	$("#User").click(function(){
                var errors=required(["Name","Email","Password","Repeat-Password"]);
                if(errors==0){
                        errors=email_format("Email");
                        if(errors==0){
                                errors=match("Password","Repeat-Password");
                                if(errors==0)
                                        errors=minlength("Password",6);
                        }
                }
                if(errors!=0)
                        return false;
	});
//        $("#status").change(function(){
//		if($("#status").val()=="User"){
  //      	        $("#permissions").html("<td>Can Change Settings:</td><td><input type="checkbox" name="Can-Change-Settings" value="1"/></td>");
//		}
//		else
//			fAlert($("#status").val());
  //      });
});
</script>
';

$Template->add('head',$head);

$content='
<span><img src="/_inc/img/new-user.png" style="float:left"/> <h1 class="image-left">New User</h1></span>
<br/>
	<form method="post">
		<table id="config-table" class="row-color">
			<col width="50%"/>
			<col width="50%"/>
			<tr>
				<th colspan="2">User Options</th>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="Name" value=""/></td>
			</tr>
                	<tr>
				<td>Email:</td>
				<td><input type="text" name="Email" value=""/></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="Password" value=""/></td>
			</tr>
                        </tr>
                        <tr>
                                <td>Repeat Password:</td>
                                <td><input type="password" name="Repeat-Password" value=""/></td>
                        </tr>
			<tr>
				<td>Status:</td>
				<td><select name="Status" id="status">
					<option default="default">Admin</option>
					<option>User</option>
				</select></td>
			</tr>
		</table>
	</form>
<input type="submit" name="add" id="User" class="submit right" value="Add User" style="margin-right:10%"/>
<br style="clear:both"/>
';

$Template->add('content',$content);


?>
