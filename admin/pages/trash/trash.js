/**
 * Trash Javascript, Furasta.Org
 *
 * Javascript for trash page.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_pages
 */

$(document).ready(function(){

        $(".delete").live( "click", function(){

                fConfirm(trans( 'prompt_confirm_delete' ),function(element){

                        element.parent().parent().fadeOut( function( ){

				$( this ).remove( );

				rowColor( );

			});

                        fetch(window.furasta.site.url + "/ajax.php?file=admin/pages/trash/delete.php&id="+element.attr("id"));

                },$(this));

        });

        $(".restore").live( "click", function(){

                fConfirm( trans( 'trash_confirm_restore' ),function(element){

                        element.parent().parent().fadeOut( function( ){

				$( this ).remove( );

				rowColor( );

			});

                        fetch(window.furasta.site.url + "/ajax.php?file=admin/pages/trash/restore.php&id="+element.attr("id"));

                },$(this));

        });

	$(".checkbox-all").click(function(){

		if($(".checkbox-all").attr("all")=="checked"){

			$("input[type=checkbox]").attr("checked","");

                        $(".checkbox-all").attr("all","");

		}

		else{

	                $("input[type=checkbox]").attr("checked","checked");

			$(".checkbox-all").attr("all","checked");

		}	

	 });

        $(".p-submit").click(function(){

                var action=$(".select-"+$(this).attr("id")).val();

                if(action=="---")

                        return false;

                var boxes=[];

                $("#trash input[name=trash-box]:checked").each(function() {

                        boxes.push($(this).val());

                });

                var boxes=boxes.join(",");

                if(boxes=="")

                        return false;

                fConfirm(trans( 'prompt_confirm_multiple' ),function(){ window.location="pages.php?page=trash&action=multiple&act="+action+"&boxes="+boxes; });

        });

});
