/**
 * Edit Pages Javascript, Furasta.Org
 *
 * javascript for the edit pages page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

$(document).ready(function(){

	/**
	 * load page type
	 */
	var type=$("#pages-type-content").attr("type");

        var id=$("#pages-type-content").attr("page-id");

        $("#pages-type-content").html( trans( 'loading' ) + "... <img src=\"/_inc/img/loading.gif\"/>");

	loadPageType(type,id);

        /**
         * initial url load, also loads url from querystring parent 
         */
	var pagename = $( "#page-name" ).attr( "value" );

        getUrl( pagename );

        /**
         * display options when clicked 
         */
        $("#options-link").click(displayOptions);

        /**
         * reload page type when select box is changed 
         */
        $("#edit-type").change(function(){

                $("#pages-type-content").html( trans( 'loading' ) + "... <img src=\"/_inc/img/loading.gif\"/>");

                var type=$(this).attr("value");

		loadPageType(type,id);

        });

        /**
         * set up page url 
         */
        $( "#select-parent" ).change( function(){

                var pagename = $( "#page-name" ).attr( "value" );

		getUrl( pagename );

        });

	/**
	 * re-set up page url when typing occurs
	 */
	$("#page-name").keyup(function(){

		var pagename = slugCheck( $("#page-name").val() );

		if( pagename == false )

			$("#page-name").addClass("error");

		else

			getUrl( pagename );
		
	});

        /**
         * function to run when page-permissions-dialog
         * is saved 
         */
        var saveFunction = function( ){
                var see = $( "input[name=\'who-can-see\']:checked" ).val( );
                var edit = $( "input[name=\'who-can-edit\']:checked" ).val( );

                var see_users = [];
                var edit_users = [];
                var see_groups = [];
                var edit_groups = [];
                var see_perm = "";
                var edit_perm = "";

                if( see == "selected" ){

                        var n = 0;

                        $( "input[name=\'see-users\']:checked" ).each( function( ){

                                if( !$( this ).attr( "disabled" ) ){
                                        see_users[ n ] = $( this ).val( );
                                        n++;
                                }


                        });

                        $( "input[name=\'see-groups\']:checked" ).each( function( i ){

                                see_groups[ i ] = $( this ).val( );

                        });

                        if( see_groups.length == 0 )
                                see_perm = see_users.join( "," );
                        else
                                see_perm = see_users.join( "," ) + "#" + see_groups.join( "," );


                }

                if( edit == "selected" ){

                        var n = 0;

                        $( "input[name=\'edit-users\']:checked" ).each( function( ){

                                if( !$( this ).attr( "disabled" ) ){
                                        edit_users[ n ] = $( this ).val( );
                                        n++;
                                }

                        });

                        $( "input[name=\'edit-groups\']:checked" ).each( function( i ){

                                edit_groups[ i ] = $( this ).val( );

                        });

                        if( edit_groups.length == 0 )
                                edit_perm = edit_users.join( "," );
                        else
                                edit_perm = edit_users.join( "," ) + "#" + edit_groups.join( "," );

                }

	

		var users = see_perm + "|" + edit_perm;

                $( "input[name=\'perm\']" ).attr( "value", users );

                $( this ).dialog( "close" );

        };

	$( "#page-permissions" ).click( function( ){

                /**
                 * load content if not already loaded
                 */
                if( !$( "#page-permissions-content" ).hasClass( "loaded" ) ){

		        var hash=Math.floor(Math.random()*1001);

                        $( "#page-permissions-content" ).load(
				window.furasta.site.url + "_inc/ajax.php?file=admin/pages/permissions.php&id=" + id + '&hash=' + hash, function( ){

                                	$( this ).addClass( "loaded" );

                        });

                }

                $( "#page-permissions-dialog" ).dialog({

                        modal:  true,

                        width:  "55%",

                        buttons: {
        
                                "Close": function( ) { $( this ).dialog( "close" ); },

                                "Save": saveFunction,

                        },

                        hide:   "fade",

                        show:   "fade",

                        resizeable:     false

                });

                $( "#page-permissions-dialog" ).dialog( "open" );

	});

});
