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


/**
 * Class TensideCatalog
 *
 * Contao Repository :: nusoap based Base backend module
 * @copyright	Peter Koch 2008-2010, CyberSpectrum 2009-2010
 * @author		Peter Koch IBK Software AG, Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Controller
 *
 */
class TensideCatalog extends Tenside
{
	/**
	 * Generate module:
	 * - Display a wildcard in the back end
	 * - Declare actionlist with templates and compilers in the front end
	 */
	public function generate()
	{
		$this->actions = array(
			//	  act[0]			strTemplate					compiler
			array('',				'tensiderep_catlist',		'listExtensions' ),
			array('view',			'repository_catview',		'viewExtension' )
		);
		return Tenside::generate();
	} // generate
	
	/**
	 * List the extensions
	 */
	protected function listExtensions()
	{
		$rep = &$this->Template->rep;
		// returning from submit?
		if ($this->filterPost('repository_action') == $rep->f_action) {
			// get url parameters
			$tmptag=trim($this->Input->post('repository_tag'));
			$tmpwildcard=trim($this->Input->post('repository_wildcardsearch'));
			$stg = $this->Session->get('repository_catalog_settings');
			if (is_array($stg)) {
				$rep->f_wildcardsearch	= trim($stg['repository_wildcardsearch']);
				$rep->f_tag 			= trim($stg['repository_tag']);
			}
			if($tmptag && $tmptag != $rep->f_tag)
			{
				$rep->f_tag 	= trim($this->Input->post('repository_tag'));
				unset($rep->f_wildcardsearch);
			} else if($tmpwildcard && $tmpwildcard != $rep->f_wildcardsearch)
			{
				$rep->f_wildcardsearch	= trim($this->Input->post('repository_wildcardsearch'));
				unset($rep->f_tag);
			} else
			{
				if($tmptag == NULL && $tmpwildcard == NULL)
				{
					unset($rep->f_wildcardsearch);
					unset($rep->f_tag);
				}
			}
			$rep->f_page	= trim($this->Input->post('repository_page'));
			$rep->f_type 	= trim($this->Input->post('repository_type'));
			$rep->f_category= trim($this->Input->post('repository_category'));
			$rep->f_state	= trim($this->Input->post('repository_state'));
			$rep->f_author	= trim($this->Input->post('repository_author'));
			$rep->f_order	= trim($this->Input->post('repository_order'));
			$rep->f_page	= trim($this->Input->post('repository_page'));
			$rep->f_find	= trim($this->Input->post('repository_find'));
			$this->Session->set(
				'repository_catalog_settings',
				array(
					'repository_tag'	=> $rep->f_tag,
					'repository_type'	=> $rep->f_type,
					'repository_category'=> $rep->f_category,
					'repository_state'	=> $rep->f_state,
					'repository_author'	=> $rep->f_author,
					'repository_order'	=> $rep->f_order,
					'repository_page'	=> $rep->f_page,
					'repository_find'	=> $rep->f_find,
					'repository_wildcardsearch'		=> $rep->f_wildcardsearch,
				)
			);
		} else {
			$stg = $this->Session->get('repository_catalog_settings');
			if (is_array($stg)) {
				$rep->f_tag 	= trim($stg['repository_tag']);
				$rep->f_type 	= trim($stg['repository_type']);
				$rep->f_category= trim($stg['repository_category']);
				$rep->f_state	= trim($stg['repository_state']);
				$rep->f_author	= trim($stg['repository_author']);
				$rep->f_order	= trim($stg['repository_order']);
				$rep->f_page	= trim($stg['repository_page']);
				$rep->f_find	= trim($stg['repository_find']);
				$rep->f_wildcardsearch	= trim($stg['repository_wildcardsearch']);
			} // if
		} // if	
		
		if ($rep->f_order=='') $rep->f_order = 'popular';
		
		if ($rep->f_page < 1) $rep->f_page = 1;
		$perpage = (int)trim($GLOBALS['TL_CONFIG']['repository_listsize']);
		if ($perpage < 1) $perpage = 10;
		
		// process parameters and build query options
		$options = array(
			'languages'	=> $this->languages,
			'sets'		=> 'sums,reviews',
			'first'		=> ($rep->f_page-1) * $perpage,
			'limit'		=> $perpage
		);
		if ($rep->f_tag		!= '') $options['tags']			= $rep->f_tag;
		if ($rep->f_type 	!= '') $options['types']		= $rep->f_type;
		if ($rep->f_category!= '') $options['categories'] 	= $rep->f_category;
		if ($rep->f_state	!= '') $options['states']		= $rep->f_state; 
		if ($rep->f_author	!= '') $options['authors']		= $rep->f_author;
		if ($rep->f_find	!= '') $options['find']			= $rep->f_find;
		if ($rep->f_wildcardsearch != '')
		{
			unset($options[match]);// in future(ER 2.0) we want something like: $options[match]				= 'fuzzy';
			$options[tags]				= $rep->f_wildcardsearch;
			unset($rep->f_tag);
		}
		switch ($rep->f_order) {
			case 'name'		: break;
			case 'title'	: $options['order'] = 'title'; break;
			case 'author'	: $options['order'] = 'author'; break;
			case 'rating'	: $options['order'] = 'rating-'; break;
			case 'reldate'	: $options['order'] = 'releasedate-'; break;
			default			: $options['order'] = 'popularity-';
		} // switch
		
		// query extensions
		$rep->extensions = $this->getExtensionList($options);
		if ($rep->f_page>1 && count($rep->extensions)==0) {
			$rep->f_page = 1;
			$options['first'] = 0;
			$rep->extensions = $this->getExtensionList($options);
		} // if

		// add view links
		$totrecs = 0;
		// Contao compatibility check
		$tlversion = Repository::encodeVersion(VERSION.'.'.BUILD);
		foreach ($rep->extensions as &$ext) {
			$ext->viewLink = $this->createUrl(array('view' => $ext->name.'.'.$ext->version.'.'.$ext->language));
			$totrecs = $ext->totrecs;
			$displayversion = sprintf('%s - %s', Repository::formatCoreVersion($ext->coreminversion), Repository::formatCoreVersion($ext->coremaxversion));
			if (($ext->coreminversion>0 && $tlversion<$ext->coreminversion) ||
				($ext->coremaxversion>0 && $tlversion>$ext->coremaxversion) )
			{	// less than current Core version
				$ext->status = (object)array(
					'color'	=> 'darkorange', 
					'text'	=> 'notapproved', 
					'par1'	=> 'Contao',
					'par2'	=> Repository::formatCoreVersion($tlversion)
				);
				$ext->validfor = (object)array(
					'color'	=> 'red', 
					'version' => $displayversion);
			} else if($ext->coremaxversion>0 && $tlversion<$ext->coremaxversion)
			{	// greater than current Core version
				$ext->validfor = (object)array(
					'color'	=> 'blue', 
					'version' => $displayversion);
			} else	// equal to current Core version
				$ext->validfor = (object)array(
					'color'	=> 'green', 
					'version' => $displayversion);
		} // foreach
		
		$rep->pages = ($totrecs > 0) ? floor(($totrecs+$perpage-1) / $perpage) : 1;	
		$rep->tags = $this->getTagList(array('languages'=>$this->languages, 'mode'=>'initcap'));
		$rep->authors = $this->getAuthorList(array('languages'=>$this->languages));
	} // listExtensions
	
	/**
	 * Detailed view of one extension.
	 */
	protected function viewExtension($aParams)
	{
		$rep = &$this->Template->rep;
		
		// parse name[.version][.language]
		$matches = array();
		if (!preg_match('#^([a-zA-Z0-9_-]+)(\.([0-9]+))?(\.([a-z]{2,2}))?$#', $aParams, $matches)) 
			$this->redirect($rep->homeLink);
		$name = $matches[1];
		$version = (count($matches)>=4) ? $matches[3] : '';
		$language = count($matches)>=6 ? $matches[5] : $this->languages;
		
		// compose base options
		$options = array(
			'match' 	=> 'exact',
			'names' 	=> $name,
			'languages'	=> $language,
			'sets'  	=> 'details,pictures,languages,history,dependencies,dependents,sums'
		);
		if ($version!='') $options['versions'] = $version;
		
		$rep->extensions = $this->getExtensionList($options);
		if (count($rep->extensions)<1) $this->redirect($rep->homeLink);
		$ext = &$rep->extensions[0];
		
		// other versions links
		if (property_exists($ext, 'allversions'))
			foreach ($ext->allversions as &$ver)
				$ver->viewLink = $this->createUrl(array('view'=>$ext->name.'.'.$ver->version.'.'.$ext->language));
			
		// other languages links
		if (property_exists($ext, 'languages')) {
			$langs = explode(',', $ext->languages);
			$ext->languages = array();
			foreach ($langs as $lang) {
				$l = new stdClass();
				$l->language = $lang;
				$l->link = $this->createUrl(array('view' => $ext->name.'.'.$ext->version.'.'.$lang));
				$ext->languages[] = $l;
			} // for
		} // if
		
		// dependencies links
		if (property_exists($ext, 'dependencies'))
			foreach ($ext->dependencies as &$dep)
				$dep->viewLink = $this->createUrl(array('view'=>$dep->extension));
			
		// dependents links
		if (property_exists($ext, 'dependents'))
			foreach ($ext->dependents as &$dep)
				$dep->viewLink = $this->createUrl(array('view'=>$dep->extension));
		
		// install link
		$ext->installLink = $this->createPageUrl('repository_manager',array('install'=>$ext->name.'.'.$ext->version));
		
		if ($this->filterPost('repository_action') == $rep->f_action) {
			if (isset($_POST['repository_installbutton'])) $this->redirect($ext->installLink);
			if (isset($_POST['repository_manualbutton']) && property_exists($ext, 'manual')) $this->redirect($ext->manual);
			if (isset($_POST['repository_forumbutton']) && property_exists($ext, 'forum')) $this->redirect($ext->forum);
			if (isset($_POST['repository_shopbutton']) && property_exists($ext, 'shop')) $this->redirect($ext->shop);
		} // if
	} // viewExtension
	
	private function getAuthorList($aOptions)
	{
		switch ($this->mode) {
			case 'local':
				return $this->RepositoryServer->getAuthorList((object)$aOptions);
			case 'soap':
				return $this->client->getAuthorList($aOptions);
			default:
				return array();
		} // if
	} // getAuthorList
	
	private function getTagList($aOptions)
	{
		switch ($this->mode) {
			case 'local':
				return $this->RepositoryServer->getTagList((object)$aOptions);
			case 'soap':
				return $this->client->getTagList($aOptions);
			default:
				return array();
		} // if
	} // getTagList
	
} // class TensideCatalog
 
?>