<?php
/**
 * @package 	tmpl
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
// No direct access
defined('_JEXEC') or die;

/* @var JDocument $document */
$document = JFactory::getDocument();

if($params->get('title_font') == 'Oswald' ){
    $document->addStyleSheet('http://fonts.googleapis.com/css?family=Oswald');
}
$captionCSS = '#mod_btslideshow_pro_'. $moduleID. ' .label_skitter_background{'.
                ($params->get('caption_background') ? 'background: ' . $params->get('caption_background') . ';' : '') .
                ($params->get('caption_opacity') ? 'opacity: ' . $params->get('caption_opacity') . ';' : '').
               '}';
$document->addStyleDeclaration($captionCSS);
$titleCSS = '#mod_btslideshow_pro_'. $moduleID. ' .label_skitter h4,
            #mod_btslideshow_pro_'. $moduleID. ' .label_skitter h4 a
            {'.
            ($params->get('title_font') ? '   font-family: '.$params->get('title_font') . ';' : '').
            ($params->get('title_size') ? '   font-size: '. $params->get('title_size'). 'px;' : ''). 
            ($params->get('title_color') ? '   color: ' . $params->get('title_color') . ';' : '').
            '}';
$document->addStyleDeclaration($titleCSS);

if($params->get('navigation_type') == 'numbers'){
    $document->addStyleDeclaration('
    #mod_btslideshow_pro_'. $moduleID. '.box_skitter .info_slide .image_number{
        width : 23px;
        height: 23px;
        -moz-border-radius:25px;
        -webkit-border-radius:25px;
        border-radius:25px;
    }
    ');
}
//style for style4
$document->addStyleDeclaration('
    #mod_btslideshow_pro_' . $moduleID . '.box_skitter .next_button{
        bottom:5px; right: 10px;
    }
    #mod_btslideshow_pro_' . $moduleID . '.box_skitter .prev_button{
        bottom:5px; right: 50px;
    }
    '
    .(($params->get('navigation_type') == 'numbers') ?
    '#mod_btslideshow_pro_' . $moduleID . '.box_skitter .info_slide{
        bottom: 5px;
    }' : '')
    . (($params->get('navigation_type') == 'dots') ?
    '#mod_btslideshow_pro_' . $moduleID . '.box_skitter .info_slide_dots{
        bottom: 12px;
    }' : '').
    '#mod_btslideshow_pro_' . $moduleID . '.box_skitter .container_thumbs{
        position: absolute;
        top: 0px;
        left: -'.($params->get('thumb_width')+5) * 2 .'px;
    }
    #mod_btslideshow_pro_' . $moduleID . '.box_skitter .info_slide_thumb{
        padding: 0px !important;
    }
    #mod_btslideshow_pro_' . $moduleID . '.box_skitter {
        margin-left: ' . ($params->get('thumb_width') + 5) * 2 .'px;
    }
    #mod_btslideshow_pro_' . $moduleID . '.box_skitter .label_skitter{
        height: 150px;
    }
    '
        
);
?>

<div style="clear: both;"></div>
<div id="mod_btslideshow_pro_<?php echo $module->id; ?>" class="box_skitter mod_btslideshow_pro<?php echo $params->get('module_class_suffix', '') ?>">
<ul>
<?php
foreach ($items as $item) {
	if (!empty($item->link)) {
		if ($item->target != 'window') {
                        $linkAttr = 'href="' . $item->link . '" target="' . $item->target . '"';
                } else {
                        $linkAttr = 'href="#" onclick="window.open(\'' . $item->link . '\' , \'\', \'type=fullscreen, scrollbars=yes,toolbar=no,menubar=no,status=no\' ); return false;"';
                }
                $img = '<a '.$linkAttr.'>
                            <img class="cubeRandom" src="' . $item->mainImage . '" 
                                rel="' . $item->thumbImage . '"
                                alt="' . $item->title . '" />    
                        </a>';
	}
	else {
		 $img = '<img class="cubeRandom" src="' . $item->mainImage . '" 
                            rel="' . $item->thumbImage . '" 
                            alt= "' . $item->title . '"/>';
	}
	$desc = '';
	if (!empty($item->title)) {
		$desc .= '<h4>';
            if(!empty($item->link)) {
                $desc.= '<a '.$linkAttr.'>' . $item->title . '</a>';
            }else{
                $desc.= $item->title;
            }
            $desc.= '</h4>';
	}
	if (!empty($item->desc)) {
		$desc .= '<p>' . $item->desc . '</p>';
	}
	?>
	<li>
		<?php echo $img; ?>
		<div class="label_text">
                    <div class="label_skitter_container">
                    <?php echo $desc; ?>
                    </div>
                    <div class="label_skitter_background"></div>
                </div>
	</li>
	<?php
}
$options = array();
$options[] = 'width:'.($params->get('width')=='auto'?"'auto'":$params->get('width'));
$options[] = "height:{$params->get('height')}";
$options[] = "animation: '{$params->get('effect')}'";

if ($params->get('navigation_type') == 'thumbs') {
    $options[] = 'thumbs: true';
    $options[] = 'thumb_height: ' . $params->get('thumb_height');
    $options[] = 'thumb_width: ' . $params->get('thumb_width');
    $options[] = 'thumb_cols: 2';
    $options[] = 'navigation_directory: "vertical"';
    $options[] = 'thumb_image_css: {background: "none", padding: "0px:"}';

}
if ($params->get('navigation_type') == 'dots') {
	$options[] = 'dots: true';
        $options[] = 'numbers_align: "right"';
        $options[] = 'numbers_right: "88px"';
}
if ($params->get('navigation_type') == 'numbers') {
    $options[] = 'numbers: true';
    $options[] = 'numbers_align: "right"';
    $options[] = 'numbers_right: "88px"';
} 
if ($params->get('navigation_type') == 'hide') {
	$options[] = 'numbers: false';
} 

if (!$params->get('caption')) {
    $options[] = 'label: false';
} else {
    if($params->get('caption_height') && $params->get('caption_height') != ''){
        $options[] = 'height_label: "' . $params->get('caption_height') .'px"';
    }
    if($params->get('caption_width') && $params->get('caption_width') != ''){
        $options[] = 'width_label: "' . $params->get('caption_width') .'px"';
    }

    $options[] = 'label_position: "left"';
    $options[] = 'label_left: "0px"';
    $options[] = 'label_top: "' . (($params->get('height') - $params->get('caption_height')) / 2) . 'px"';
}
if($params->get('show_progressbar')){
            $options[] = 'progressbar: true';
            $options[] = 'progressbar_css: {background: "'.$params->get('progressbar_color', '#000000').'"}'; 
        }else{
            $options[] = 'progressbar: false';
        }
$options = implode(",\n", $options);


?>
</ul>
</div>
<div style="clear: both;"></div>
<script type="text/javascript">
// <![CDATA[
    <?php
	if ($params->get('navigation_type') == 'thumbs') {
			echo "BTJ('#mod_btslideshow_pro_{$module->id}').css({'margin-bottom':'5px'});\n\t\t";
	}
	?>
	var structure = '<a href="#" class="prev_button">prev</a>' +
						'<a href="#" class="next_button">next</a>' +
						'<span class="info_slide"></span>' +
						'<div class="container_skitter">' +
								'<div class="image">' +
										'<a target="_blank" href=""><img class="image_main" alt=""/></a>' +
										'<div class="label_skitter"></div>' +
								'</div>' +
						'</div>';
	BTJ('#mod_btslideshow_pro_<?php echo $module->id; ?>').skitter({
		<?php echo $options; ?>,
		animateNumberOut: {backgroundColor: '<?php echo $params->get('animateNumberOut', '#565656')?>'},        
		animateNumberOver: {backgroundColor: "<?php echo $params->get('animateNumberOver', '#c36305')?>"},
		animateNumberActive: {backgroundColor: "<?php echo $params->get('animateNumberActive', '#c36305')?>"},
		structure: structure,
		velocity: <?php echo $params->get('effect_velocity', 1.3)?>,
		interval: <?php echo $params->get('time_interval'); ?>,
		navigation: <?php echo $params->get('show_button'); ?>,
		auto_play: <?php echo $params->get('auto_play',1); ?>,
		fullscreen: false,
		max_number_height: 100,
		responsive: <?php echo $params->get('responsive',0); ?>
	});
// ]]>	
</script>