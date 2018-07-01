<?php

class Ves_Brand_Model_System_Config_Source_ListGroup
{

    public function toOptionArray() {
        $Collection = Mage::getModel('ves_brand/group')->getCollection();;
        $arr = array(array("value" => "0", "label" => Mage::helper("ves_brand")->__("-- Select a brand group --")));
        foreach($Collection as $cat) {
            $arr[] = array("value" => $cat->getId(), "label" =>$cat->getName());
        }
        return $arr;
    }
}