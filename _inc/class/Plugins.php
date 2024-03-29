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
 * ======== PLUGIN FILE ========
 * 
 * Below are all the supported variables and functions
 * which should be contained in the plugin file.
 * 
 * # array keys
 * $plugin = array(
 *       'name' => '', # required
 *       'description' => '',
 *       'version' => '', # required
 *       'dependencies' => array( 'plugin_1', 'plugin_2' ),
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
 *		 # $Page
 *               'filter_page_vars' => 'function_name',
 *
 *               'filter_metadata' => 'function_name',
 *
 *		 # $breadcrumbs
 *		 'filter_breadcrumbs' => 'function_name',
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
 *
 * 		 # jobs
 * 		 'jobs' => array(
 * 			array( '+1 minute', 'function_name' ),	// executes close to every minute
 *			array( '+1 year', 'function_name' )	// executes close to every year
 *		 )
 *
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
 * ======== DEPENDENCIES ========
 *
 * 
 *
 * ======== CREATING A PLUGIN HOOK / FILTER ========
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
 * ======== JOBS ========
 * 
 * Jobs are supposed to emulate the cron program on linux systems.
 * Cron allows you to set tasks which should be executed at given
 * times. Jobs work in much the same way, the key difference is that
 * you need a steady stream of vistors to make sure the jobs get run
 * on time. IE, the jobs are only run when someone loads the website
 * and the job program determines that they are due to be performed.
 * There is no guarantee that the jobs will, in fact, be run on time,
 * but merely at the next available opportunity.
 *
 * The first parameter for a job is the rate at which the job should
 * be executed. This is calculated using strtotime so look at the
 * strtotime for documentation: http://php.net/manual/en/function.strtotime.php
 * 
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
	 * daysofmonth
	 *
	 * an array of values accepted by the jobs function
	 * outside of the normal strtotime values
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	static private $daysofmonth = array(
		'01', '02', '03', '04', '05', '06', '07', '08', '09', '10',
		'11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
		'21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
		'31', 'last'
	);

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
	 * @param string $p_name
	 * @param array $plugin
	 * @access public
	 * @return bool true or void
	 */
	public function register( $p_name, $plugin ){

		/**
		 * check if plugin being registered is defined already,
		 * if its a new plugin check that the manditory vars are set
		 */
		if( !in_array( $plugin, $this->plugins ) ){
			if( isset( $plugin[ 'name' ] ) && isset( $plugin[ 'version' ] ) ){
				$this->plugins{ $p_name } = $plugin;
				return true;
			}
			error('The plugin "' . $p_name . '" must define at least the name and version variables.','Plugin Error');
		}

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

			$url = SITE_URL . 'admin/plugin.php?p_name=' . str_replace( ' ', '-', $plugin[ 'name' ] ) . '&';

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
			if( isset( $plugin[ 'admin' ][ 'page_type' ] ) ){

				if( is_array( @$plugin[ 'admin' ][ 'page_type' ] ) ){ // multiple page types support
					foreach( $plugin[ 'admin' ][ 'page_type' ] as $type )
						array_push( $types, $type[ 'name' ] );
				}
				else
	                        	array_push( $types, @$plugin[ 'admin' ][ 'page_type' ][ 'name' ] );
			}
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

			if( is_array( @$plugin[ 'admin' ][ 'page_type' ] ) ){ // plugin has multiple page types
				foreach( $plugin[ 'admin' ][ 'page_type' ] as $t ){
					if( $t[ 'name' ] != $type )
						continue;

					if( !isset( $t[ 'function' ] ) ) // use normal page type for admin area
						return false;

					if( function_exists( @$t[ 'function' ] ) )
						return call_user_func( $t[ 'function' ], $id );

					if( is_array( @$t[ 'function' ] ) && method_exists( $t[ 'function' ][ 0 ], $t[ 'function' ][ 1 ] ) );
						return call_user_func( $t[ 'function' ], $id );
					
				}
			}
			else{ // plugin has one page type

				/**
				 * check if correct plugin
				 */
				if( @$plugin[ 'admin' ][ 'page_type' ][ 'name' ] != $type 
					|| !isset( $plugin[ 'admin' ][ 'page_type' ][ 'function' ] ) )
					continue;

				/**
				 * using functions
				 */
				if( function_exists( $plugin[ 'admin' ][ 'page_type' ][ 'function' ] ) )
	                               	return call_user_func( $plugin[ 'admin' ][ 'page_type' ][ 'function' ], $id );

				/**
				 * using methods
				 */
				elseif( is_array( @$plugin[ 'admin' ][ 'page_type' ][ 'function' ] ) 
					&& method_exists( $plugin[ 'admin' ][ 'page_type' ][ 'function' ][ 0 ],
					@$plugin[ 'admin' ][ 'page_type' ][ 'function' ][ 1 ] ) )
					return call_user_func( $plugin[ 'admin' ][ 'page_type' ][ 'function' ], $id );

			}

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
	 * frontendTemplateFunctions
	 *
	 * returns an array of the names
	 * of plugins which have registered
	 * frontend template functions 
	 * 
	 * @access public
	 * @return array
	 */
	public function frontendTemplateFunctions( ){

		$functions = array( );

		foreach( $this->plugins as $plugin ){
			if( isset( $plugin[ 'frontend' ][ 'template_function' ][ 'name' ] ) &&
				function_exists( $plugin[ 'frontend' ][ 'template_function' ][ 'function' ] ) ){
				$functions[ $plugin[ 'frontend' ][ 'template_function' ][ 'name' ] ] = $plugin[ 'frontend' ][ 'template_function' ][ 'function' ];
			}
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

			if( is_array( @$plugin[ 'frontend' ][ 'page_type' ] ) ){ // plugin has multiple page types
				for( $i = 0; $i < count( $plugin[ 'frontend' ][ 'page_type' ] ); $i++ ){
					$p = $plugin[ 'frontend' ][ 'page_type' ][ $i ];

					if( $plugin[ 'admin' ][ 'page_type' ][ $i ][ 'name' ] != $type )
						continue;

					if( function_exists( @$p ) )
						return call_user_func( $p, $page_id );

					if( method_exists( @$p[ 0 ], @$p[ 1 ] ) );
						return call_user_func( $p, $page_id );
					
				}
			}
			else{ // plugin has one page type
				if( $plugin[ 'admin' ][ 'page_type' ] != $type )
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
	 * @return array|bool
	 */
	public function plugins( $name = false ){
		if( $name ){
			if( isset( $this->plugins{ $name } ) )
				return $this->plugins{ $name };
			return false;
		}
		return $this->plugins;
	}

	/**
	 * jobs
	 *
	 * this function runs through registered jobs and checks
	 * if they are due to be run. if they are it runs them and
	 * records the time in the $SETTINGS array so that it can
	 * determine later when the job needs to be run again
	 *
	 * @access public
	 * @return void
	 */
	public function jobs( ){

		global $SETTINGS;
		$functions = array( ); // used to delete old jobs from the settings array
		$change = false; // should the config be updated

		foreach( $this->plugins as $plugin ){

			if( is_array( @$plugin[ 'general' ][ 'jobs' ] ) ){

				foreach( $plugin[ 'general' ][ 'jobs' ] as $job ){
					$rate = @$job[ 0 ];
					$function = @$job[ 1 ];
					$time = time( );

					if( !function_exists( $function ) ) // function does not exist
						continue;

					array_push( $functions, $function );

					/**
					 * if in settings array, check if it should be run now
					 * or not
					 */
					if( isset( $SETTINGS[ 'jobs' ][ $function ] ) ){
						$last = $SETTINGS[ 'jobs' ][ $function ];

						/**
						 * get next execution date
						 */
						if( in_array( $rate, self::$daysofmonth ) ){
							$day = date( 'dd' );
							if( $rate == 'last' ) // get last day of month
								$rate = date( 't', strtotime( 'today' ) );
							if( $day == $rate ) // this is the dayofmonth
								$next = $time - 1;
						}
						else
							$next = strtotime( $rate, $last );

						if( $time < $next ) // program has been run already, within range
							continue;
					}

					/**
					 * call function, if it returns true then
					 * update time in settings file
					 */
					$success = call_user_func( $function );
					if( $success ){
						$change = true;
						$SETTINGS[ 'jobs' ][ $function ] = $time;
					}
				}

			}

		}

		/**
		 * remove old jobs from settings array
		 */
		if( is_array( @$SETTINGS[ 'jobs' ] ) ){
			foreach( $SETTINGS[ 'jobs' ] as $name => $job ){
				if( !in_array( $name, $functions ) ){
					unset( $SETTINGS[ 'jobs' ][ $name ] );
					$change = true;
				}
			}
		}

		/**
		 * if settings_rewrite is required, do it!
		 */
		if( $change ){
			global $DB, $PLUGINS;
			settings_rewrite( $SETTINGS, $DB, $PLUGINS );
		}

	}

}
?>
