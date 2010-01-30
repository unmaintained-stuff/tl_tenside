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
 * Class SoapFault
 *
 * @copyright	CyberSpectrum 2009
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Controller
 *
 */

// require nusoap.
require_once(TL_ROOT . '/plugins/nusoap/nusoap.php');

// provide wrapper object to "simulate" soap fault
if(!class_exists('SoapFault'))
{
	class SoapFault extends Exception {
		public function __construct($faultcode, $faultstring/*, $faultactor, $detail, $faultname, $headerfault */)
		{
			parent::__construct($faultstring);
		}
	}
}

?>