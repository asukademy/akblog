<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Windwalker\DI\Container;
use Windwalker\Model\Model;
use Windwalker\View\Engine\PhpEngine;
use Windwalker\View\Html\EditView;
use Windwalker\Xul\XulEngine;

// No direct access
defined('_JEXEC') or die;

/**
 * Akquickicons Icons view
 *
 * @since 1.0
 */
class AkquickiconsViewIconHtml extends EditView
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
	protected $name = 'icon';

	/**
	 * The item name.
	 *
	 * @var  string
	 */
	protected $viewItem = 'icon';

	/**
	 * The list name.
	 *
	 * @var  string
	 */
	protected $viewList = 'icons';

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
		parent::prepareData();
	}

	/**
	 * setTitle
	 *
	 * @param string $title
	 * @param string $icons
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'pencil-2')
	{
		parent::setTitle(JText::_('COM_AKQUICKICONS_TITLE_ICON'), $icons);
	}
}
