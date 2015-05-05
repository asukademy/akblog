<?php
/**
 * Part of Akquickicons Component project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

?>
<style>
	.elfinder-button-search input
	{
		padding-left: 20px;
	}

	.elfinder-ltr .elfinder-button-search .ui-icon-close
	{
		right: 13px;
	}
</style>
<div id="akquickicons" class="image-manager">
	<div class="row-fluid">
		<div class="span2">
			<h4 class="page-header"><?php echo JText::_('JOPTION_MENUS'); ?></h4>
			<?php echo $this->data->sidebar; ?>
		</div>

		<div class="span10">
			<div id="elfinder"></div>
		</div>
	</div>
</div>
