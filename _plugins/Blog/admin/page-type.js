/**
 * Blog Plugin, Furasta.Org
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @licence	http://furasta.org/licence.txt The BSD Licence
 * @version	1
 */

$(function(){
	$( '#tabs' ).tabs( );
	$( '#help-blog-title' ).click( function( ){ fHelp( trans( 'blog_title_help' ) ); });
});
