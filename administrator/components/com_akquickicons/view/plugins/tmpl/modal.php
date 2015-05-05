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

$search = $data->state->get('search', array());

$lang = JFactory::getLanguage();
$lang->load('com_plugins');
?>

<div id="akquickicons" class="windwalker plugins tablelist row-fluid">
	<form action="<?php echo JURI::getInstance(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<?php echo with(new FileLayout('joomla.searchtools.default'))->render(array('view' => $this->data)); ?>

		<table id="pluginList" class="adminlist table table-striped modal-list">
			<thead>
			<tr>
				<!--TITLE-->
				<th class="center">
					<?php echo $grid->sortTitle('COM_PLUGINS_NAME_HEADING', 'plugin.name'); ?>
				</th>

				<!--FOLDER-->
				<th class="center">
					<?php echo $grid->sortTitle('COM_PLUGINS_FOLDER_HEADING', 'plugin.folder'); ?>
				</th>

				<!--ELEMENT-->
				<th class="center">
					<?php echo $grid->sortTitle('COM_PLUGINS_ELEMENT_HEADING', 'plugin.element'); ?>
				</th>

				<!--ID-->
				<th width="1%" class="nowrap center">
					<?php echo $grid->sortTitle('JGRID_HEADING_ID', 'plugin.id'); ?>
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

				$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
				$extension = 'plg_' . $item->folder . '_' . $item->element;
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
				||	$lang->load($extension . '.sys', $source, null, false, false)
				||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
				||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);

				$item->title = JText::_($item->name);

				// Search
				// ========================================================================
				if(!empty($search['index']))
				{
					$title = JText::_($item->name);

					if( strpos( strtolower($title), strtolower($search['index']) ) === false )
					{
						continue;
					}
				}

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
					</td>

					<!--CATEGORY-->
					<td class="center">
						<?php echo $this->escape($item->folder); ?>
					</td>

					<!--ACCESS VIEW LEVEL-->
					<td class="center">
						<?php echo $this->escape($item->element); ?>
					</td>

					<!--ID-->
					<td class="center">
						<?php echo $item->extension_id; ?>
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