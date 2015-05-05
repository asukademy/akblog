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
 * Module helper to provides some useful methods.
 *
 * @since 2.0
 */
abstract class ModAkquickiconsHelper
{
	/**
	 * getButtonImage
	 *
	 * @param array $button
	 *
	 * @return  string
	 */
	public static function getButtonImage($button)
	{
		if (!empty($button['image']))
		{
			return JHtml::image($button['image'], $button['id'], null, true);
		}

		if (!empty($button['icon_class']))
		{
			return "<i class=\"{$button['icon_class']}\"></i>";
		}

		return "<i class=\"icon-joomla\"></i>";
	}

	/**
	 * getButtonImage
	 *
	 * @param array $button
	 *
	 * @return  string
	 */
	public static function getButtonIcon($button)
	{
		if (!empty($button['icon_class']))
		{
			return "<i class=\"{$button['icon_class']}\"></i>";
		}

		if (!empty($button['image']))
		{
			return JHtml::image($button['image'], $button['id'], null, true);
		}

		return "<i class=\"icon-joomla\"></i>";
	}

	/**
	 * loadFontAwesome
	 *
	 * @return  void
	 */
	public static function loadFontAwesome()
	{
		$doc = JFactory::getDocument();

		$doc->addStyleSheetVersion('components/com_akquickicons/asset/css/font-awesome.css');
	}

	/**
	 * Escape text for safe.
	 *
	 * @param string $text Text to escape.
	 *
	 * @return  string  Escaped text.
	 */
	public static function escape($text)
	{
		return htmlspecialchars($text);
	}

	/**
	 * show
	 *
	 * @param mixed $data
	 *
	 * @return  void
	 */
	public static function show($data)
	{
		echo "<pre>" . print_r($data, 1) . "</pre>";
	}
}
