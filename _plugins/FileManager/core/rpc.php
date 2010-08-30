<?php
/**
  * SaorFM rpc file
  *
  * PHP Version 5
  *
  * This file holds the SaorFM rpc handler, which uses
  * the saorFM core
  *
  * @category SaorFM
  * @package  rpc
  * @authors  Conor Mac Aoidh <conormacaoidh@gmail.com>
  * @license  http://www.opensource.org/licenses/bsd-license.php BSD License
  * @link     http://www.saorfm.org/
 */

$action=@$_GET['action'];

if($action=='')
	die();

/**
 * require SaorFM core and create an instance
 * of the core class
 */
require 'SaorFM.php';

$saorFM=new SaorFM();

/**
 * switch according to the desired action
 */
switch($action){
	case 'move':
		$file=@$_GET['file'];
		$to=@$_GET['to'];

		$saorFM->move($file,$to);
		
		/**
		 * die with the last known error if isset
		 */
		$error=$saorFM->error;
		if($error!='')
			die($error);

		die('0');
	break;
	case 'delete':
		$file=@$_GET['file'];

		$saorFM->delete($file);

		/**
		 * die with the last known error if isset
		 */
		$error=$saorFM->error;
		if($error!='')
			die($error);

		die('0');
	break;
}