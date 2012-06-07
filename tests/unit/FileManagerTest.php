<?php

/**
 * File Manager Test, Furasta.Org
 *
 * runs tests on the filemanager class located at _inc/class/FileManager.php
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    tests
 */

// load config file
$_SESSION[ 'user' ][ 'id' ] = 1;
$_SESSION[ 'user' ][ 'login' ] = true;
require dirname( __FILE__ ) . '/../../trunk/_inc/define.php';

class FileManagerTest extends PHPUnit_Framework_Testcase{

	/**
	 * hasPerm
	 *
	 * tests the hasPerm method
	 *
	 * @test
	 */
	public function hasPerm( ){

		$FileManager = FileManager::getInstance( );
		$User = User::getInstance( );

		/**
		 * checks if user has permission to write to a system
		 * directory - should never be true
		 */
		$success = $FileManager->hasPerm( 'users/', 'w' );

		// assert that the return value is false		
		$this->assertFalse( $success );

		if( !$success ){ // make sure it's for the right reason, check error id
			$error = $FileManager->error( );
			$this->assertEquals( 8, $error[ 'id' ] );	
		}

	}	

}
