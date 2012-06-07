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
	 * holds an instance of the fm and user
	 */
	private $fm;
	private $us;

	/**
	 * __construct
	 * 
	 * saves an instance of the file manager
	 * and user in the class
	 */
	public function __construct( ){
		$this->fm = FileManager::getInstance( );
		$this->us = User::getInstance( );
	}

	/**
	 * hasPerm
	 *
	 * tests the hasPerm method
	 *
	 * @test
	 */
	public function hasPerm( ){

		/**
		 * checks if user has permission to write to a system
		 * directory - should never be true
		 */
		$success = $this->fm->hasPerm( 'users/', 'w' );

		// assert that the return value is false		
		$this->assertFalse( $success );

		if( !$success ){ // make sure it's for the right reason, check error id
			$error = $this->fm->error( );
			$this->assertEquals( 8, $error[ 'id' ] );	
		}

	}	


	/**
	 * addDir
	 *
	 * tests the addDir method
	 *
	 * @test
	 */
	public function addDir( ){

		/**
		 * attempt to mkdir in users home dir
		 */
		$success = $this->fm->addDir( 'users/1/test' );

		if( !$success ){
			$error = $this->fm->error( );
			echo $error[ 'description' ];
		}

		$this->assertFalse( !$success );

	}


	/**
	 * removeDir
	 *
	 * tests the removeDir method
	 *
	 * @test
	 */
	public function removeDir( ){

		/**
		 * attempt to mkdir in users home dir
		 *
		$success = $fm->addDir( 'users/1/test' );

		$this->assertFalse( !$success );
		*/
	}
}
