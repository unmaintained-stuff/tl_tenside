<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005-2010
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Repository
 * @license    LGPL
 * @filesource
 */

// we need the repos settings.
require_once(TL_ROOT . '/system/modules/rep_base/RepositorySettings.php');

/**
 * Class Tenside
 *
 * TYPOlight Repository :: nusoap based Base backend module
 * @copyright	Copyright (C) 2008 by Peter Koch, IBK Software AG, CyberSpectrum 2009
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Controller
 *
 */
class Tenside extends RepositoryBackendModule
{
	/**
	 * Generate module:
	 * - Display a wildcard in the back end
	 * - Select the template and compiler in the front end
	 */
	public function generate()
	{
		$this->rep = new stdClass();
		$rep = &$this->rep;
		$rep->username	= $this->BackendUser->username;
		$rep->isadmin	= $this->BackendUser->isAdmin;
		$this->strTemplate = $this->actions[0][1];
		$this->compiler	= $this->actions[0][2];
		foreach ($this->actions as &$act) {
			if ($act[0]!='') {
				$this->parameter = $this->Input->get($act[0]);
				if ($this->parameter!='') {
					$this->action = $act[0];
					$this->strTemplate = $act[1];
					$this->compiler = $act[2];
					break;
				} // if
			} // if
		} // foreach
		return str_replace(array('{{', '}}'), array('[{]', '[}]'), BackendModule::generate());
	} // generate

	/**
	 * Compile module: common initializations and forwarding to distinct function compiler
	 */
	protected function compile()
	{
		// hide module?
		$compiler = $this->compiler;
		if ($compiler=='hide') return;

		// load other helpers
		$this->tl_root = str_replace("\\",'/',TL_ROOT).'/';
		$this->tl_files = str_replace("\\",'/',$GLOBALS['TL_CONFIG']['uploadPath']).'/';
		$this->loadLanguageFile('tl_repository');
		$this->loadLanguageFile('languages');
		$this->Template->rep = $this->rep;
		$this->languages = rtrim($GLOBALS['TL_LANGUAGE'].','.trim($GLOBALS['TL_CONFIG']['repository_languages']),',');
		$this->languages = implode(',',array_unique(explode(',',$this->languages)));

		// complete rep initialization
		$rep = $this->rep;
		$rep->f_link	= $this->createUrl(array($this->action=>$this->parameter));
		$rep->f_action	= $this->compiler;
		$rep->f_mode	= $this->action;
		$rep->theme		= new RepositoryBackendTheme();
		$rep->backLink	= $this->getReferer(ENCODE_AMPERSANDS);
		$rep->homeLink	= $this->createUrl();

		// load soap client in case wsdl file is defined
		$wsdl = trim($GLOBALS['TL_CONFIG']['repository_wsdl']);
		if ($wsdl != '') {
			// decide which library to use, force nusoap if soap module not loaded, let user decide otherwise.
			if((!extension_loaded('soap')) || ($GLOBALS['TL_CONFIG']['repository_force_nusoap'] == true))
			{
				require_once(TL_ROOT . '/system/modules/tensiderepository/SoapClient.php');
				$soapclass = 'NuSoapClient';
			} else {
				// we want to use the soap module by PHP.
				if (!REPOSITORY_SOAPCACHE)
					ini_set('soap.wsdl_cache_enabled', 0);
				$soapclass = 'SoapClient';
			}
			
			// Default client params
			$params=array(
					'soap_version' => SOAP_1_2,
					'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 1
				);
			
			// HOOK: proxy module
			if ($GLOBALS['TL_CONFIG']['useProxy']) {
				$proxy_uri = parse_url($GLOBALS['TL_CONFIG']['proxy_url']);
				$params['proxy_host'] = $proxy_uri['host'];
				$params['proxy_port'] = $proxy_uri['port'];
				$params['proxy_login'] = $proxy_uri['user'];
				$params['proxy_password'] = $proxy_uri['pass'];
			}
			try {
				$this->client = new $soapclass($wsdl, $params);
			} catch (Exception $e) {
				$this->strTemplate = 'tensiderep_error';
				$this->Template = new FrontendTemplate($this->strTemplate);
				$this->Template->rep = $this->rep;
				$this->Template->error = 'FATAL ERROR IN SOAP STARTUP: ' . $e->getMessage();
				return;
			}
			$this->mode = 'soap';
		} else
			// fallback to load RepositoryServer class if on central server
			if (file_exists($this->tl_root . 'system/modules/rep_server/RepositoryServer.php')) {
				$this->import('RepositoryServer');
				$this->RepositoryServer->enableLocal();
				$this->mode = 'local';
			} // if

		try {
			// execute compiler
			$this->$compiler($this->parameter);
		} catch (SoapFault $e) {
			$this->strTemplate = 'tensiderep_error';
			$this->Template = new FrontendTemplate($this->strTemplate);
			$this->Template->rep = $this->rep;
			$this->Template->error = sprintf('<strong>Error: %s</strong><br /> <pre>%s</pre>', $e->getMessage(), $e->getTraceAsString());
		}
	} // compile
} // class Tenside

?>