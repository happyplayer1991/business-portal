<?php
class Ves_Themesettings_Block_Html_Head extends Mage_Page_Block_Html_Head{

	protected $_isVenusTheme;

	protected function _construct()
	{
		parent::_construct();
		$ves_theme = Mage::helper('themesettings/theme');
        $package_name = Mage::getSingleton('core/design_package')->getPackageName();
        $theme = Mage::getSingleton('core/design_package')->getTheme('template');
		$this->_isVenusTheme = $ves_theme->isVenusTheme($package_name,$theme);
	}

	/**
     * Get HEAD HTML with CSS/JS/RSS definitions
     * (actually it also renders other elements, TODO: fix it up or rename this method)
     *
     * @return string
     */
	public function getCssJsHtml()
	{
        $isVenusTheme = $this->_isVenusTheme;
        if(!$isVenusTheme) //check if not venus theme return parent getCssJsHtml 
            return parent::getCssJsHtml();

        //venustheme compress js,css
		$ves = Mage::helper('themesettings');
		$compress_css_type = $ves->getConfig("compression/compress_css_type");
		$compress_js_type = $ves->getConfig("compression/compress_js_type");
		
		$js_excludes = $css_excludes = $excludeItems = array();

		if($compress_js_type !='') {
			$exclude_js_files = $ves->getConfig("compression/exclude_js_files");
			$exclude_js_files = trim($exclude_js_files);
			$js_excludes = explode( ",", $exclude_js_files );
			if(!empty($js_excludes)) {
				foreach($js_excludes as $k=>$item) {
					$js_excludes[$k] = trim($item);
				}
				if(count($js_excludes)==0){
					$js_excludes[0]	 = $exclude_js_files;
				}
			}
		}

		if($compress_css_type != '') {
			$exclude_css_files = $ves->getConfig("compression/exclude_css_files");
			$exclude_css_files = trim($exclude_css_files);
			$excludes = explode( ",", $exclude_css_files );
			if(!empty($excludes)) {
				foreach($excludes as $k=>$item) {
					$css_excludes[$k] = trim($item);
				}
				if(count($css_excludes)==0){
					$css_excludes[0]	 = $exclude_css_files;
				}
			}
		}
		
        // separate items by types
		$lines  = array();
		foreach ($this->_data['items'] as $item) {
			if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
				continue;
			}
			$if     = !empty($item['if']) ? $item['if'] : '';
			$params = !empty($item['params']) ? $item['params'] : '';
			switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                // Ves Compression
                if($isVenusTheme && count($js_excludes)>0 && in_array($item['name'], $js_excludes) && $compress_js_type!=''){
                	$excludeItems[$if][$item['type']][$params][$item['name']] = $item['name'];
                	unset($lines[$if][$item['type']][$params][$item['name']]);
                }
                break;
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                // Ves Compression
                if($isVenusTheme && count($css_excludes)>0 && in_array($item['name'], $css_excludes) && $compress_css_type!=''){
                	$excludeItems[$if][$item['type']][$params][$item['name']] = $item['name'];
                	unset($lines[$if][$item['type']][$params][$item['name']]);
                }
                break;
                default:
                $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');

        // Override
        if($isVenusTheme){
        	$shouldMergeJs = $compress_js_type;
        	$shouldMergeCss = $compress_css_type;
        }

        $html   = '';
        foreach ($lines as $if => $items) {
        	if (empty($items)) {
        		continue;
        	}
        	if (!empty($if)) {
                // open !IE conditional using raw value
        		if (strpos($if, "><!-->") !== false) {
        			$html .= $if . "\n";
        		} else {
        			$html .= '<!--[if '.$if.']>' . "\n";
        		}
        	}
            // static and skin css
        	$html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
        		empty($items['js_css']) ? array() : $items['js_css'],
        		empty($items['skin_css']) ? array() : $items['skin_css'],
        		$shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
        		);

            // static and skin javascripts
        	$html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
        		empty($items['js']) ? array() : $items['js'],
        		empty($items['skin_js']) ? array() : $items['skin_js'],
        		$shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
        		);

            // other stuff
        	if (!empty($items['other'])) {
        		$html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
        	}

        	if (!empty($if)) {
                // close !IE conditional comments correctly
        		if (strpos($if, "><!-->") !== false) {
        			$html .= '<!--<![endif]-->' . "\n";
        		} else {
        			$html .= '<![endif]-->' . "\n";
        		}
        	}
        }

        if($isVenusTheme && is_array($excludeItems) && count($excludeItems) > 0){
        // Vess Compression
        	$shouldMergeJs = false;
        	$shouldMergeCss = false;
        	foreach ($excludeItems as $if => $items) {
        		if (empty($items)) {
        			continue;
        		}
        		if (!empty($if)) {
                // open !IE conditional using raw value
        			if (strpos($if, "><!-->") !== false) {
        				$html .= $if . "\n";
        			} else {
        				$html .= '<!--[if '.$if.']>' . "\n";
        			}
        		}

            // static and skin css
        		$html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
        			empty($items['js_css']) ? array() : $items['js_css'],
        			empty($items['skin_css']) ? array() : $items['skin_css'],
        			$shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
        			);

            // static and skin javascripts
        		$html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
        			empty($items['js']) ? array() : $items['js'],
        			empty($items['skin_js']) ? array() : $items['skin_js'],
        			$shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
        			);

            // other stuff
        		if (!empty($items['other'])) {
        			$html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
        		}

        		if (!empty($if)) {
                // close !IE conditional comments correctly
        			if (strpos($if, "><!-->") !== false) {
        				$html .= '<!--<![endif]-->' . "\n";
        			} else {
        				$html .= '<![endif]-->' . "\n";
        			}
        		}
        	}
        }
        return $html;
    }
}