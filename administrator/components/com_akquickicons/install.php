<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Script file of Akquickicons component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_akquickicons
 */
class Com_AkquickiconsInstallerScript
{
	/**
	 * Method to install the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function install(\JInstallerAdapterComponent $parent)
	{
		// Set Default datas with asset.
		$db = JFactory::getDbo();

		$p_installer = $parent->getParent();
		$path        = $p_installer->getPath('source');

		jimport('joomla.filesystem.folder');
		$origin = $path . '/images/quickicons';
		$target = JPATH_ROOT . '/images/quickicons';

		if (!is_dir($target))
		{
			if (JFolder::copy($origin, $target))
			{
				JFactory::getApplication()->enqueueMessage('Copy icons to: ' . $target, 'message');
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage('images/quickicons folder has exists.', 'warning');
		}

		// Set Category
		$q = $db->getQuery(true);

		$q->select('id')
			->from('#__categories')
			->where("extension = 'com_akquickicons'");

		$db->setQuery($q);
		$catids = $db->loadColumn();

		$cat = JTable::getInstance('Category');

		foreach ($catids as $catid)
		{
			$cat->load($catid);
			$cat->store();
		}

		// Set icons
		$q = $db->getQuery(true);

		$q->select('id')
			->from('#__akquickicons_icons');

		$db->setQuery($q);
		$icon_ids = $db->loadColumn();

		$table_path = $path . '/table/icon.php';

		include_once $table_path;

		$icon = JTable::getInstance('icon', 'AkquickiconsTable');

		foreach ($icon_ids as $k => $icon_id)
		{
			$icon->load($icon_id);
			$icon->catid = $catids[0];
			$icon->store();
		}

		$this->catid = $catids[0];
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function uninstall(\JInstallerAdapterComponent $parent)
	{
		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		$q->select('extension_id')
			->from('#__extensions')
			->where("element='mod_akquickicons'");

		$db->setQuery($q);
		$result = $db->loadResult();

		if ($result)
		{
			$installer = new JInstaller;
			$installer->uninstall('module', $result);

			$q = $db->getQuery(true);

			$q->delete('#__categories')
				->where("extension='com_akquickicons'");
			$db->setQuery($q);
			$db->execute();
		}
	}

	/**
	 * Method to update the component
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function update(\JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * ethod to run before an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function preflight($type, \JInstallerAdapterComponent $parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function postflight($type, \JInstallerAdapterComponent $parent)
	{
		$db = JFactory::getDbo();

		// Get install manifest
		// ========================================================================
		$p_installer = $parent->getParent();
		$installer   = new JInstaller;
		$manifest    = $p_installer->manifest;
		$path        = $p_installer->getPath('source');
		$result      = array();

		$css = <<<CSS
<style type="text/css">
#ak-install-img
{
}

#ak-install-msg
{
}
</style>
CSS;

		echo $css;

		$installScript = dirname($path) . '/windwalker/src/System/installscript.php';

		if (!is_file($installScript))
		{
			$installScript = JPATH_LIBRARIES . '/windwalker/src/System/installscript.php';
		}

		include $installScript;

		// Set Module active
		// ========================================================================
		if ($type == 'install')
		{
			$q = $db->getQuery(true);

			$q->select('*')
				->from('#__modules')
				->where("module='mod_akquickicons'");

			$db->setQuery($q);

			$module = $db->loadObject();

			$module->published = 1;
			$module->position  = 'cpanel';
			$params            = new stdClass;
			$params->catid     = 1;
			$module->params    = json_encode($params);

			$db->updateObject('#__modules', $module, 'id');

			$in           = new stdClass;
			$in->moduleid = $module->id;
			$in->menuid   = 0;

			$db->insertObject('#__modules_menu', $in);
		}
	}
}
