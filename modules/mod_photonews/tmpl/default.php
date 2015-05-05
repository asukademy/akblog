<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_photonews
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$thumb = new \Windwalker\Image\Thumb;

$thumb->setCachePosition('images/photonews');
?>
<div class="photonews-module-wrap<?php echo $classSfx; ?>">
	<div class="photonews-module-wrap-inner">

		<ul class="photonews-module-list nav nav-tabs nav-stacked">
			<?php foreach ($items as $item): ?>
				<li>
					<a class="" href="<?php echo $item->readmore_link; ?>">
						<h4><span><?php echo $item->title; ?></span></h4>
						<img src="<?php echo $thumb->resize($item->image . '?' . $item->id, 260, 125, true);?>" alt="<?php echo $item->title; ?>" />
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</div>