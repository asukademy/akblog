<?php

/**
 * @package 	mod_btslideshow_pro
 * @version	1.1
 * @created	Aug 2011
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
// No direct access
defined('_JEXEC') or die;

if (!class_exists('MyEzset\Content\Articles'))
{
	echo 'Please enable Ezset.';
	return;
}

$items = \MyEzset\Content\Articles::getArticles($params);

// $items = json_decode(base64_decode($params->get('gallery')));
if (count($items) == 0) {
    echo JText::_('MOD_BTSLIDESHOW_NOTICE_NO_IMAGES');
    return;
}

$moduleID = $module->id;

// Make thumbnail & slideshow images if haven't created or just change the size
require_once (JPATH_ROOT . '/modules/mod_btslideshow_pro/helpers/images.php');
require_once (JPATH_ROOT . '/modules/mod_btslideshow_pro/helpers/helper.php');
BTSlideshowHelper::fetchHead($params);
$dir = JPATH_ROOT . '/modules/mod_btslideshow_pro/images/';
$width = $params->get('width');
$thumb_width = $params->get('thumb_width');
$height = $params->get('height');
$thumb_height = $params->get('thumb_height');
$compression = $params->get('jpeg_compression');
//check if original image aren't created
foreach ($items as $key => $item) {
    if (!JFile::exists("{$dir}/original/{$item->file}")){
        if(JFile::exists("{$dir}/tmp/original/{$item->file}")) {
            JFile::move($dir.'/tmp/original/'. $item->file, $dir.'/original/'. $item->file);
            JFile::move($dir . '/tmp/manager/' . $item->file, $dir . '/manager/' . $item->file);
        }else{
            // unset($items[$key]);
			continue;
        }
    }
    if (!empty($item->link))
        $item->link = JRoute::_($item->link);
    if (JFile::exists("{$dir}/original/{$item->file}")) {
		if($params->get('crop_image',1)){
			if (!is_numeric($params->get('width'))) {
				JFile::copy("{$dir}/original/{$item->file}", "{$dir}/slideshow/{$item->file}");
			} else {
				BTImageHelper::createImage("{$dir}/original/{$item->file}", "{$dir}/slideshow/{$item->file}", $width, $height, true, $compression);
			}
			$item->mainImage = JURI::root() . "modules/mod_btslideshow_pro/images/slideshow/{$item->file}";
		}
		else{
			$item->mainImage = JURI::root() . "modules/mod_btslideshow_pro/images/original/{$item->file}";
		}
		if($params->get('crop_thumb',1)){
			BTImageHelper::createImage("{$dir}/original/{$item->file}", "{$dir}/thumbnail/{$item->file}", $thumb_width, $thumb_height, true, $compression);
			$item->thumbImage = JURI::root() . "modules/mod_btslideshow_pro/images/thumbnail/{$item->file}";
		}
		else{
			$item->thumbImage = $item->mainImage;
		}
    }	
}
$document = JFactory::getDocument();
if(!$params->get('responsive',0)){
	$document->addStyleDeclaration("#mod_btslideshow_pro_{$moduleID},#mod_btslideshow_pro_{$moduleID} .container_skitter img{height:{$height}px;width:{$width}px;}");
}
// By Asika
$items = \MyEzset\Content\Articles::getImages($items, $params);
require JModuleHelper::getLayoutPath('mod_btslideshow_pro', $params->get('layout', 'default'));
?>