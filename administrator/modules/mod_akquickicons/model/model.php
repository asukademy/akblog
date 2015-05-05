<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_akquickicons
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The Akquickicons model to get data.
 *
 * @since 2.0
 */
class ModAkquickiconsModel extends \JModelDatabase
{
	/**
	 * Property buttons.
	 *
	 * @var  array
	 */
	public $buttons = array();

	/**
	 * getItems
	 *
	 * @param \JRegistry $params
	 *
	 * @return  array
	 */
	public function getItems($params)
	{
		$list          = null;
		$this->buttons = array();
		$date          = JFactory::getDate('now', JFactory::getConfig()->get('offset'));
		$user          = JFactory::getUser();
		$lang          = JFactory::getLanguage();

		// Load Mod Quickicon language
		$lang->load('mod_quickicon', JPATH_BASE, null, false, false) || $lang->load('mod_quickicon', JPATH_BASE . '/modules/mod_quickicon', null, false, false)
		|| $lang->load('mod_quickicon', JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load('mod_quickicon', JPATH_BASE . '/modules/mod_quickicon', $lang->getDefault(), false, false);

		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		$catid = $params->get('catid', array(78));
		$catid = is_array($catid) ? $catid : array($catid);

		if (!in_array(1, $catid))
		{
			$catid = implode(',', $catid);

			$q->where("a.catid IN ({$catid})");
		}

		$now = $date->toSQL(true);

		$publish = "((a.publish_up = '0000-00-00 00:00:00' OR a.publish_up < '{$now}') AND
					(a.publish_down = '0000-00-00 00:00:00' OR a.publish_down > '{$now}'))";

		$viewlevel = $user->getAuthorisedViewLevels();
		$viewlevel = implode(',', $viewlevel);

		$language = JFactory::getLanguage()->getTag();
		$select   = "a.*, b.*,
		a.link 		AS link,
		a.images 	AS images,
		a.title 	AS title,
		a.access 	AS access,
		a.alias		AS alias,
		a.params	AS params,
		b.title 	AS cat_title,
		b.alias	    AS cat_alias
		";

		$q->select($select)
			->from("#__akquickicons_icons AS a")
			->join('LEFT', "#__categories AS b ON a.catid = b.id")
			->where("b.extension = 'com_akquickicons'")
			->where("a.published >=1")
			->where($publish)
			->where("a.access IN ({$viewlevel})")
			->where("(a.language = '{$language}' OR a.language = '*')")
			->where("b.published > 0")
			->order("b.lft, a.ordering");

		$db->setQuery($q);
		$buttons = $db->loadObjectList();

		foreach ($buttons as $button)
		{
			$button->params = new \JRegistry($button->params);

			$uri = new JUri($button->link);

			// Smart URL
			if ($button->params->get('smart_url', 1))
			{
				$uri = $this->smartUrlConvertor($uri, $button);
			}

			$catid = $button->catid;

			$this->buttons[$catid][] = array(
				'link'       => JRoute::_($uri->toString()),
				'image'      => $button->images ? JURI::root() . $button->images : null,
				'text'       => $button->params->get('langkey') ? JText::_($button->params->get('langkey')) : $button->title,
				'icon_class' => $button->icon_class,
				'access'     => true,
				'cat_title'  => $button->cat_title,
				'id'         => $button->params->get('id', 'akicon_' . str_replace('-', '_', $button->alias)),
				'class'      => $button->params->get('class', 'ak-icon-item'),
				'params'     => $button->params
			);
		}

		if ($params->get('get_icon_from_plugins', 1))
		{
			$this->getIconFromPlugins($params);
		}

		return $this->buttons;
	}

	/**
	 * getIconFromPlugins
	 *
	 * @param \JRegistry $params
	 *
	 * @return  array
	 */
	public function getIconFromPlugins($params)
	{
		// Include buttons defined by published quickicon plugins
		$keys = array_keys($this->buttons);
		JPluginHelper::importPlugin('quickicon');
		$app    = JFactory::getApplication();
		$arrays = (array) $app->triggerEvent('onGetIcons', array('mod_quickicon'));

		// Extensions plugin image map
		foreach ($arrays as $response)
		{
			foreach ($response as $icon)
			{
				$default = array(
					'link'   => null,
					'text'   => null,
					'image'  => 'joomla',
					'access' => true,
					'class'  => 'ak-icon-item'
				);

				$icon = array_merge($default, $icon);

				if (!is_null($icon['link']) && !is_null($icon['text']))
				{
					$icon['icon_class'] = 'icon-' . $icon['image'];

					unset($icon['image']);

					// Set params
					if (isset($icon['params']))
					{
						$icon['params'] = ($icon['params'] instanceof JRegistry) ? $icon['params'] : new JRegistry();
					}
					else
					{
						$icon['params'] = new JRegistry;
					}

					if (!isset ($keys[0]))
					{
						$keys[0] = null;
					}

					if (!isset ($buttons[$keys[0]]))
					{
						$buttons[$keys[0]] = array();
					}

					$this->buttons[$keys[0]][] = $icon;
				}
			}
		}

		return $this->buttons;
	}

	/**
	 * smartUrlConvertor
	 *
	 * @param JUri  $uri
	 * @param array $button
	 *
	 * @return  mixed
	 */
	public function smartUrlConvertor($uri, $button)
	{
		// fix task redirect
		if ($uri->getVar('layout') == 'edit')
		{
			$uri->setVar('task', "{$uri->getVar('view')}.{$uri->getVar('layout')}");
			$uri->delVar('view');
			$uri->delVar('layout');
		}

		return $uri;
	}
}
