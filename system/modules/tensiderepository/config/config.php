<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 * 
 * PHP version 5
 * @copyright	Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Tenside
 * @license		LGPL 
 * @filesource
 */

/**
 * Back-end modules - we override the repository as it is useless without soap.
 */
$GLOBALS['BE_MOD']['system']['repository_catalog'] = array(
		'callback'		=> 'TensideCatalog',
		'icon'			=>	RepositoryBackendTheme::image('catalog16'),
		'stylesheet'	=>	RepositoryBackendTheme::file('backend.css')
	);
$GLOBALS['BE_MOD']['system']['repository_manager'] = array(
		'callback'		=> 'TensideManager',
		'icon'			=>	RepositoryBackendTheme::image('install16'),
		'stylesheet'	=>	RepositoryBackendTheme::file('backend.css')
	);

// we have no other option if the extension is not loaded.
if(!extension_loaded('soap'))
	$GLOBALS['TL_CONFIG']['repository_force_nusoap'] = true;

?>