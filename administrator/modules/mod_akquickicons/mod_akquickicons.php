<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_akquickicons
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
JLoader::registerPrefix('ModAkquickicons', __DIR__);

$model    = new ModAkquickiconsModel($params);
$buttons  = $model->getItems($params);
$classSfx = ModAkquickiconsHelper::escape($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_akquickicons', $params->get('layout', 'joomla25'));
