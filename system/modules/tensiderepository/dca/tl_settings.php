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
 * Add palettes to tl_settings
 */

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('repository_listsize', 'repository_listsize,repository_force_nusoap', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

/**
 * Add fields to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['repository_force_nusoap'] = array
	(
		'label'						=> &$GLOBALS['TL_LANG']['tl_settings']['repository_force_nusoap'],
		'exclude'					=> true,
		'inputType'					=> 'checkbox',
		'default'					=> false,
		'eval'						=> array('nospace'=>true, 'tl_class'=>'w50')
);

// we have no other option if the extension is not loaded.
if(!extension_loaded('soap'))
	$GLOBALS['TL_DCA']['tl_settings']['fields']['repository_force_nusoap']['eval']['disabled'] = true;

?>