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

		$this->assertFalse( true );

		$FileManager = FileManager::getInstance( );
		$User = User::getInstance( );

		/**
		 * checks if user has permission to write to a system
		 * directory - should never be true
		 */
		$this->assertFalse( $FileManager->hasPerm( 'users/', 'w' ) );

		/**
		 * checks if the user has permission to read their own
		 * home files directory. should always be true
		 */
		$this->assertFalse( !$FileManager->hasPerm( 'users/ ' . $User->id( ) . '/', 'r' ) );


	}	

}
