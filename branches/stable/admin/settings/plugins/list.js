/**
 * List Plugin Javascript, Furasta.Org
 *
 * javascript for the list plugin page
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_settings
 */

$(document).ready(function(){

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

		$("#users input[name=trash-box]:checked").each(function() {

			boxes.push($(this).val());

		});

		var boxes=boxes.join(",");

		if(boxes=="")

			return false;

		fConfirm("Are you sure you want to perform a multiple "+action+"?",function(){ window.location="settings.php?page=plugins&action=multiple&act="+action+"&boxes="+boxes; });

	});

});
