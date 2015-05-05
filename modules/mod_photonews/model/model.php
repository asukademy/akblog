<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_photonews
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * The Photonews model to get data.
 *
 * @since 1.0
 */
class ModPhotonewsModel extends \JModelDatabase
{
	/**
	 * Get item list.
	 *
	 * @param \JRegistry $params
	 *
	 * @return mixed Item list.
	 *
	 * @throws Exception
	 */
	public function getItems(\JRegistry $params)
	{
		if (!$this->includeWindWalker())
		{
			echo 'Please Install WindWalker Framework';

			return false;
		}

		if (!function_exists('file_get_html'))
		{
			include_once __DIR__ . '/../helper/simple_html_dom.php';
		}

		$list = null;
		$db   = JFactory::getDbo();
		$q    = $db->getQuery(true);

		// Access filter
		$access     = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

		$where = \Windwalker\Model\Helper\QueryHelper::publishingItems('a.', 'state');
		$cats  = implode(', ', $params->get('catid'));

		if ($cats)
		{
			$categories = with(new \Windwalker\Joomla\DataMapper\DataMapper('#__categories'))->find(array("id IN ({$cats})"));
			$catWhere = array();

			foreach ($categories as $cat)
			{
				$catWhere[] = 'b.lft >= ' . $cat->lft . ' AND b.rgt <= ' . $cat->rgt;
			}

			if ($catWhere)
			{
				$q->where(new JDatabaseQueryElement('()', $catWhere, ' OR '));
			}
		}

		// Ordering
		$ordering  = $params->get('ordering');
		$order_map = array(
			'm_dsc'  => 'a.modified DESC, a.created DESC',
			'mc_dsc' => 'CASE WHEN (a.modified = ' . $db->quote($db->getNullDate()) . ') THEN a.created ELSE a.modified END',
			'c_dsc'  => 'a.created DESC',
			'p_dsc'  => 'a.publish_up DESC',
			'r'      => 'RAND()'
		);

		$q->select('a.*, b.*, a.title AS title, a.alias AS alias, a.access AS access')
			->from('#__content AS a')
			->leftJoin('#__categories AS b ON b.id = a.catid')
			->where($where)
			->group('a.id')
			->order($order_map[$ordering]);

		$db->setQuery($q, 0, $params->get('count'));
		$list = $db->loadObjectList();

		foreach ($list as &$item)
		{
			// Get Link
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			else
			{
				$item->readmore_link = JRoute::_('index.php?option=com_users&view=login');
			}

			// Get Image
			$item->images = new JRegistry($item->images);

			if ($item->images->get('image_intro'))
			{
				$item->image = $item->images->get('image_intro');
			}
			elseif ($item->images->get('image_fulltext'))
			{
				$item->image = $item->images->get('image_fulltext');
			}
			else
			{
				$text = JHtmlContent::prepare($item->introtext);

				$html = str_get_html($text);
				$imgs = $html->find('img');

				$item->image = '';

				if (!empty($imgs[0]))
				{
					$item->image = $imgs[0]->src;
				}
			}

			$item->image = str_replace(' ', '%20', $item->image);
		}

		return $list;
	}

	/**
	 * includeWindWalker
	 *
	 * @return  boolean
	 */
	protected function includeWindWalker()
	{
		$windwalkerInitFile = JPATH_LIBRARIES . '/windwalker/src/init.php';

		if (defined('WINDWALKER'))
		{
			return true;
		}

		if (!is_file($windwalkerInitFile))
		{
			return false;
		}

		include_once $windwalkerInitFile;

		return true;
	}
}
