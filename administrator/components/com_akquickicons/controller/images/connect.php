<?php
/**
 * Part of Akquickicons Component project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Registry\Registry;

/**
 * Class AkquickiconsControllerImagesConnect
 *
 * @since 1.0
 */
class AkquickiconsControllerImagesConnect extends \Windwalker\Controller\Controller
{
	/**
	 * Method to run this controller.
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		// Init some API objects
		// ================================================================================
		$elfinder_path = WINDWALKER . '/src/Elfinder/Connect/';

		include_once $elfinder_path . 'elFinderConnector.class.php';
		include_once $elfinder_path . 'elFinder.class.php';
		include_once $elfinder_path . 'elFinderVolumeDriver.class.php';

		/**
		 * Simple function to demonstrate how to control file access using "accessControl" callback.
		 * This method will disable accessing files/folders starting from '.' (dot)
		 *
		 * @param  string $attr attribute name (read|write|locked|hidden)
		 * @param  string $path file path relative to volume root directory started with directory separator
		 *
		 * @return bool|null
		 */
		function access($attr, $path)
		{
			// If file/folder begins with '.' (dot). Set read+write to false, other (locked+hidden) set to true
			if (strpos(basename($path), '.') === 0)
			{
				return !($attr == 'read' || $attr == 'write');
			}
			// Else elFinder decide it itself
			else
			{
				return null;
			}
		}

		$opts = array(
			// 'debug' => true,
			'roots' => array(
				array(
					// Driver for accessing file system (REQUIRED)
					'driver'        => 'LocalFileSystem',

					// Path to files (REQUIRED)
					'path'          => JPath::clean(JPATH_ROOT . '/images/quickicons'),
					// 'startPath'     => JPath::clean(JPATH_ROOT . '/images/quickicons/'),
					'URL'           => JPath::clean(JURI::root(true) . '/images/quickicons/'), // URL to files (REQUIRED)
					'tmbPath'       => JPath::clean(JPATH_ROOT . '/cache/aqi-finder-thumb'),
					'tmbURL'        => JURI::root(true) . '/cache/aqi-finder-thumb',
					// 'tmbSize'       => 128,
					'tmp'           => JPath::clean(JPATH_ROOT . '/cache/aqi-finder-temp'),

					// Disable and hide dot starting files (OPTIONAL)
					'accessControl' => 'access',
					'uploadDeny'    => array('text/x-php'),
					// 'uploadAllow'   => array('image'),
					'disabled'      => array('archive', 'extract', 'rename', 'mkfile')
				)
			)
		);

		foreach ($opts['roots'] as $driver)
		{
			include_once $elfinder_path . 'elFinderVolume' . $driver['driver'] . '.class.php';
		}

		// Run elFinder
		$connector = new elFinderConnector(new elFinder($opts));
		$connector->run();

		exit();
	}
}
 