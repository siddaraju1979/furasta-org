<?php

$FileManager = new FileManager( );

// addFile test
$contents = 'these are some test file contents';
$path = 'users/1/newfile.txt';
$FileManager->addFile( $path, $contents );

// addDir test
//$path = 'users/1/testdir';
//$FileManager->addDir( $path );

// removeDir test
//$FileManager->removeDir( $path );

// readFile test
//die( $FileManager->readFile( $path ) );
?>
