<?php
/**
 * @package 	formfields
 * @version	1.1.3
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

class JFormFieldNavigation extends JFormField {

    protected $type = 'navigation';

    public function getInput() {
        $value = $this->value;
        $modulePath = JPATH_ROOT . '/modules/mod_btslideshow_pro';
        $configPath = $modulePath . '/tmpl/configs/config.xml';
        $options = array();
        $screenshots = array();
        @$sxml = simplexml_load_file($configPath);
        if (isset($sxml) && $sxml) {
            foreach ($sxml->layout as $layout) {

                foreach ($layout->navigation->option as $option) {
                    $objOption = new stdClass();
                    $objOption->class = (string) $layout->attributes()->name;
                    $objOption->value = (string) $option->attributes()->value;
                    $objOption->text = (string) $option;
                    $options[] = $objOption;
                }
                
                $screenshots[(string)$layout->attributes()->name] = (string) $layout->screenshot;
            }
        }
        //prepare js object for screenshots.
        $jsScreenShotParams = '';
        foreach($screenshots as $layoutname => $img){
            $jsScreenShotParams.= "'".$layoutname."'" . ':"' .$img . '",';
        }
        $jsScreenShotParams = substr($jsScreenShotParams, 0, strlen($jsScreenShotParams) -1);
        
        $html = '<select id="' . $this->id . '" name="' . $this->name . '">';
        foreach ($options as $option) {
            $html.= '<option class="' . $option->class . '" value="' . $option->value . '">' . $option->text . '</option>';
        }
        $html .='</select>';
        $html .='
        <script type="text/javascript">
            var jQ = jQuery.noConflict();
            jQ(document).ready(function(){
                var screenshots = {' . $jsScreenShotParams. '};
                var layout = "";    
                jQ("#jform_params_layout option").each(function(){
                    layout = jQ(this).attr("value").replace("_:","");
                    jQ("#layout-demo a."+ layout).attr("href", screenshots[layout]);
                });
                layout = jQ("#jform_params_layout").val().replace("_:","");
                jQ("#layout-demo a."+layout).show();
                
                var options = jQ("#jform_params_navigation_type option");
                for(var i = 0; i< options.length ; i++){
                    if(!jQ(options[i]).hasClass(layout)) jQ(options[i]).remove();
                    else{
                        if(jQ(options[i]).attr("value") == "' . $value. '") jQ(options[i]).attr("selected", "selected");
                    }
                }
                jQ("#jform_params_navigation_type").trigger("liszt:updated");
                
                jQ("#jform_params_layout").change(function(){ 
                    layout = jQ("#jform_params_layout").val().replace("_:","");
                    jQ("#layout-demo a").hide();
                    jQ("#layout-demo a."+layout).show();
                    jQ("#jform_params_navigation_type option").each(function(){
                        jQ(this).remove();
                    });
                    
                    for(var i = 0; i< options.length ; i++){
                        if(jQ(options[i]).hasClass(layout)) {
                            jQ("#jform_params_navigation_type").append(jQ(options[i])); 
                            jQ("#jform_params_navigation_type option").eq(0).attr("selected", "selected");
                        }
                    }
                    jQ("#jform_params_navigation_type").trigger("liszt:updated");
                });
                
            });
            
        </script>
        ';
        return $html;
    }

}

?>
