<?php
/**
 * @package		Asikart Joomla! Extansion Example
 * @subpackage	mod_example
 * @copyright	Copyright (C) 2012 Asikart.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

abstract class modPhotonewsHelper
{
	public static function getList(&$params)
	{
		if(self::includeWindWalker()){
			echo 'Please Install WindWalker Framework';
			return false;
		}
		
		if(!function_exists('file_get_html')){
			include_once AKPATH_HTML.'/simple_html_dom.php' ;
		}
		
		$list 	= null ;
		$db 	= JFactory::getDbo();
		$q 		= $db->getQuery(true) ;
		
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		
		$where = AKHelper::_('query.PublishingItems', 'a.', 'state') ;
		$cats = implode(', ', $params->get('catid'));
		
		// Ordering
		$ordering = $params->get('ordering') ;
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created DESC',
			'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created DESC',
			'p_dsc' => 'a.publish_up DESC',
			'r'		=> 'RAND()'
		);
		
		$q->select('a.*')
			->from('#__content AS a')
			->where($where)
			->where(" a.catid IN ({$cats}) ")
			->group('a.id')
			->order($order_map[$ordering])
			;
		
		$db->setQuery($q, 0, $params->get('count'));
		$list = $db->loadObjectList();
		
		foreach ($list as &$item) {
			
			// Get Link
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				$item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			} else {
				$item->readmore_link = JRoute::_('index.php?option=com_users&view=login');
			}
			
			
			// Get Image
			$item->images = new JRegistry($item->images) ;
			
			if( $item->images->get('image_intro') ) {
				
				$item->image = $item->images->get('image_intro') ;
				
			}elseif( $item->images->get('image_fulltext') ) {
				
				$item->image = $item->images->get('image_fulltext') ;
				
			}else{
				
				$html = str_get_html($item->introtext.$item->fulltext);
				$imgs = $html->find('img') ;
				
				$item->image = '' ;
				if( !empty($imgs[0]) ) {
					$item->image = $imgs[0]->src ;
				}
			}
			
			$item->image = str_replace(' ', '%20', $item->image) ;
		}
		
		return $list ;
	}
	
	
	/*
	 * function includeWindWalker
	 * @param 
	 */
	
	public static function includeWindWalker()
	{
		if(!defined('AKPATH_ROOT')) {
			$lib_ww_path	= JPATH_LIBRARIES . '/windwalker' ;
		
			// Init WindWalker
			// ===============================================================
			if(!file_exists($ww_path.'/init.php')) {
				return false ;
			}
			
			include_once $ww_path.'/init.php' ;
			return true ;
		}
	}
}
