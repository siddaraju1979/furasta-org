<?php

class rssPages{
        var $name='RSS Pages';
	var $description='Display RSS Feeds on pages via page types..';
	var $version=1;
	var $frontendTemplateFunction='rss_pages';
	var $adminPageType='RSS Feed';
        var $email='conormacaoidh@gmail.com';
        var $href='http://blog.macaoidh.name';

	function adminPageType($page_id){
		// create rss_feed($url) function, which returns array should be cached also, within function!

		return 'test';
	}

	function frontendTemplateFunction($Page){
		return 'RSS page content..';
	}
}

$Plugins->register(new rssPages);
?>
