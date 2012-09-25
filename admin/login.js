/**
 * Login Javascript, Furasta.Org
 *
 * javascript for the login page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

$(document).ready(function(){

	$( "#password-reminder" ).click( function( ){

		var saveFunction = function( ){

                        var email = $( "input[ name=\'Email-Reminder\' ]" ).attr( "value" );

                        var filter=/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

                        if( filter.test( email ) == false ){

                                $( "#reminder-information" ).html( "Please enter a valid email address" );

                                return false;

                        }

                        $( "#reminder-information" ).html( "<img src=\'' . SITEURL . '_inc/img/loading.gif\' /> Loading.." );

                        $.ajax({

                                url     :       window.furasta.site.url + "ajax.php?file=admin/users/reminder.php&no_config&email=" + email,

                                timeout :       5000,

                                success :       function( html ){

                                                        if( html == 1 )

                                                                $( "#reminder-information" ).html( "The email address you provided does not correspond to a user account." );

                                                        else
                                                                $( "#reminder-information" ).html( "An email has been sent to you containing password reset details." );
                                },

                                error   :       function( ){

                                                $( "#reminder-information" ).html( "There has been an error processing your request. Please try again." );

                                }

                        });

		};

                $( "#password-reminder-dialog" ).dialog({

                        modal:  true,

                        width:  "400px",

                        buttons: {
        
                                "Close": function( ) { $( this ).dialog( "close" ); },

                                "Send": saveFunction,

                        },

                        hide:   "fade",

                        show:   "fade",

                        resizeable:     false

                });

                $( "#password-reminder-dialog" ).dialog( "open" );


	} );

});
