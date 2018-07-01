<?php
class Ves_Themesettings_Block_Adminhtml_System_Config_Form_Field_Gmap extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $element->getElementHtml();
        $isLoadScript = Mage::registry('gmap_loaded');
        $elementId = $element->getHtmlId();
        $elementId = str_replace("_address_preview", "", $elementId);
        $latElementId = $elementId.'_location_lat';
        $lngElementId = $elementId.'_location_lng';
        $addressElementId = $element->getHtmlId();
        if(empty($isLoadScript)){
            // $html .= '<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script><script src="http://js.maxmind.com/app/geoip.js" type="text/javascript"></script><script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=places"></script><script src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'ves/themesettings/locationpicker.jquery.js"></script>';
            $html .= '<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script><script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBJqZi4IJe2FuYuWB0rHmbotwYJhaI7JmA&sensor=false&libraries=places"></script><script src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'ves/themesettings/locationpicker.jquery.js"></script>';
            Mage::register('gmap_loaded',true);
        }
        $html .= '<br/><div id="map-'.$element->getHtmlId().'" style="width:600px;height:400px">';
        $html.= '</div>';
        $html.= '<script type="text/javascript">

        jQuery(window).load(function(){
            jQuery("#map-'.$element->getHtmlId().'").locationpicker({
                location: {latitude: $("'.$latElementId.'").value, longitude: $("'.$lngElementId.'").value},
                radius: 100,
                enableAutocomplete: true,
                inputBinding: {
                    latitudeInput: jQuery("#'.$latElementId.'"),
                    longitudeInput: jQuery("#'.$lngElementId.'"),
                    locationNameInput: jQuery("#'.$addressElementId.'")
                }
            });
});
</script>';
return $html;
}
}