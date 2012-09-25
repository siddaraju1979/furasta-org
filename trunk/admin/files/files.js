/**
 * Files List Javascript, Furasta.Org
 *
 * Javascript for the file manager gui in the admin
 * area
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_files
 */

/**
 * global vars
 */
var filecrumbs;
var filecrumb;
var newfolder;
var dir_list;
var dir;
var file;
var item;
var filemenu;
var upload_file;
var default_dir;

$( function( ){

        /**
         * init the gui for the file manager
         * on the selected div
         */
        fmgui_init( $( '#file-manager' ) );

        /**
         * list the root of the files dir by default
         */
        show_dir( default_dir );

        /**
         * new folder event
         */
        newfolder.live( 'click', function( ){
                new_folder( );
                return false;
        });

        /**
         * filecrumb event
         */
        filecrumb.live( 'click', function( ){
                show_dir( $( this ).attr( 'href' ) );
                return false;
        });

        /**
         * change directory when clicked
         */
        dir.live( 'click', function( ){
                show_dir( $( this ).attr( 'href' ) );
                return false;
        });

        /**
         * show file contents
         */
        file.live( 'click', function( ){
                show_file( $( this ).attr( 'href' ) );
                return false;
        });

        /**
         * upload file event
         */
        upload_file.live( 'click', function( ){
                upload_file( );
                return false;
        });

        /**
         * item hover event
         */
        $( 'li', dir_list ).live({
                mouseenter : function( ){
                        $( '.delete-img', $( this ) ).addClass( 'delete-small-img' );
                },
                mouseleave : function( ){
                        $( '.delete-img', $( this ) ).removeClass( 'delete-small-img' );
                }
        });

});

/**
 * fmgui_init
 *
 * initialises the gui interface, creates base html
 * and initaialises various vars
 *
 * @param selector element
 * @return void
 */
function fmgui_init( element ){

        // set default directory
        default_dir = '/users/' + window.furasta.user.id + '/';

        // initial html
        element.html( 
                  '<div id="files">'
                + '     <div id="directory-container">'
                + '             <div class="th"><h3 style="color:#fff;margin-top:5px;margin-left:5px">Directory Listing</h3></div>'
                + '             <ul id="panel-top">'
                + '                     <li><a class="link" id="file-upload"><img src="' + window.furasta.site.url + '_inc/img/file.png"/><span>Upload File</span></a></li>'
                + '                     <li><a class="link" id="new-folder"><img src="' + window.furasta.site.url + '_inc/img/folder.png"/><span>New Folder</span></a></li>'
                + '                     <li><a class="link" id="dir-settings"><span id="menu-configuration-img"></span><span>Folder Settings</span></a></li>'
                + '             </ul>'
                + '             <div id="filecrumbs"></div>'
                + '             <ul id="directory-list"></ul> '     
                + '             <br style="clear:both"/>'
                + '     </div>'
                + '</div>'
        );

        // initiate globals
        filecrumbs  = $( '#filecrumbs' );
        filecrumb   = $( '.crumb' );
        newfolder   = $( '#new-folder' );
        dir_list    = $( '#directory-list' );
        dir         = $( '.dir' );
        file        = $( '.file' );
        item        = $( '.item' );
        filemenu    = $( '.menu-left' );
        upload_file = $( '#file-upload' );

}

/**
 * show_dir
 *
 * reads the given path and displays contents
 *
 * @param string path
 * @return void
 */
function show_dir( path ){
        
        show_filecrumbs( path );

        dir_list.html( '<img src="' + window.furasta.site.url + '_inc/img/loading.gif"/>' );

        FileManager.readDir( path,
                function( data ){
                        dir_list.html( '' ); 
                        for( var i in data ){
                                dir_list.append( '<li>' + show_item( data[ i ] ) + '</li>' );
                        }
                }
        );

}

/**
 * show_file
 *
 * displays the content of the file in the appropriate format
 *
 * @param string path
 * @return void
 */
function show_file( path ){
         
        // fetch file contents with ajax
        FileManager.readFile( path, function( content ){

                $( '#dialog' ).html( content );

        });

        // create dialog for resulting request
        $('#dialog')
		.html( '<p>Loading..</p><img src="' + window.furasta.site.url + '_inc/img/loading.gif"/></p>' )
		.attr( 'title', path )
		.dialog( {
			modal   : true,
			buttons : {
                                'Cancel' : function( ){
                                        $( this ).dialog( 'close' );
                                },
                                'Save' : function( ){
                                        $( this ).dialog( 'close' );
                                }   
                        },
			hide    : 'fade',
                        width   : 400,
                        height  : 400,
			show    : 'fade',
			resizeable : false
		})
		.dialog( 'open' );

}

/**
 * show_item
 *
 * shows a given file, switching between different file
 * types - dir, file, image etc
 *
 * @param object data
 * @return string
 */
function show_item( data ){
  
        content = '<a class="delete link" href="' + data.path + '"><span class="delete-img">&nbsp;</span></a>';
        content += '<a class="link ' + data.type + ' item" href="' + data.path + '">';
        content += '<img src="' + window.furasta.site.url;

        switch( data.type ){
                case 'dir':
                        content += '_inc/img/folder.png';
                break;
                case 'file':
                        content += '_inc/img/file.png';
                break;
        }

        content += '"/><br/>' + data.name + '</a>';
        
        return content;

}

/**
 * show_filecrumbs
 *
 * prints the breadcrumbs for the directory
 * that is being viewed, supplied by path
 *
 * @param string path
 * @return void
 */
function show_filecrumbs( path ){

        filecrumbs.attr( 'current', path );

        content = '<a class="link crumb" href="/">files</a>';
        path    = path.split( '/' );
        cur     = '';
        for( i = 0; i < path.length - 1; ++i ){
                cur += path[ i ] + '/';
                content += '<a class="link crumb" href="' + cur + '">' + path[ i ] + '</a> / ';

        }

        filecrumbs.html( content );

}

/**
 * new_folder
 *
 * prompts user for new folder name and
 * sends request for folder to be created
 *
 * @return void
 */
function new_folder( ){

        fPrompt( 'Please enter the name of the folder you would like to create.', function( param, val ){
               dir = filecrumbs.attr( 'current' );
               name = $( 'input[name="prompt-input"]' ).val( );
               FileManager.addDir( dir + name, function(  ){
                        show_dir( dir );              
               });
        }, null );

}
