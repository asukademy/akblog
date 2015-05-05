<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Filter\FilterHelper;
use Windwalker\Model\ListModel;

// No direct access
defined('_JEXEC') or die;

/**
 * Akquickicons Plugins model
 *
 * @since 1.0
 */
class AkquickiconsModelPlugins extends ListModel
{
	/**
	 * Component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'akquickicons';

	/**
	 * The URL option for the component.
	 *
	 * @var  string
	 */
	protected $option = 'com_akquickicons';

	/**
	 * The prefix to use with messages.
	 *
	 * @var  string
	 */
	protected $textPrefix = 'COM_AKQUICKICONS';

	/**
	 * The model (base) name
	 *
	 * @var  string
	 */
	protected $name = 'plugins';

	/**
	 * Item name.
	 *
	 * @var  string
	 */
	protected $viewItem = 'plugin';

	/**
	 * List name.
	 *
	 * @var  string
	 */
	protected $viewList = 'plugins';

	/**
	 * Configure tables through QueryHelper.
	 *
	 * @return  void
	 */
	protected function configureTables()
	{
		$queryHelper = $this->getContainer()->get('model.plugins.helper.query', Container::FORCE_NEW);

		$queryHelper->addTable('plugin', '#__extensions');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
	}

	/**
	 * prepareGetQuery
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  void
	 */
	protected function prepareGetQuery(\JDatabaseQuery $query)
	{
	}

	/**
	 * The post getQuery object.
	 *
	 * @param JDatabaseQuery $query The db query object.
	 *
	 * @return  void
	 */
	protected function postGetQuery(\JDatabaseQuery $query)
	{
		$query->where($query->format('%n = %q', 'type', 'plugin'));
	}

	/**
	 * getList
	 *
	 * @param string $query
	 * @param int    $limitstart
	 * @param int    $limit
	 *
	 * @return  array
	 */
	public function getList($query, $limitstart = 0, $limit = 0)
	{
		$plugins = parent::getList($query, 0, 0);

		$total = count($plugins);
		$this->cache[$this->getStoreId('getTotal')] = $total;

		$searches = $this->state->get('search');

		if (!empty($searches['plugin.name']))
		{
			foreach ($plugins as $i => $item)
			{
				if (!preg_match("/{$searches['plugin.name']}/i", $item->name))
				{
					unset($plugins[$i]);
				}
			}
		}

		if ($total < $limitstart)
		{
			$limitstart = 0;
			$this->state->set('list.start', 0);
		}

		return array_slice($plugins, $limitstart, $limit ? $limit : null);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method will only called in constructor. Using `ignore_request` to ignore this method.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'plugin.folder, plugin.extension_id', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
	}

	/**
	 * Process the query filters.
	 *
	 * @param JDatabaseQuery $query   The query object.
	 * @param array          $filters The filters values.
	 *
	 * @return  JDatabaseQuery The db query object.
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		return parent::processFilters($query, $filters);
	}

	/**
	 * processSearches
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $searches
	 *
	 * @return  JDatabaseQuery
	 */
	protected function processSearches(JDatabaseQuery $query, $searches = array())
	{
		return $query;
	}

	/**
	 * Configure the filter handlers.
	 *
	 * Example:
	 * ``` php
	 * $filterHelper->setHandler(
	 *     'plugin.date',
	 *     function($query, $field, $value)
	 *     {
	 *         $query->where($field . ' >= ' . $value);
	 *     }
	 * );
	 * ```
	 *
	 * @param FilterHelper $filterHelper The filter helper object.
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
	}

	/**
	 * Configure the search handlers.
	 *
	 * Example:
	 * ``` php
	 * $searchHelper->setHandler(
	 *     'plugin.title',
	 *     function($query, $field, $value)
	 *     {
	 *         return $query->quoteName($field) . ' LIKE ' . $query->quote('%' . $value . '%');
	 *     }
	 * );
	 * ```
	 *
	 * @param SearchHelper $searchHelper The search helper object.
	 *
	 * @return  void
	 */
	protected function configureSearches($searchHelper)
	{
		show($this->state);
	}

	/**
	 * Translate a list of objects
	 *
	 * @param   array  &$items  The array of objects
	 *
	 * @return  array  The array of translated objects
	 */
	protected function translate(&$items)
	{
		$lang = JFactory::getLanguage();

		foreach ($items as &$item)
		{
			$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;
			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true)
			|| $lang->load($extension . '.sys', $source, null, false, true);
			$item->name = JText::_($item->name);
		}
	}
}
