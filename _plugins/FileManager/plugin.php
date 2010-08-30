<?php

/**
 * SaorFM Plugin File, Furasta.Org
 *
 * Plugin to impliment the saorFM core via RPC
 * in the admin area of the CMS.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version    1.0
 * @package    saorFM
 */

class FileManager{
	public $name='File Manager';
	public $description='Plugin to impliment the saorFM core via RPC.';
	public $version=1;

	public function adminMenu($menu_items,$url){
		$menu_items['File Manager']=array('url'=>$url);
		return $menu_items;
	}

	public function adminPage(){
		require 'admin/explorer.php';
		return $content;
	}
}

$Plugins->register(new FileManager);
?>