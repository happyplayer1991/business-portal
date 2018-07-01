<?php
class Ves_Themesettings_Block_Adminhtml_System_Config_Form_Field_Product_ProductPreview extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$fieldIdSuffix = strstr($element->getHtmlId(), '_product_preview');
			//Replace the suffix with suffix appropriate for the background color picker in the current options group
		$parentId = str_replace($fieldIdSuffix, '', $element->getHtmlId());

    	$id = time();
		$item = '';

		$item .= '<div id="preview'.$id.'" class="product-editor">';
		// Item
		$item .= '<div class="product-item products-grid">';
		$item .= '<div class="product-block">';

		// Image
		$item .= '<div class="image">';
		// Icon
		$item .= '<div class="icon">';
		$item .= '<span class="onsale"><span>Sale</span></span>';
		$item .= '<span class="new-icon"><span>New</span></span>';
		$item .= '</div>'; // Icon
		//Product Image
		$item .= '<div class="product-image">';
		$item .= '<i class="fa fa-picture-o"></i>';
		$item .= '<a href="#" title="main-image" class="main-image" ><img src=""/></a>';
		$item .= '<a href="#" title="hover-image" class="hover-image" ><img src=""/></a>';
		$item .= '</div>'; // Product Image

		// Quickview
		$item .= '<div class="quickview">';
		$item .= '<i class="fa fa-eye"></i>';
		$item .= '<span>Quickview</span>';
		$item .= '</div>'; // Quickview

		$item .= '</div>'; // Image

		// Product Info
		$item .= '<div class="product-info">';
		// Action
		$item .= '<div class="actions">';
		// Add to cart
		$item .= '<button onclick="javscript:return false;" class="button btn-cart ajx-cart"><i class="fa fa-eye"></i>Addto Cart</button>'; // Add to cart
		// Add to link
		$item .= '<ul class="add-to-links">';
		$item .= '<li class="link-wishlist"><i class="fa fa-heart-o"></i><span>Add To Wishlist</span></li>';
		$item .= '<li class="link-compare"><i class="fa fa-files-o"></i><span>Add To Compare</span></li>';
		$item .= '<li></li>';
		$item .= '</ul>'; // Add to link
		$item .= '</div>'; // Action
		$item .= '</div>';

		// Product Name
		$item .= '<h3 class="product-name">Classic Hardshell Suitcase</h3>';

		// Product Price
		$item .= '<div class="price-box">';
		$item .= '<p class="old-price">
                <span class="price-label">Regular Price:</span><span class="price" id="old-price-6">$200.00</span></p>';
        $item .= '<p class="special-price"><span class="price-label">Special Price</span><span class="price" id="product-price-6">$150.00</span></p>';
		$item.= '</div>'; // Product Price

		// Ratings
		$item .= '<div class="ratings">';
		$item .= '<div class="rating-box"><div class="rating" style="width:90%"></div></div>';
		$item .= '<p class="rating-links">
            <a href="#">5 Review(s)</a><span class="separator">|</span><a href="#">Add Your Review</a></p>';
		$item .= '</div>'; // Ratings

		// Short Description
		$item .= '<div class="desc std">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla</div>';

		$item.= '</div>';
		$item.= '</div>'; // Item
		$item.= '</div>'; // Product Editor

		$script = '';
		$script .= '<script type="text/javascript">';
		$script .= '
		jQuery(function(){
				sidebarFix();
				jQuery(window).scroll(function(){
					sidebarFix();
                });
				function sidebarFix(){
					var parentEditor = jQuery("#'.$parentId.'");
					var previewBlock = jQuery("#preview'.$id.'");
					var productHeight = previewBlock.height();
					var sectionPosition = parentEditor.offset().top;
					var scrollPosition = jQuery(this).scrollTop();
					var table = jQuery("#'.$parentId.' .form-list");
					var tablePosition = table.width()+50;
					previewBlock.css({"left":tablePosition});
					previewBlock.show(1000);
					if(scrollPosition>sectionPosition && scrollPosition+productHeight<(sectionPosition+parentEditor.height())){
						previewBlock.css({"top":scrollPosition-sectionPosition+30,"bottom":"inherit"});
					}
					if((scrollPosition+productHeight)<sectionPosition){
						previewBlock.css({"top": 0,"bottom":"inherit"});
					}
					if(scrollPosition+productHeight>(sectionPosition+parentEditor.height())){
						previewBlock.css({"top": "inherit","bottom":0});
					}
				}
		});
		';
		$script .= '</script>';
        $useContainerId = $element->getData('use_container_id');
        return sprintf('<tr class="system-fieldset-sub-head fieldset-hidden" id="row_%s"><td colspan="5">%s</td></tr>',
            $element->getHtmlId(), $item.$script
        );
    }
}