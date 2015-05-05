<?php
/**
 * @package        Asikart.Module
 * @subpackage     mod_photonews
 * @copyright      Copyright (C) 2014 SMS Taiwan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
JLoader::registerPrefix('ModPhotonews', __DIR__);

$model    = new ModPhotonewsModel($params);
$items    = $model->getItems($params);
$classSfx = ModPhotonewsHelper::escape($params->get('moduleclass_sfx'));

if (!$items)
{
	return;
}

require JModuleHelper::getLayoutPath('mod_photonews', $params->get('layout', 'default'));
