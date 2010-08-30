<?php

class frontendEditor{
        var $name='Frontend Editor';
	var $description='Creates an admin panel which enables editing in the frontend.';
	var $version=1;
	var $frontendTemplateFunction='frontend_editor';
        var $email='conormacaoidh@gmail.com';
        var $href='http://blog.macaoidh.name';



	function frontendTemplateFunction($Page){
	        $id=@$_SESSION['user_id'];
        	if($id==0)
                	return;
	        $User=new User($_SESSION['user_id']);
	        $content='<div id="admin-editor">
        	        <ul>
                	        <li><a href="/admin/">Admin Area</a></li>
                        	<li><a href="/admin/pages.php?page=edit&id=">Edit This Page</a></li>
	                        <li><a href="/admin/logout.php">Logout</a></li>
        	                <li><a href="/admin/users.php?page=list">'.$User->about('name').'</a></li>
                	</ul>
	        </div>';
        	return $content;
	}
}

$Plugins->register(new frontendEditor);
?>
