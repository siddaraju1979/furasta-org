<?php

/**
 * Language Page - English, Furasta.Org
 *
 * This file contains an array of errors and content
 * in the language stated above.
 *
 * @author     Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @license    http://furasta.org/licence.txt The BSD License
 * @version    1.0
 * @package    admin_lang
 */

$lang_errors = array( 
	'1' => 'Page Updated',
	'2' => 'Page Deleted',
	'3' => 'Page Created',
	'4' => 'A page named "%1" already exists or is reserved for the system.',
	'5' => 'The %1 is a required field, please do not leave it blank.', 
	'6' => 'Please enter a valid email in the %1 field.', 
	'7' => 'The %1 field is not valid.',
	'8' => 'The %1 field must be at least %2 characters long.',
	'9' => 'The %1 must match the %2 field.', 
	'10'=> 'Please enter a valid URL in the %1 field.',
	'11'=> 'User not activated.',
	'12'=> 'Login details are incorrect',
	'13'=> 'Details Updated',
	'14'=> 'A new version of Furasta.Org is available. <a href="%2">Click here to download %1</a>',
	'15'=> 'Pages Trashed',
	'16'=> 'Pages Restored',
	'17'=> 'Pages Deleted',
	'18'=> 'The %1 field must be at most %2 characters long'
);

$lang = array( 

	// general terms
	'name' => 'Name',
	'author' => 'Author',
	'type' => 'Type',
	'edited' => 'Edited',
	'new' => 'New',
	'pages' => 'Pages',
	'trash' => 'Trash',
	'groups' => 'Groups',
	'users' => 'Users',
	'template' => 'Template',
	'version' => 'Version',
	'forum' => 'Forum',
	'bug_tracker' => 'Bug Tracker',
	'group' => 'Group',
	'parent' => 'Parent',
	'permissions' => 'Permissions',
	'loading' => 'Loading',
	'title' => 'Title',
	'save' => 'Save',
	'delete' => 'Delete',
	'restore' => 'Restore',

	// menu items
	'menu_overview' => 'Overview',
	'menu_pages' => 'Pages',
	'menu_edit_pages' => 'Edit Pages',
	'menu_new_page' => 'New Page',
	'menu_trash' => 'Trash',
	'menu_users_groups' => 'Users & Groups',
	'menu_edit_users' => 'Edit Users',
	'menu_edit_groups' => 'Edit Groups',
	'menu_settings' => 'Settings',
	'menu_configuration' => 'Configuration',
	'menu_template' => 'Template',
	'menu_plugins' => 'Plugins',
	'menu_update' => 'Update',
	
	// Overview Page
	'overview_recently_trashed' => 'Recently Trashed',
	'overview_recently_edited' => 'Recently Edited',
	'overview_furasta_development_blog' => 'Furasta.Org Development Blog',
	'overview_website_overview' => 'Website Overview',

	// Pages
	'pages_list_go_button' => 'Go',
	'pages_list_expand_collapse_all' => 'Expand / Collapse all',
	'pages_edit_show_options' => 'Show Options',
	'pages_edit_hide_options' => 'Hide Options',
	'pages_edit_is_home' => 'Is Home Page',
	'pages_edit_hide_navigation' => 'Hide in Navigation',

	// Trash
	'trash_empty' => 'No Trash!',
	'trash_go_button' => 'Go',	

	// Users & Groups
	'Email' => 'Email',
	'Delete' => 'Delete',
	'New User' => 'New Users',
	'Homepage' => 'Homepage',
	'Change Password' => 'Change Password',
	'To reset the password an email will be sent to %1 with reset details. Press \'Yes\' to confirm.' =>
		'To reset the password an email will be sent to %1 with reset details. Press \'Yes\' to confirm.',
	
	// Configuration
	'configuration_website_options' => 'Website Options',
	'configuration_website_title' => 'Title',
	'configuration_website_subtitle' => 'Subtitle',
	'configuration_website_url' => 'URL',
	'configuration_maintenance_mode' => 'Maintenance Mode',
	'configuration_dont_index' => 'Don\'t Index Website',
	'configuration_diagnostic_mode' => 'Diagnostic Mode',
	'configuration_language' => 'Language',
	

	// Prompts
	'prompt_yes' => 'Yes',
	'prompt_no' => 'No',
	'prompt_cancel' => 'Cancel',
	'prompt_confirm' => 'Are you sure?',

	// Help prompts
	'help_configuration_index' => 'Ticking this box will force search engines such as Google, Yahoo etc not to index this website. This option should be enabled to make the website more private, though people will still be able to access it through direct URLs.',
	'help_configuration_maintenance' => 'Ticking this box will enable Maintenance Mode; a feature which should be enabled while making sitewide changes. For example, if you are performing a re-structure of your website and you enable Maintenance Mode people who view the website will see a maintenance notice and will not be able to access any content.',
	'help_configuration_diagnostic' => '<b>Note: For developers only</b><br/>The CMS carrys out all kinds of caching to make page loads quicker, including that of CSS and JavaScript. If you want to temporarily disable this feature for development reasons then you can enable Diagnostic Mode. During this time there will be no caching of CSS or JavaScript, so it is recommended to enable it again when finished testing',
	'help_configuration_url' => 'The default URL for the website. All links will use this URL. Some may want to add remove the \"www.\"',
);

?>
