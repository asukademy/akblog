<?php
/**
 * Part of Akquickicons Component project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class AkquickiconsControllerIconsStatePublish
 *
 * @since 1.0
 */
class AkquickiconsControllerIconsStateUnpublish extends \Windwalker\Controller\State\UnpublishController
{
	/**
	 * The data fields to update.
	 *
	 * @var string
	 */
	protected $stateData = array(
		'published' => 0
	);
}
