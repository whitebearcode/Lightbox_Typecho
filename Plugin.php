<?php
/**
 * Lightbox图片灯箱效果
 * @package Lightbox
 * @author WhiteBear
 * @version 1.0.0
 * @link https://www.coder-bear.com/
 */
class Lightbox_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('Lightbox_Plugin', 'headcss');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Lightbox_Plugin', 'footjs');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Lightbox_Plugin', 'LightboxArticle');
    }
   
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
	}
   
    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
    
    $alwaysShowNavOnTouchDevices = new Typecho_Widget_Helper_Form_Element_Select('alwaysShowNavOnTouchDevices', array('true' => '显示',  'false' => '不显示'), 'false', '是否显示上一张下一张箭头', '若选择开启则在鼠标悬停时出现上一张和下一张箭头');
    $form->addInput($alwaysShowNavOnTouchDevices->multiMode());

    $albumLabel = new Typecho_Widget_Helper_Form_Element_Text('albumLabel', null,'','图片下方显示文字', '当点击图片时，图片下方的显示，默认为当前第X张，总共X张，可自定义，%1表示第几张图片，%2表示总共几张');
        $form->addInput($albumLabel);
        
     $disableScrolling = new Typecho_Widget_Helper_Form_Element_Select('disableScrolling', array('true' => '是',  'false' => '否'), 'false', '是否在打开灯箱时阻止页面滚动', '若选择是则在打开灯箱时阻止页面滚动');
    $form->addInput($disableScrolling->multiMode());
    
    $fadeDuration = new Typecho_Widget_Helper_Form_Element_Text('fadeDuration', null,'','灯箱容器和叠加层淡入和淡出所花费的时间', '灯箱容器和叠加层淡入和淡出所花费的时间（以毫秒为单位，仅需输入数字）');
        $form->addInput($fadeDuration);
        
     $fitImagesInViewport = new Typecho_Widget_Helper_Form_Element_Select('fitImagesInViewport', array('true' => '是',  'false' => '否'), 'false', '是否自动调整图片大小来适应屏幕', '若选择是则自动调整图片大小来适应屏幕');
    $form->addInput($fitImagesInViewport->multiMode());
    
    $imageFadeDuration = new Typecho_Widget_Helper_Form_Element_Text('imageFadeDuration', null,'','加载后图像淡入所花费的时间', '加载后图像淡入所花费的时间（以毫秒为单位，仅需输入数字）');
        $form->addInput($imageFadeDuration);
        
     $maxWidth = new Typecho_Widget_Helper_Form_Element_Text('maxWidth', null,'','限制图像宽度', '限制图像宽度（以px为单位，仅需输入数字）');
        $form->addInput($maxWidth);
        
        $maxHeight = new Typecho_Widget_Helper_Form_Element_Text('maxHeight', null,'','限制图像高度', '限制图像高度（以px为单位，仅需输入数字）');
        $form->addInput($maxHeight);
        
        $positionFromTop = new Typecho_Widget_Helper_Form_Element_Text('positionFromTop', null,'','打开的灯箱容器距离窗口顶部的距离', '打开的灯箱容器距离窗口顶部的距离（以px为单位，仅需输入数字）');
        $form->addInput($positionFromTop);
        
        $resizeDuration = new Typecho_Widget_Helper_Form_Element_Text('resizeDuration', null,'','不同长宽图片来回切换花费的时间', '不同长宽图片来回切换花费的时间（以毫秒为单位，仅需输入数字）');
        $form->addInput($resizeDuration);
        
        $showImageNumberLabel = new Typecho_Widget_Helper_Form_Element_Select('showImageNumberLabel', array('false' => '隐藏',  'true' => '不隐藏'), 'true', '图片下方是否显示文字', '若选择隐藏则图片下方显示文字项无效');
    $form->addInput($showImageNumberLabel->multiMode());
    
    $wrapAround = new Typecho_Widget_Helper_Form_Element_Select('wrapAround', array('true' => '是',  'false' => '否'), 'true', '到达最后一张图片时是否显示点击后回到第一张的箭头', '若选择是则到达最后一张图片时显示点击后回到第一张的箭头');
    $form->addInput($wrapAround->multiMode());
	}


    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}   
   
    /**
     * 替换文章中图片链接
     *
     * @access public
     * @param string $content
     * @return void
     */
    public static function LightboxArticle($content, $widget, $lastResult) {
        $Archive = Typecho_Widget::widget('Widget_Archive');
    	$content = empty($lastResult) ? $content : $lastResult;
    
    	$pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
    $replacement = '<a href="$1" class="lightbox-image-link" data-lightbox="lightbox-set" data-title="'.$context->title.'"><img class="lightbox-image" src="$1" alt="'.$content->title.'" title="点击放大图片"></a>';
    $content = preg_replace($pattern, $replacement, $content);
        return $content;
    }
    

    /**
     * 头部插入CSS
     *
     * @access public
     * @param unknown $headcss
     * @return unknown
     */
    public static function headcss($css) {
        $Lightbox_assets = Helper::options()->pluginUrl .'/Lightbox/';
		$css = '<link rel="stylesheet" type="text/css" media="all" href="'.$Lightbox_assets.'assets/css/lightbox.min.css" />';
		echo $css;
    }
    
    /**
     * 底部插入JS
     *
     * @access public
     * @param unknown $footjs
     * @return unknown
     */
    public static function footjs($js) {
        $Options = Typecho_Widget::widget('Widget_Options')->plugin('Lightbox');
        $Lightbox_assets = Helper::options()->pluginUrl .'/Lightbox/';
		$js = '
<script src="'.$Lightbox_assets.'assets/js/lightbox-plus-jquery.min.js"></script>
<script>
		jQuery(document).ready(function(){
    lightbox.option({
    \'alwaysShowNavOnTouchDevices\':'.$Options->alwaysShowNavOnTouchDevices.',
      \'albumLabel\': "'.$Options->albumLabel.'",
\'disableScrolling\': '.$Options->disableScrolling.',
      \'fadeDuration\': "'.$Options->fadeDuration.'",
      \'fitImagesInViewport\': '.$Options->fitImagesInViewport.',
      \'imageFadeDuration\': "'.$Options->imageFadeDuration.'",
      \'maxWidth\': "'.$Options->maxWidth.'",
      \'maxHeight\': "'.$Options->maxHeight.'",
      \'positionFromTop\': "'.$Options->positionFromTop.'",
      \'resizeDuration\': "'.$Options->resizeDuration.'",
      \'showImageNumberLabel\': '.$Options->showImageNumberLabel.',
      \'wrapAround\': '.$Options->wrapAround.',
    })
    })
</script>

';
		
		echo $js;
    }
}