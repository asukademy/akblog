<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\GridView;
use Windwalker\Xul\XulEngine;

// No direct access
defined('_JEXEC') or die;

/**
 * Akquickicons Plugins View
 *
 * @since 1.0
 */
class AkquickiconsViewPluginsHtml extends GridView
{
	/**
	 * The component prefix.
	 *
	 * @var  string
	 */
	protected $prefix = 'akquickicons';

	/**
	 * The component option name.
	 *
	 * @var string
	 */
	protected $option = 'com_akquickicons';

	/**
	 * The text prefix for translate.
	 *
	 * @var  string
	 */
	protected $textPrefix = 'COM_AKQUICKICONS';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $name = 'plugins';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = 'plugin';

	/**
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = 'plugins';

	/**
	 * Method to instantiate the view.
	 *
	 * @param Model            $model     The model object.
	 * @param Container        $container DI Container.
	 * @param array            $config    View config.
	 * @param SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		$config['grid'] = array(
			// Some basic setting
			'option'    => 'com_akquickicons',
			'view_name' => 'plugin',
			'view_item' => 'plugin',
			'view_list' => 'plugins',

			// Column which we allow to drag sort
			'order_column'   => 'plugin.catid, plugin.ordering',

			// Table id
			'order_table_id' => 'pluginList',

			// Ignore user access, allow all.
			'ignore_access'  => false
		);

		// Directly use php engine
		$this->engine = new PhpEngine;

		parent::__construct($model, $container, $config, $paths);
	}

	/**
	 * Prepare data hook.
	 *
	 * @return  void
	 */
	protected function prepareData()
	{
		foreach ($this->data->items as $item)
		{
			$query = array(
				'option' => 'com_plugins',
				'task' => 'plugin.edit',
				'extension_id' => $item->extension_id
			);

			$item->link = 'index.php?' . http_build_query($query);
		}
	}

	/**
	 * Configure the toolbar button set.
	 *
	 * @param   array   $buttonSet Customize button set.
	 * @param   object  $canDo     Access object.
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		// Get default button set.
		$buttonSet = parent::configureToolbar($buttonSet, $canDo);

		// In debug mode, we remove trash button but use delete button instead.
		if (JDEBUG)
		{
			$buttonSet['trash']['access']  = false;
			$buttonSet['delete']['access'] = true;
		}

		return $buttonSet;
	}
}
