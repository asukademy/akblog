<?php
/**
 * Part of Akquickicons Component project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Akquickicons\Helper;

/**
 * Class IconHelper
 *
 * @since 1.0
 */
class IconHelper
{
	/**
	 * Property icons.
	 *
	 * @var  array
	 */
	protected static $icons = array();

	/**
	 * getIcons
	 *
	 * @return  array
	 */
	public static function getIcons()
	{
		if (static::$icons)
		{
			return static::$icons;
		}

		$icons = file_get_contents(static::getIconsFile());

		$icons = explode("\n", $icons);

		foreach ($icons as $k => $icon)
		{
			$icons[$k] = trim($icon);

			if (! $icon)
			{
				unset($icons[$k]);
			}
		}

		return static::$icons = $icons;
	}

	/**
	 * getIconsFile
	 *
	 * @return  string
	 */
	public static function getIconsFile()
	{
		return AKQUICKICONS_ADMIN . '/etc/icons.txt';
	}
}
 