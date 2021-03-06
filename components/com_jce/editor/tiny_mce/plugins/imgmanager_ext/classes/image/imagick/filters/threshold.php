<?php

/**
 * @package   	JCE
 * @copyright 	Copyright (c) 2009-2014 Ryan Demmer. All rights reserved.
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * Based on JImage library from Joomla.Platform 11.3
 */
defined('_JEXEC') or die;

/**
 * Image Filter class adjust the smoothness of an image.
 */
class WFImageImagickFilterThreshold extends WFImageImagickFilter {

    /**
     * Method to apply a filter to an image resource.
     *
     * @param   array  $options  An array of options for the filter.
     *
     * @return  void
     * 
     * @throws  InvalidArgumentException
     * @throws  RuntimeException
     */
    public function execute(array $options = array()) {
        if (empty($options)) {
            throw new InvalidArgumentException('No valid amount was given.  Expected integer.');
        }
        $value  = (int) array_shift($options);
        // get hex value of amount
        $hex    = dechex($value);
        // create hex color, eg: #808080
        $color  = '#' . str_repeat($hex, 3);
        
        $this->handle->blackthresholdImage($color);
        $this->handle->whitethresholdImage($color);
    }
}
