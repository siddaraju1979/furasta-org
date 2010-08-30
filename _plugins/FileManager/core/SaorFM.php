<?php
/**
  * SaorFM core
  *
  * PHP Version 5
  *
  * This file holds the core SaorFM class, which is the main controller
  * of the system
  *
  * @category SaorFM
  * @package  None
  * @authors  Kae Verens <kae@verens.com>, Conor Mac Aoidh <conormacaoidh@gmail.com>
  * @license  http://www.opensource.org/licenses/bsd-license.php BSD License
  * @link     http://www.saorfm.org/
 */

/**
  * main controller class of the SaorFM project
  *
  * @category SaorFM
  * @package  None
  * @authors  Kae Verens <kae@verens.com>, Conor Mac Aoidh <conormacaoidh@gmail.com>
  * @license  http://www.opensource.org/licenses/bsd-license.php BSD License
  * @link     http://www.saorfm.org/
**/
class SaorFM{
	private $config=array();

	// so that last error is recorded
	public $error;

	/**
	  * initialise the SaorFM object.
	  *
	  * @return string JSON object
	**/
	function init() {
		define('CORE',dirname(__FILE__).'/');
		define('FILES',CORE.'../user-files/');
		$config=CORE.'config.php';
		if (!file_exists($config)) {
			return '{"error":1}';
		}
		require $config;
		if (!isset($SaorFM_config)) {
			return '{"error":2}';
		}
		$this->config=json_decode($config);
		return '{}';
	}

	/**
	  * writes the SaorFM configuration to the config.php file
	  *
	  * @param array $config Current configuration of SaorFM.
	  *
	  * @return string JSON object
	**/
	function writeConfig($config) {
		$php="<?php\n"
			.'$SaorFM_config=\''
			.json_encode($config)
			.'\';';
		$config_file=CORE.'config.php';
		file_put_contents($config_file, $php);
		if (file_get_contents($config_file) !== $php) {
			return '{"error":3}';
		}

		$this->config=$config;
		return '{}';
	}

	/**
	 * move
	 *
	 * Moves a file to a different destination. Can also
	 * be used to move directories, or to rename files.
	 *
	 * @param string $source file to be moved
	 * @param string $destination to move file
	 * @return strun JSON object
	 */

	public function move($source,$destination){

		/**
		 * @todo create moveDir method
		 */
		if(is_dir(FILES.$source))
			return $this->moveDir($source,$destination);

		/**
		 * Add FILES on front of files to make
		 * sure they are within a designated area
		 */
		$source=FILES.$source;
		$destination=FILES.$destination;

		/**
		 * Check if files exist
		 */
		if(!file_exists($source)){
			$this->error='The file does not exist.';
			return '{"error":1}';
		}

		/**
		 * Series of elimination to provide
		 * quickest move function.
		 */
		if(!shell_exec('mv '.$source.' '.$destination)){
			if(!rename($source,$destination)){
				$this->error='Must grant write permission in
				source and destination locations.';
				return '{"error":2}';
			}
		}

		return '{}';
	}

	/**
	 * delete
	 *
	 * Deletes a directory. If $link is a directory,
	 * will delete directory and all sub dirs and files.
	 *
	 * @param string $link , file or dir to be deleted
	 * @return string JSON object
	 */
	public function delete($link){
		/**
		 * @todo create deleteDir method
		 */
		if(is_dir(FILES.$link))
			return $this->deleteDir($link);

		/**
		 * Add FILES on front of file to make
		 * sure its within a designated area.
		 */
		$link=FILES.$link;

		/**
		 * Check file exists.
		 */
		if(!file_exists($link)){
			$this->error='File does not exist.';
			return '{"error":1}';
		}

		/**
		 * Series of elimination to provide
		 * quickest delete function.
		 */
		if(!shell_exec('rm '.$link)){
			if(!unlink($link)){
				$this->error='Must grant write permission in
				source destination.';
				return '{"error:2"}';
			}
		}

		return '{}';
	}
}
