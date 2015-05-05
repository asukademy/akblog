<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_akquickicons
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Supports a modal article picker.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_content
 * @since          1.6
 */
class JFormFieldQuick_Menus extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Quick_Menus';

	/**
	 * Property view_list.
	 *
	 * @var  string
	 */
	protected $view_list = 'icons';

	/**
	 * Property view_item.
	 *
	 * @var  string
	 */
	protected $view_item = 'icon';

	/**
	 * Property extension.
	 *
	 * @var  string
	 */
	protected $extension = 'com_akquickicons';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	public function getInput()
	{
		$app = JFactory::getApplication();

		$app->input->set('hidemainmenu', false);

		$menu = JModuleHelper::getModule('mod_menu');

		$menu = JModuleHelper::renderModule($menu);

		$menu = str_replace('<ul id="menu" class="nav ', '<ul id="menu" class="nav nav-pills ', $menu);

		//$app->input->set('hidemainmenu', true) ;

		$script = <<<SCRIPT
		
		<script type="text/javascript">
			window.addEvent('domready', function(){
				a = $$('.controls > ul > li ul a') ;
				a.addEvent('click' , function(e){
					e.stop();
					$('jform_link').set('value', e.target.get('href'));
					$('jform_link').highlight();
				} );
			});
		</script>
		
SCRIPT;

		$app->input->set('hidemainmenu', true);

		echo $script;

		return $menu;
	}
}
