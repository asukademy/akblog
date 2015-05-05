<?php
/**
 * Part of Akquickicons Component project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Registry\Registry;

/**
 * Class AkquickiconsViewImagesHtml
 *
 * @since 1.0
 */
class AkquickiconsViewImagesHtml extends \Windwalker\View\Html\GridView
{
	/**
	 * The elFinder default toolbar buttons.
	 *
	 * @var  array
	 */
	protected $defaultToolbar = array(
		array('back', 'forward'),
		array('reload'),
		// array('home', 'up'),
		array('mkdir', 'mkfile', 'upload'),
		// array('open', 'download', 'getfile'),
		array('info'),
		array('quicklook'),
		array('copy', 'cut', 'paste'),
		array('rm'),
		array('duplicate', 'rename', 'edit', 'resize'),
		// array('extract', 'archive'),
		array('search'),
		array('view'),
		array('help')
	);

	/**
	 * Render this view.
	 *
	 * @return string
	 */
	public function prepareData()
	{
		// Init some API objects
		// ================================================================================
		$container  = $this->getContainer();
		$input      = $container->get('input');
		$asset      = $container->get('helper.asset');
		$lang       = $container->get('language');
		$lang_code  = $lang->getTag();
		$lang_code  = str_replace('-', '_', $lang_code);

		$com_option = $this->option ? : $input->get('option');
		$config     = new Registry($this->data->config);

		// Script
		$this->displayScript($com_option, $config);

		$toolbar = $config->get('toolbar', $this->defaultToolbar);
		$toolbar = json_encode($toolbar);

		$onlymimes = '"image/jpeg", "image/png", "image/jpg"';

		// Get INI setting
		$upload_max = ini_get('upload_max_filesize');
		$upload_num = ini_get('max_file_uploads');

		$upload_limit = 'Max upload size: ' . $upload_max;
		$upload_limit .= ' | Max upload files: ' . $upload_num;

		$script = <<<SCRIPT
        var elFinder ;

		// Init elFinder
        jQuery(document).ready(function($) {
            elFinder = $('#elfinder').elfinder({
                url         : 'index.php?option=com_akquickicons&task=images.connect' ,
                width       : '100%' ,
                height      : 500 ,
                onlyMimes   : [$onlymimes],
                lang        : '{$lang_code}',
                uiOptions   : {
                    toolbar : {$toolbar}
                }
            }).elfinder('instance');

            elFinder.ui.statusbar.append( '<div class="akfinder-upload-limit">{$upload_limit}</div>' );
        });
SCRIPT;

		$asset->internalJS($script);
	}

	/**
	 * Display elFinder script.
	 *
	 * @param string $com_option Component option name.
	 * @param array  $config     Config array.
	 *
	 * @return void
	 */
	private function displayScript($com_option, $config)
	{
		$lang      = $this->container->get('language');
		$lang_code = $lang->getTag();
		$lang_code = str_replace('-', '_', $lang_code);

		// Include elFinder and JS
		// ================================================================================

		$asset = $this->container->get('helper.asset');

		// JQuery
		$asset->jquery();
		$asset->bootstrap();

		// ElFinder includes
		$asset->addCss('js/jquery-ui/css/smoothness/jquery-ui-1.8.24.custom.css');
		$asset->addCss('js/elfinder/css/elfinder.min.css');
		$asset->addCss('js/elfinder/css/theme.css');

		$asset->addJs('js/jquery-ui/js/jquery-ui.min.js');
		$asset->addJs('js/elfinder/js/elfinder.min.js');

		if (is_file(JPATH_LIBRARIES . '/windwalker/asset/js/elfinder/js/i18n/elfinder.' . $lang_code . '.js'))
		{
			$asset->addJs('js/elfinder/js/i18n/elfinder.' . $lang_code . '.js');
		}
	}

	/**
	 * setTitle
	 *
	 * @param null   $title
	 * @param string $icons
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'pictures')
	{
		parent::setTitle(JText::_('COM_AKQUICKICONS_TITLE_IMAGES'), $icons);
	}

	/**
	 * configureToolbar
	 *
	 * @param array   $buttonSet
	 * @param object  $canDo
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		$buttonSet['option']['handler'] = function()
		{
			JToolbarHelper::preferences('com_akquickicons');
		};

		return $buttonSet;
	}
}
 