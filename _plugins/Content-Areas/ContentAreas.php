<?php

/**
 * Content Areas Plugin, Furasta.Org
 *
 * @author      Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence     http://furasta.org/licence.txt The BSD Licence
 * @version     1
 */

/**
 * ContentAreas
 *
 * the content areas class manages everything to
 * do with content areas 
 *
 * @todo add permissions support
 */
class ContentAreas{

	/**
	 * instance
	 *
	 * holds the instance of the class
	 */
	private static $instance;

	/**
	 * registered
	 *
	 * holds an array containing data on registered
	 * content areas
	 */
	private $registered = array( );

	/**
	 * __construct
	 *
	 * sets up the class, fetches content area
	 * info from the db
	 */
	public function __construct( ){
		$registered = rows( 'select * from ' . PREFIX . 'content_areas' );
		$reg = array( );

		for( $i = 0; $i < count( $registered ); ++$i ){
			$name =  $registered[ $i ][ 'name' ];
			$reg[ $name ] = array( );

			foreach( $registered[ $i ] as $area => $val ){

				if( $area == 'data' || $area == 'content' ){
					$reg[ $name ][ $area ] = json_decode( $val, true );
					continue;
				}
				$reg[ $name ][ $area ] = $val;

			}
		}

		$this->registered = $reg;
	}

	/**
	 * getInstance 
	 * 
	 * @static
	 * @param string $value , true if new instance should be made
	 * @access public
	 * @return instance
	 */
	public static function getInstance( $value = false ){

		/**
		 * if value is true a new instance is created
		 */
		if( $value == true ){
			if( self::$instance )
				self::$instance == '';

			self::$instance = new ContentAreas( );
			return self::$instance;
                }

		/**
		 *  checks if an instance exists
		 */
		if(!self::$instance)
			self::$instance = new ContentAreas( );

		return self::$instance;
	}

	/**
	 * isRegistered
	 *
	 * returns true if content area is registered, else false
	 *
	 * @param string $name
	 * @access public
	 * @return bool
	 */
	public function isRegistered( $name ){
		if( array_key_exists( $name, $this->registered ) )
			return true;

		return false;
	}

	/**
	 * getContent
	 *
	 * gets the content of the given content area for
	 * this page and returns it
	 *
	 * @access public
	 * @param string $name
	 * @param int $page_id
	 * @return string or bool
	 */
	public function getContent( $name, $page_id ){
		if( $this->isRegistered( $name ) ){
			if( isset( $this->registered{ $name }{ 'content' }{ $page_id } ) )
				return $this->registered{ $name }{ 'content' }{ $page_id };
			return $this->registered{ $name }{ 'content' }{ 'all' };
		}
		return false;
	}

	/**
	 * addArea
	 *
	 * adds an area to the content areas database
	 * if the area exists it adds a new page to the
	 * $content
	 *
	 * note: if area exists, page id is added to content but
	 * data is not altered
	 *
	 * @param string $name
	 * @param string or int $page_id
	 * @param string $data
	 * @access public
	 * @return bool
	 */
	public function addArea( $name, $page_id, $data ){
		if( $this->isRegistered( $name ) ){
			// update existing area
			$this->registered{ $name }{ 'content' }{ $page_id } = '';
			$content = $this->registered{ $name }{ 'content' };
			$id = $this->registered{ $name }{ 'id' };

			query( 'update ' . PREFIX . 'content_areas set content="' . $content . '" where id=' . $id );
		}
		else{
			// create new area
			$data = addslashes( json_encode( $data ) );
			$content = addslashes( json_encode( array( 'all' => '' ) ) );
			query( 'insert into ' . PREFIX . 'content_areas values ( "", "' . $name . '", "' . $content . '", "' . $data . '" )' );
			$this::getInstance( true );
		}
	}

	/**
	 * areas
	 *
	 * returns the array of registered content areas
	 *
	 * @access public
	 * @return array
	 */
	public function areas( ){
		return $this->registered;
	}

}
?>
