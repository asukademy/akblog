<?php
/**
 * @version		$Id: default.php 22338 2011-11-04 17:24:53Z github_bot $
 * @package		Joomla.Site
 * @subpackage	mod_articles_latest
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//JHTML::_('behavior.tooltip');

AKHelper::_('thumb.setCacheURL', 'images/news-thumbs/cache');
AKHelper::_('thumb.setCachePath', JPATH_ROOT.'/images/news-thumbs/cache');
AKHelper::_('thumb.setTempPath', JPATH_ROOT.'/images/news-thumbs/temp');
?>

<ul class="latestnews_photo<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :  
//$img = AK::_( 'content.getArticleImages' , $item->id , true );

/*
$title_content = '<img class="app-icon" style="margin:0 5px 5px 0;float:left;" src="'.
					AKHelper::_('thumb.resize', $item->image, 100, 100, 1).'" alt="'.$item->title.'" />'.
					JString::substr( strip_tags($item->introtext) , 0 , 150 ) ;
*/
?>
	<li>
		<a class="" title="<?php echo $item->title; ?>::<?php echo htmlspecialchars($title_content); ?>" href="<?php echo $item->readmore_link; ?>">
			<h4><span><?php echo $item->title; ?></span></h4>
			<img src="<?php echo AKHelper::_('thumb.resize', $item->image . '?' . $item->id, 225, 125, 1);?>" alt="<?php echo $item->title; ?>"
				onerror="this.src='<?php echo AKHelper::_('thumb.resize', 'image/global/default_img.png', 225, 100, 1); ?>'"/>
		</a>
	</li>
<?php endforeach; ?>
</ul>