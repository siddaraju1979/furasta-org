<?php

/**
 * Plugins Class, Furasta.Org
 *
 * This file collects information that is to be displayed
 * by various active plugins.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    plugin_architecture
 */

/**
 * Plugins 
 *
 * This class manages the retrieval of plugin data
 * from active plugin files. Each active plugin.php file 
 * is loaded and each plugin registers itself. Then when
 * the plugin hooks are loaded the functions return the
 * appropriate information.
 *
 * There are various different hooks and filters in the
 * plugin architecture. A hook is a slot to execute a
 * piece of code during the runtime of the CMS, a filter is
 * a slot to change some of the output of the CMS.
 *
 * PLUGIN FILE 
 * 
 * Below are all the supported variables and functions
 * which should be contained in the plugin file. Note that
 * where ever functions are used methods can also be used. To use
 * methods use the format: array( 'classname', 'methodname' )
 * 
 * # array keys
 * $plugin = array(
 *       'name' => '', # required
 *       'description' => '',
 *       'version' => '', # required
 *       'email' => '',
 *       'href' => '',
 *       'importance' => '',
 *
 *       # admin functions
 *       'admin'=> array(
 *               # $menu_array, $url
 *               'filter_menu' => 'function_name'
 *
 *               'page_type' => array(
 *                       'name' => '',
 *
 *                       # $page_array
 *                       'function' => 'function_name',
 *               ),
 *
 *               'page' => 'function_name',
 *
 * 		 # exectues just as the page starts loading
 *               'on_load' => 'function_name',
 *
 *		 # executes when the page is finished loading
 *		 'on_finish' => 'function_name',
 *
 * 		 'filter_lang' => 'function_name',
 *
 * 		 'filter_lang_errors' => 'function_name',
 *
 *               'filter_page_content' => 'function_name',
 *
 *		 'filter_group_permissions' => 'function_name',
 *
 *               'overview_item' => array(
 *                       'name' => '',
 *
 *                       # $page_array
 *                       'function' => 'function_name',
 *               ),
 *
 *		 # allows plugins to have custom templates in the
 *		 # admin area for their specific plugin page only
 *		 'template_override' => 'file_name',
 *
 *       ),
 *
 *       # frontend functions
 *       'frontend' => array(
 *               # $page_array
 *               'page_type' => 'function_name',
 *
 * 		 # exectues just as the page starts loading
 *               'on_load' => 'function_name',
 *
 *		 # executes when the page is finished loading
 *		 'on_finish' => 'function_name',
 * 
 *               'template_function' => array(
 *                       'name' => '',
 *
 *                       # $page_array
 *			 # @todo add support for objects here
 *                       'function' => 'function_name',
 *               ),
 *
 *               'filter_page_content' => 'function_name',
 *
 *               'filter_metadata' => 'function_name',
 *
 *       ),
 *
 *       # sitewide functions
 *       'general' => array(
 *
 *               # $htaccess
 *               'filter_htaccess' => 'function_name',
 *
 *               # $robots
 *               'filter_robots' => 'function_name',
 *
 *               # $settings
 *               'filter_settings_rewrite' => 'function_name',
 *
 *               # $sitemap
 *               'filter_sitemap' => 'function_name',
 *       ),
 *
 * );
 *
 * Note that there are two required fields for
 * writing a plugin - name and version. Filters
 * are prefixed with 'filter'. It is advisable to
 * prefix all plugin functions with the name of the
 * plugin to avoid conflicts.
 * 
 * Please look at specific functions below for more
 * details.
 *
 * CREATING A PLUGIN HOOK / FILTER
 *  
 * Hooks and filters can be created within a plugin
 * so that other plugins can extend their functionality.
 * To do so there are two dedicated functions in the
 * plugin class.
 * 
 * $Plugin->hook( "hookarea", "hookname" );
 * $Plugin->filter( "filterarea", "filtername", $to_be_filtered );
 * 
 * If a plugin contains hooks, it is important
 * to consider the $importance var. The plugin
 * should have a high importance so that it is
 * loaded before plugins which extend it.
 *
 * @todo add support for $importance var, add the importance as array key in $this->plugins, and order foreaches by key 
 */
class Plugins{
        /**
         * instance
         * 
         * holds the Plugins instance
         *
         * @var object
         * @static
         * @access public
         */
        private static $instance;

	/**
	 * plugins 
	 * 
	 * An array of all plugins registered during runtime.
	 *
	 * @var array
	 * @access public
	 * @todo make this private
	 */
	public $plugins = array();

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

                        self::$instance = new Plugins() ;
                        return self::$instance;
                }

                /**
                 *  checks if an instance exists
                 */
                if(!self::$instance)
                        self::$instance = new Plugins();

                return self::$instance;
        }


	/**
	 * register 
	 * 
	 * Function through which plugins are registered. Plugins
	 * must be registered for the CMS to even recognise them.
	 *
	 * @param mixed $plugin , the name of the plugin
	 * @access public
	 * @return bool true or void
	 */
	public function register( $plugin ){

		/**
		 * check if plugin being registered is defined already,
		 * if its a new plugin check that the manditory vars are set
		 */
		if( !in_array( $plugin, $this->plugins ) ){
			if( isset( $plugin[ 'name' ] ) && isset( $plugin[ 'version' ] ) ){
				array_push( $this->plugins, $plugin );
				return true;
			}
			error('Plugins must define at least the name and version variables.','Plugin Error');
		}
		else
			error('Plugins cannot be defined twice.','Plugin Error');
	}

	/**
	 * refactor
         *
         * re-order the plugins array by order
         * of the $importance var
	 * 
	 * @access public
	 * @return void
	 * @todo finish this
	 */
	public function refactor( ){

		$plugins = array( );

		foreach( $this->plugins as $plugin ){

			if( isset( $plugin[ 'importance' ] ) )
				$plugins[ $plugin[ 'importance' ] ] = $plugin;
			else
				$plugins[ '1' ] = $plugin;

		}

		die( print_r( $plugins ) );
	}

	/**
	 * adminMenu
	 *
	 * Returns a filtered version of the original $menu_items array.
	 i 
	 * @param array $menu_items , items currently on the menu
	 * @access public
	 * @return array
	 */
	public function adminMenu( $menu_items ){

		$menu = $menu_items;

		foreach( $this->plugins as $plugin ){

			$url = SITEURL . 'admin/plugin.php?p_name=' . str_replace( ' ', '-', $plugin[ 'name' ] ) . '&';

			if( isset( $plugin[ 'admin' ][ 'filter_menu' ] ) ){
				/**
				 * default method using functions
				 */
				if( function_exists( $plugin[ 'admin' ][ 'filter_menu' ] ) )
	                        	$menu = call_user_func_array( $plugin[ 'admin' ][ 'filter_menu' ], array( $menu, $url ) );

				/**
				 * using methods
				 */
				elseif( method_exists( @$plugin[ 'admin' ][ 'filter_menu' ][ 0 ], @$plugin[ 'admin' ][ 'filter_menu' ][ 1 ] ) )
					$menu = call_user_func_array( $plugin[ 'admin' ][ 'filter_menu' ], array( $menu, $url ) );					
			
			}
                }

                return $menu;
	}

	/**
	 * adminPageTypes
	 *
	 * returns an array of all the page types 
	 * 
	 * @access public
	 * @return array
	 */
	public function adminPageTypes( ){
		$types = array( );

		foreach( $this->plugins as $plugin ){
			if( isset( $plugin[ 'admin' ][ 'page_type' ][ 'name' ] ) )
                        	array_push( $types, $plugin[ 'admin' ][ 'page_type' ][ 'name' ] );
                }

		return $types;
	}

	/**
	 * adminPageType
	 *
	 * executes a specific page type 
	 * 
	 * @param string $type 
	 * @param int $page_id 
	 * @access public
	 * @return void
	 */
	public function adminPageType( $type, $id ){

		foreach( $this->plugins as $plugin ){

			/**
			 * check if correct plugin
			 */
			if( @$plugin[ 'admin' ][ 'page_type' ][ 'name' ] != $type || !isset( $plugin[ 'admin' ][ 'page_type' ][ 'function' ] ) )
				continue;

			/**
			 * using functions
			 */
			if( function_exists( $plugin[ 'admin' ][ 'page_type' ][ 'function' ] ) )
                               	return call_user_func( $plugin[ 'admin' ][ 'page_type' ][ 'function' ], $id );

			/**
			 * using methods
			 */
			elseif( method_exists( @$plugin[ 'admin' ][ 'page_type' ][ 'function' ][ 0 ], @$plugin[ 'admin' ][ 'page_type' ][ 'function' ][ 1 ] ) )
				return call_user_func( $plugin[ 'admin' ][ 'page_type' ][ 'function' ], $id );

                }

	}

	/**
	 * adminPage
	 *
	 * cycles through installed plugins and executes
	 * the correct one for the current page
	 * 
	 * @param string $p_name 
	 * @access public
	 * @return void
	 */
	public function adminPage( $p_name ){

		foreach( $this->plugins as $plugin ){

			/**
			 * if not correct plugin, continue
			 */
			if( $plugin[ 'name' ] != $p_name || !isset( $plugin[ 'admin' ][ 'page' ] ) )
				continue;

			/**
			 * using functions
			 */
			if( function_exists( $plugin[ 'admin' ][ 'page' ] ) )
				call_user_func( $plugin[ 'admin' ][ 'page' ] ); 

			/**
			 * using methods
			 */
			elseif( method_exists( @$plugin[ 'admin' ][ 'page' ][ 0 ], @$plugin[ 'admin' ][ 'page' ][ 1 ] ) )
				call_user_func( $plugin[ 'admin' ][ 'page' ] );

			/**
			 * plugin not found
			 */
			else{
				$dev = ( isset( $plugin[ 'email' ] ) ) ? ': <a href="mailto:'.$plugin[ 'email' ] . '">' . $plugin[ 'email' ] . ' </a>':'.';
				$href = ( isset( $plugin[ 'url' ] ) ) ? '<a href="' . $plugin[ 'url' ] . '">' . $plugin[ 'name' ] . '</a>' : $plugin[ 'name' ];
				error('The plugin \'<em>'.$href.'</em>\' has encountered an error and has had to quit. Please report this to the plugin developer'.$dev,'Plugin Error');
			}
		}	
	}

	/**
	 * registeredPlugins
	 *
	 * This function allows access to the
	 * array of registered plugins
	 * 
	 * @access public
	 * @return array
	 */
	public function registeredPlugins( ){

		return $this->plugins;

	}

	/**
	 * frontendTemplateFunctions
	 *
	 * returns an array of the names
	 * of plugins which have registered
	 * frontend template functions 
	 * 
	 * @access public
	 * @return array
	 * @todo order by $importance var
	 */
	public function frontendTemplateFunctions(){
		$functions = array();

                for( $i = 0; $i < count( $this->plugins ); $i++ ){
                        if( isset( $this->plugins{ $i }{ 'frontend' }{ 'template_function' }{ 'name' } ) && function_exists( @$this->plugins{ $i }{ 'frontend' }{ 'template_function' }{ 'function' } ) )
					$functions[ $this->plugins{ $i }{ 'frontend' }{ 'template_function' }{ 'name' } ] = $this->plugins{ $i }{ 'frontend' }{ 'template_function' }{ 'function' };					

                }

		return $functions;
	}

	/**
	 * frontendPageType 
	 * 
	 * executes the corresponding plugin function
	 * to the frontend page type accessed
	 *
	 * @param string $type 
	 * @param int $page_id
	 * @access public
	 * @return unknown
	 */
	public function frontendPageType( $type, $page_id ){

                /**
                 * search plugins 
                 */
                foreach( $this->plugins as $plugin ){

			/**
			 * if not correct plugin, continue
			 */
			if( @$plugin[ 'admin' ][ 'page_type' ][ 'name' ] != $type || !isset( $plugin[ 'frontend' ][ 'page_type' ] ) )
				continue;

                        /**
                         * is correct, attempting functions 
                         */
                        if( function_exists( $plugin[ 'frontend' ][ 'page_type' ] ) )
                                return call_user_func( $plugin[ 'frontend' ][ 'page_type' ], $page_id );

			/**
			 * using methods 
			 */
			elseif( method_exists( @$plugin[ 'frontend' ][ 'page_type' ][ 0 ], @$plugin[ 'frontend' ][ 'page_type' ][ 1 ] ) )
				return call_user_func( $plugin[ 'frontend' ][ 'page_type' ][ 'function' ], $page_id );

                }

	}

	/**
	 * adminOverviewItems 
	 * 
	 * returns an array of the registered
	 * overview items
	 *
	 * @access public
	 * @return array
	 */
	public function adminOverviewItems( ){
                $items = array( );
                $num = 0;

                /**
                 * search plugins 
                 */
                foreach( $this->plugins as $plugin ){

			/**
			 * if name isn't set, continue
			 */
			if( !isset( $plugin[ 'admin' ][ 'overview_item' ][ 'name' ] ) )
				continue;

			/**
			 * otherwise register the overview item
			 */
			$items[ $num ][ 'name' ] = $plugin[ 'admin' ][ 'overview_item' ][ 'name' ];
			$items[ $num ][ 'id' ] = str_replace( ' ', '-', $plugin[ 'name' ] );
			$items[ $num ][ 'status' ] = 'open';

			$num++;

                }

                return $items;
	}

	/**
	 * adminOverviewItemContent 
	 * 
	 * returns the content of a specific
	 * overview item
	 *
	 * @param string $item_id
	 * @access public
	 * @return string
	 * @todo use $Template class lang files for error report below
	 */
	public function adminOverviewItemContent( $item_id ){

		/**
		 * search plugins for match
		 */
		foreach( $this->plugins as $plugin ){
	
			/**
			 *  if match found, return content
			 */
			if( str_replace( ' ', '-', $plugin[ 'name' ] ) == $item_id ){

				/**
				 * using functions
				 */
				if( function_exists( @$plugin[ 'admin' ][ 'overview_item' ][ 'function' ] ) )
					return call_user_func( $plugin[ 'admin' ][ 'overview_item' ][ 'function' ] );

				/**
				 * using methods 
				 */
				if( method_exists( @$plugin[ 'admin' ][ 'overview_item' ][ 'function' ][ 0 ], @$plugin[ 'admin' ][ 'overview_item' ][ 'function' ][ 1 ] ) )
					return call_user_func( $plugin[ 'admin' ][ 'overview_item' ][ 'function' ] );

			}
				
		}
		
		/**
		 * no matches found, or plugin function or method not set correctly
		 */
		return 'plugin not found';
	}

	/**
	 * hook 
	 * 
	 * Allows access to plugin hooks.
	 *
	 * @param string $area 
	 * @param string $name 
	 * @param string or array $params, optional
	 * @access public
	 * @return void
	 */
	public function hook( $area, $name, $params = false ){

		/**
		 * search plugins
		 */
		foreach( $this->plugins as $plugin ){

			/**
			 * check if correct plugin
			 */
			if( !isset( $plugin[ $area ][ $name ] ) )
				continue;

			/**
			 * using functions
			 */
			if( function_exists( $plugin[ $area ][ $name ] ) ){
                                if( is_array( $params ) )
					call_user_func_array( $plugin[ $area ][ $name ], $params );
				else
					call_user_func( $plugin[ $area ][ $name ], $params );
			}

                        /**
                         * using methods 
                         */
                        elseif( method_exists( @$plugin[ $area ][ $name ][ 0 ], @$plugin[ $area ][ $name ][ 1 ] ) ){
                                if( is_array( $params ) )
                                        call_user_func_array( $plugin[ $area ][ $name ], $params );
                                else
                                        call_user_func( $plugin[ $area ][ $name ], $params );
			}

		}

	}

        /**
         * filter 
         * 
	 * Allows access to plugin filters.
	 *
	 * set is_array to true if you want to pass an array of params rather
	 * than one parameter which is an array
	 *
         * @param string $area 
         * @param string $name 
         * @param string $to_be_filtered
	 * @param string $is_array optional
         * @access public
         * @return void
         */
        public function filter( $area, $name, $to_be_filtered, $is_array = false ){

		/**
		 * var to hold filtered content
		 */
		$content = $to_be_filtered;

                /**
                 * search plugins
                 */
                foreach( $this->plugins as $plugin ){

                        /**
                         * check if correct plugin
                         */
                        if( !isset( $plugin[ $area ][ $name ] ) )
                                continue;

                        /**
                         * using functions
                         */
                        if( function_exists( $plugin[ $area ][ $name ] ) )
                                $content = ( $is_array ) ?
						call_user_func_array( $plugin[ $area ][ $name ], $content ) :
						call_user_func( $plugin[ $area ][ $name ], $content );

                        /**
                         * using methods 
                         */
                        elseif( method_exists( @$plugin[ $area ][ $name ][ 0 ], @$plugin[ $area ][ $name ][ 1 ] ) )
                                $content = ( $is_array ) ?
                                                call_user_func_array( $plugin[ $area ][ $name ], $content ) :
                                                call_user_func( $plugin[ $area ][ $name ], $content );

                }

		return $content;

        }

	/**
	 * plugins
	 *
	 * returns the plugins array, or if $name is
	 * provided returns details on a particular
	 * plugin
	 *
	 * @params string $name optional
	 * @access public
	 * @return array
	 */
	public function plugins( $name = false ){
		if( $name ){
			foreach( $this->plugins as $plugin ){
				if( $plugin[ 'name' ] == $name )
					return $plugin;
			}
		}
		return $this->plugins;
	}

}
?>
