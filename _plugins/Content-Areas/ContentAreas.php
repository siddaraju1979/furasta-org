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
 * ==== WIDGETS ====
 *
 * Widgets are methods of adding content to a content area.
 * The content areas plugin only provides one default widget -
 * the content widget - which allows content to be added to a
 * content area with tinymce.
 *
 * This class has built in support for adding your own widget
 * using a plugin. Some example types of widgets would be
 * image gallery, facebook anything like that.
 *
 * Widgets are created by adding to the $plugin array. For
 * an example look at the ContentAreas plugin file. Widgets
 * must be added to the plugin array in the following format:
 *
 * 	'admin' => array(
 * 		'content_area_widgets' => array(
 * 			array(
 *				'name' => 'Example Widget',
 *				'function' => 'function to be executed to display the widget'
 * 			)
 * 		)
 *	),
 * 	'frontend' => array(
 * 		'content_area_widgets' => array(
 * 			array(
 *				'name' => 'Example Widget',
 *				'function' => 'function to be executed to display the widget'
 * 			)
 * 		)
 *	)
 * 
 * As you can see from the example above, each plugin can have
 * multiple widgets
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
	 * widgets
	 * 
	 * holds an array of content area widgets
	 */
	private $widgets;

	/**
	 * __construct
	 *
	 * sets up the class, fetches content area
	 * info from the db
	 */
	public function __construct( ){
		$registered = rows( 'select * from ' . DB_PREFIX . 'content_areas' );
		$reg = array( );

		/**
		 * add registered content areas to $this->registered
		 */
		if( !empty( $registered ) ){
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
		}
		$this->registered = $reg;

		/**
		 * get all widgets from plugins
		 */
		$widgets = array( );
		$Plugins = Plugins::getInstance( );
		foreach( $Plugins->plugins( ) as $plugin ){
			if( isset( $plugin[ 'admin' ][ 'content_area_widgets' ] )
				&& isset( $plugin[ 'frontend' ][ 'content_area_widgets' ] ) ){
				$admin = $plugin[ 'admin' ][ 'content_area_widgets' ];
				$frontend = $plugin[ 'frontend' ][ 'content_area_widgets' ];

				foreach( $admin as $widget )
					$widgets[ $widget[ 'name' ] ][ 'admin' ] = $widget[ 'function' ];

				foreach( $frontend as $widget )
					$widgets[ $widget[ 'name' ] ][ 'frontend' ] = $widget[ 'function' ];
			}
		}

		$this->widgets = $widgets;
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
			if( isset( $this->registered{ $name }{ 'content' }{ $page_id } ) ){
				$widgets = $this->registered{ $name }{ 'content' }{ $page_id };
				$area = $page_id;
			}
			else{
				$widgets = $this->registered{ $name }{ 'content' }{ 'all' };
				$area = 'all';
			}
			$content = '';
			if( count( $widgets ) == 0 )
				return $content;
			foreach( $widgets as $widget ){
				$content .= $this->widgetContent( $widget[ 'name' ], $name, $widget[ 'id' ], $area ); 
			}
			return $content;
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
	 * @param string $content
	 * @access public
	 * @return bool
	 */
	public function addArea( $name, $page_id, $content = false ){
		if( $this->isRegistered( $name ) && $content != false ){
			// update existing area
			$id = $this->registered{ $name }{ 'id' };
			$content = addslashes( json_encode( $content ) );
			// add widgets for this area to widget options database
			query( 'update ' . DB_PREFIX . 'content_areas set content="' . $content . '" where id=' . $id );
			$this->registered{ $name }{ 'content' } = $content;
		}
		else{
			// create new area
			$this->registered{ $name }{ 'content' } = $content;
			$content = addslashes( json_encode( array( 'all' => array() ) ) );
			query( 'insert into ' . DB_PREFIX . 'content_areas values ( "", "' . $name . '", "' . $content . '", "" )' );
			$this->registered{ $name }{ 'id' } = mysql_insert_id( );
		}
	}

	/**
	 * deleteWidget
	 * 
	 * deletes a widget from a content area
	 * 
	 * @param string $area_name
	 * @param int $widget_id
	 * @access public
	 * @return bool
	 * @todo expand beyond 'all'
	 */
	public function deleteWidget( $area_name, $widget_id ){
		$area = $this->areas( $area_name );

		for( $i = 0; $i < count( $area[ 'content' ][ 'all' ] ); ++$i ){
			if( $area[ 'content' ][ 'all' ][ $i ][ 'id' ] == $widget_id ){
				unset( $area[ 'content' ][ 'all' ][ $i ] );
				return $this->addArea( $area_name, 'all', $area[ 'content' ] );
			}
		}
	}

	/**
	 * areas
	 *
	 * returns the array of registered content areas
	 *
	 * @param string $name, optional
	 * @access public
	 * @return array
	 */
	public function areas( $name = false ){
		if( $name ){
			if( isset( $this->registered{ $name } ) )
				return $this->registered{ $name };
			return false;
		}
		return $this->registered;
	}

	/**
	 * widgets
	 *
	 * returns an array of widgets or if name is supplied
	 * returns info on one widget
	 * 
	 * @param string $name, optional
	 * @access public
	 * @return array
	 */
	public function widgets( $name = false ){
		// return a specific widget
		if( $name ){
			if( isset( $this->widgets{ $name } ) )
				return $this->widgets{ $name };
			return false;
		}

		// return all widget info
		return $this->widgets;
	}

	/**
	 * widgetContent
	 *
	 * returns the content of a widget
	 *
	 * @param string $widget_name
	 * @param string $area_name
	 * @param string $id
	 * @param string $area optional
	 * @access public
	 * @return string
	 */
	public function widgetContent( $widget_name, $area_name, $id, $area = 'all' ){
		if( isset( $this->widgets{ $widget_name } ) && function_exists( $this->widgets{ $widget_name }{ 'frontend' } ) ){
			ob_start( );
			call_user_func_array( $this->widgets{ $widget_name }{ 'frontend' }, array( $area_name, $id, $area ) );
			$content = ob_get_contents( );
			ob_end_clean( );
			return $content;
		}
		return false;
	}

}
?>
