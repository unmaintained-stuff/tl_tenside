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
 * Class SoapClient
 *
 * @copyright	CyberSpectrum 2009
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Controller
 *
 */

// require nusoap.
require_once(TL_ROOT . '/plugins/nusoap/nusoap.php');

/**
 * Class NuSoapClient
 *
 * @copyright	CyberSpectrum 2009
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Controller
 *
 */
class NuSoapClient
{
	protected $client=NULL;

	public function __construct($wsdl, $params)
	{
		$this->client = new nusoap_client($wsdl, 'wsdl', $params['proxy_host'], $params['proxy_port'], $params['proxy_login'], $params['proxy_password']);
		//'soap_version' => SOAP_1_2,
		$this->client->setHTTPEncoding('gzip');
		$this->client->soap_defencoding = 'UTF-8';
		$this->client->decode_utf8 = false;
		$this->client->setDebugLevel(9);
	}
	
	protected function ConvertToObj($arr=array())
	{
		if(is_array($arr))
		{
			$keyList = array_keys($arr);
			$struct=false;
			foreach ($keyList as $keyListValue) {
				if (!is_int($keyListValue))	$struct=true;
				if(is_array($arr[$keyListValue])) $arr[$keyListValue]=$this->ConvertToObj($arr[$keyListValue]);
			}
			return $struct ? (object)$arr : $arr;
		}
		return $arr;
	}
	
	public function __call($method, $args)
	{
		$result=$this->client->call($method, $args);
		$errMsg=$this->client->getError();
		if($errMsg !== false)
		{
			throw new SoapFault('SoapFault', $errMsg);
		}
		$ret=$this->ConvertToObj($result);
		return $ret;
	}
};

// provide wrapper object to "simulate" soap extension
if(!class_exists('SoapClient'))
{
	class SoapClient extends NuSoapClient {}
}

// provide wrapper object to "simulate" soap extension
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