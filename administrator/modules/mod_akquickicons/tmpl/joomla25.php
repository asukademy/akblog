<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_akquickicons
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
JHtml::_('behavior.modal');

$doc->addStylesheet('components/com_akquickicons/asset/css/akicons.css');
$doc->addStyleSheet('modules/mod_akquickicons/css/akquickicons.css');
ModAkquickiconsHelper::loadFontAwesome();

$tabs = count($buttons) > 1 ? true : false;

$keys = array_keys($buttons);
$uniqid = uniqid();
?>
<style type="text/css">
	.pane-sliders .panel .tabs h3 {
		background-color : transparent;
	}
</style>
<?php if (!empty($buttons)): ?>

	<div class="aqi-module joomla25-layout">
		<!-- Icons -->
		<?php echo $tabs ? JHtmlBootstrap::startTabSet('iconTab-' . $uniqid, array('active' => 'tab-' . $uniqid . '-' . $keys[0])) : null; ?>

		<?php foreach ($buttons as $key => $group): ?>

			<?php echo $tabs ? JHtmlBootstrap::addTab('iconTab-' . $uniqid, 'tab-' . $uniqid . '-' . $key, $group[0]['cat_title']) : null; ?>
			<div class="cpanel">
				<?php foreach ($group as $button): ?>
					<div class="<?php echo (JVERSION >= 3) ? '' : 'icon-wrapper'; ?>">
						<div class="icon <?php echo $button['class'] ?>" id="<?php echo JArrayHelper::getValue($button, 'id'); ?>">
							<a href="<?php echo $button['link']; ?>"
								class="<?php echo $button['params']->get('target') == 'modal' ? 'modal' : ''; ?>"
								target="<?php echo $button['params']->get('target') == 'blank' ? '_blank' : '_self'; ?>"
								>
								<?php echo ModAkquickiconsHelper::getButtonImage($button); ?>
								<div>
									<span><?php echo $button['text']; ?></span>
								</div>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="clearfix"></div>
			</div>
			<div class="clr"></div>
			<?php echo $tabs ? JHtmlBootstrap::endTab() : null; ?>

		<?php endforeach; ?>

		<?php echo $tabs ? JHtmlBootstrap::endTabSet() : null; ?>

	</div>
<?php endif; ?>
<div class="clearfix clr"></div>