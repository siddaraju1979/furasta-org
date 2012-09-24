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
var filecrumbs  = $( '#filecrumbs' );
var filecrumb   = $( '.crumb' );
var newfolder   = $( '#new-folder' );
var dir_list    = $( '#directory-list' );
var dir         = $( '.dir' );
var file        = $( '.file' );
var item        = $( '.item' );
var filemenu    = $( '.menu-left' );
var default_dir;

$( function( ){

        default_dir = '/users/' + window.furasta.user.id + '/';

        /**
         * list the root of the files dir by default
         */
        show_dir( default_dir );

        /**
         * new folder event
         */
        newfolder.click( function( ){
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
