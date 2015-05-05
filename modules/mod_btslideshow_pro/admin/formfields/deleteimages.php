<?php
/**
 * @package 	formfields
 * @version	1.1
 * @created	Aug 2011
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldDeleteImages extends JFormField{
    protected $type = 'deleteimages';
    protected function  getInput() {
        $html  = '<button id="btnDeleteAll">'.JText::_("MOD_BTSLIDESHOW_BUTTON_DELETEALL").'</button>';
        return $html;
    }

}