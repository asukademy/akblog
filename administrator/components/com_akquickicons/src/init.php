<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';

JLoader::registerPrefix('Akquickicons', JPATH_BASE . '/components/com_akquickicons');
JLoader::registerNamespace('Akquickicons', JPATH_ADMINISTRATOR . '/components/com_akquickicons/src');
JLoader::registerNamespace('Windwalker', __DIR__);
JLoader::register('AkquickiconsComponent', JPATH_BASE . '/components/com_akquickicons/component.php');
