<?php
/**
 * Part of knowledge project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace MyEzset\Content;

use Windwalker\Helper\UriHelper;
use Windwalker\Image\Thumb;

/**
 * The Articles class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class Articles
{
	/**
	 * getArticles
	 *
	 * @param \JRegistry $params
	 *
	 * @return  \stdClass[]
	 */
	public static function getArticles(&$params)
	{
		include_once JPATH_ROOT . '/modules/mod_articles_latest/helper.php';

		$params->set('count', 7);
		$params->set('ordering', 'random');
		$params->set('show_featured', '1');
		$params->set('user_id', '0');

		$items = \ModArticlesLatestHelper::getList($params);

		foreach ($items as &$item)
		{
			$item->file = null;
		}

		return $items;
	}

	/**
	 * getImages
	 *
	 * @param \stdClass[] $items
	 * @param \JRegistry  $params
	 *
	 * @return  \stdClass[]
	 */
	public static function getImages($items, \JRegistry $params)
	{
		$thumb = new Thumb;
		$width = $params->get('width');
		$thumb_width = $params->get('thumb_width');
		$height = $params->get('height');
		$thumb_height = $params->get('thumb_height');
		$crop = (bool) $params->get('crop_image', true);

		foreach ($items as &$item)
		{
			$img = new \JRegistry($item->images);

			$image = $img->get('image_intro');

			if (!$image)
			{
				$image = static::getFirstImage($item->introtext);
			}

			if (!$image)
			{
				continue;
			}

			$image = UriHelper::pathAddHost($image);

			$item->mainImage = $thumb->resize($image, $width, $height, $crop);
			$item->thumbImage = $thumb->resize($image, $thumb_width, $thumb_height, $crop);
			$item->file = pathinfo($image, PATHINFO_BASENAME);
		}

		return $items;
	}

	/**
	 * getFirstImage
	 *
	 * @param string $html
	 *
	 * @return  string
	 */
	public static function getFirstImage($html)
	{
		$html = \JHtmlContent::prepare($html);

		preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $html, $matches);

		return isset($matches[1]) ? $matches[1] : null;
	}
}
