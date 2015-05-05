<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;

JHtml::_('behavior.tooltip');
JHtmlBehavior::framework(true);

/**
 * Prepare data for this template.
 *
 * @var Windwalker\DI\Container       $container
 * @var Windwalker\Helper\AssetHelper $asset
 */
$container = $this->getContainer();
$input     = $container->get('input');
$grid      = $data->grid;
$data->asset = $container->get('helper.asset');

$function = $input->get('function', 'jSelectArticle');

$lang = JFactory::getLanguage();
$lang->load('com_modules');
?>

<div id="akquickicons" class="windwalker modules tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<?php echo with(new FileLayout('joomla.searchtools.default'))->render(array('view' => $this->data)); ?>

		<table id="moduleList" class="adminlist table table-striped modal-list">
			<thead>
			<tr>
				<!--TITLE-->
				<th class="center">
					<?php echo $grid->sortTitle('JGLOBAL_TITLE', 'module.title'); ?>
				</th>

				<!--CLIENT-->
				<th class="center">
					<?php echo $grid->sortTitle('JSITE', 'ext.client_id'); ?>
				</th>

				<!--POSITION-->
				<th class="center">
					<?php echo $grid->sortTitle('COM_MODULES_HEADING_POSITION', 'module.position'); ?>
				</th>

				<!--NAME-->
				<th class="center">
					<?php echo $grid->sortTitle('COM_MODULES_HEADING_MODULE', 'ext.name'); ?>
				</th>

				<!--ID-->
				<th width="1%" class="nowrap center">
					<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'module.id'); ?>
				</th>
			</tr>
			</thead>

			<!--PAGINATION-->
			<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $data->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>

			<!-- TABLE BODY -->
			<tbody>
			<?php foreach ($data->items as $i => $item)
				:

				$client    = 'site';
				$extension = $item->module;
				$source    = constant('JPATH_' . strtoupper($client)) . "/modules/$extension";
				$lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), null, false, false)
				|| $lang->load("$extension.sys", $source, null, false, false)
				|| $lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), $lang->getDefault(), false, false)
				|| $lang->load("$extension.sys", $source, $lang->getDefault(), false, false);

				$item->name = JText::_($item->ext_name);

				$client    = 'administrator';
				$extension = $item->module;
				$source    = constant('JPATH_' . strtoupper($client)) . "/modules/$extension";
				$lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), null, false, false)
				|| $lang->load("$extension.sys", $source, null, false, false)
				|| $lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), $lang->getDefault(), false, false)
				|| $lang->load("$extension.sys", $source, $lang->getDefault(), false, false);

				$item->name = JText::_($item->ext_name);

				// Prepare data
				$item = new Data($item);

				// Prepare item for GridHelper
				$grid->setItem($item, $i);
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<!--TITLE-->
					<td class="n/owrap has-context quick-edit-wrap">
						<div class="item-title">
							<a class="pointer" style="cursor: pointer;"
								onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->link; ?>','<?php echo $this->escape(addslashes($item->title)); ?>');"
								>
								<?php echo $this->escape($item->title); ?>
							</a>
						</div>

						<?php if (!empty($item->note)) : ?>
							<div class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note));?>
							</div>
						<?php endif; ?>
					</td>

					<!--CLIENT-->
					<td class="center">
						<?php echo $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE'); ?>
					</td>

					<!--POSITION-->
					<td class="center">
						<?php if ($item->position) : ?>
							<span class="label label-info">
							<?php echo $item->position; ?>
						</span>
						<?php else : ?>
							<span class="label">
							<?php echo JText::_('JNONE'); ?>
						</span>
						<?php endif; ?>
					</td>

					<!--NAME-->
					<td class="">
						<?php echo $item->name;?>
					</td>

					<!--ID-->
					<td class="center">
						<?php echo $item->id; ?>
					</td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>