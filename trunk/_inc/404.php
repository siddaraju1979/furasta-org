<?php

/**
 * Smarty Configuration File, Furasta.Org
 *
 * Loads the Smarty Template Engine and asigns
 * values to all of the template variables.
 * Assigns functions, then plugin functions and
 * displays the correct template file.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 */

header('Content-Type: text/html; charset=UTF-8');

$Plugins->hook( 'frontend', 'on_load' );

$smarty_dir=HOME.'_inc/Smarty/';

require $smarty_dir.'libs/Smarty.class.php';
require $function_dir.'frontend.php';

$smarty=new Smarty();

$smarty->template_dir=$smarty_dir.'templates';
$smarty->compile_dir=$smarty_dir.'templates_c';

$plugin_functions=$Plugins->frontendTemplateFunctions();
foreach($plugin_functions as $function)
	$smarty->register_function($function->frontendTemplateFunction,array($function,'frontendTemplateFunction'));

$smarty->register_function( 'metadata', 'frontend_metadata' );
$smarty->register_function( 'page_content', 'frontend_page_content' );
$smarty->register_function('menu','frontend_menu');

$content = '
<h2>404 - Page Not Found</h2>
<p>The requested URL was not found.</p>
<p>The page could have been removed or the URL may be incorrect.</p>
<p><a href="' . SITEURL . '">Return to site index</a></p>
';

$smarty->assign('__page_content',$content);
$smarty->assign('page_name', '404 - Page Not Found');
$smarty->assign('page_id','0');
$smarty->assign('page_slug','');
$smarty->assign('page_edited','');
$smarty->assign('page_user','');
$smarty->assign('page_parent_id','');
$smarty->assign('page_type', 'Normal' );

$smarty->assign( 'siteurl', SITEURL );

$time=microtime(true)-START_TIME;
$smarty->assign('page_load_time',$time);

$smarty->assign( 'home', HOME );
$smarty->assign('site_title',$SETTINGS['site_title']);
$smarty->assign('site_subtitle',$SETTINGS['site_subtitle']);

if(!file_exists(TEMPLATE_DIR.'index.html'))
	error( 'Template files could not be found.', 'Template Error' );

if(file_exists(TEMPLATE_DIR.'404.html'))
	$smarty->display(TEMPLATE_DIR.'404.html');
else
	$smarty->display(TEMPLATE_DIR.'index.html');

exit;
?>
