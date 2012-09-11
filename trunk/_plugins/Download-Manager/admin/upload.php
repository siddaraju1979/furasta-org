<?php



if(isset($_POST['Upload'])){
	if(isset($_DB_FILES['Upload-Package'])&&file_exists($_DB_FILES['Upload-Package']['tmp_name'])){
		$file_name=md5($_DB_FILES['Upload-Package']['name']);
        	if(!move_uploaded_file($_DB_FILES['Upload-Package']['tmp_name'],USERS_FILES.'files/downloads/'.$file_name.'.zip'))
			error('There has been a fatal error moving the uploaded package, please check your permissions in the '.HOME.' directory','Upload Error');
	}
	else
                error('There was an error uploading the file, please try again by refreshing the page.','Upload Error');

        $name=addslashes($_POST['Name']);
	$page_id=addslashes($_GET['page_id']);

	query('insert into '.DB_PREFIX.'downloadManager values ("","'.$name.'","'.$file_name.'","0","'.$page_id.'")');
	header('location: /admin/pages.php?page=edit&id='.$page_id);
}

?>
