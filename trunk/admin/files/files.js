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

$( function( ){

        /**
         * list the root of the files dir by default
         */
        FileManager.show_dir( '/' );

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

});

var FileManager = {

        filecrumbs  : $( '#filecrumbs' ),
        dir_list    : $( '#directory-list' ),
        dir         : $( '.directory' ),
        file        : $( '.file' ),

        /**
         * fm_exec
         *
         * executes a method of the file manager class
         * with the given parameters and callback
         *
         * @param object postdata
         * @param function fn
         * @return void
         */
        fm_exec : function( postdata, fn ){

                $.ajax({
                        type    : 'post',
                        url     : window.furasta.site.url + '_inc/ajax.php?file=_inc/ajax/fm.php',
                        data    : postdata,
                        dataType: 'json',
                        success : fn,
                        error   : function( ){
                                fAlert( 'An unknown error occurred. Please try again.' );
                        }

                });

        },

        /**
         * show_dir
         *
         * reads the given path and displays contents
         *
         * @param string path
         * @return void
         */
        show_dir : function( path ){
                
                filecrumbs.html( path.split( '/' ).join( ' / ' ) );
                dir_list.html( '<img src="' + window.furasta.site.url + '_inc/img/loading.gif"/>' );

                fm_exec( {
                        func : 'readDir',
                        'path' : path
                }, function( data ){
                        dir_list.html( '' ); 
                        for( var i in data ){
                                dir_list.append( '<li><a class="link" href="' + i + '"><img src="' + window.furasta.site.url + '_inc/img/folder.png"/><br/>' + i + '</a></li>' );
                        }

                });

        }

}
