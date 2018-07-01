<?php
class Mage_MyBudge_Block_List extends Mage_Core_Block_Template
{
    public function getTitle() {
        return 'My Budge';
    }   

    public function getBudges() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $customer = Mage::getSingleton("customer/session")->getCustomer();
        
        $query = "SELECT budge_id FROM budge_user WHERE customer_id = :customer_id";
        $binds = array(
            'customer_id' => $customer->getId()
        );
        $result = $connection->query( $query, $binds );
        $rows = $result->fetchAll($sql,$array);

        $budge_ids = array();
        foreach($rows as $row):
            array_push($budge_ids, $row['budge_id']);
        endforeach; 

        $budge_array = array();
        foreach ($budge_ids as $id):
            $budge = Mage::getModel("budge/budge")->load($id);
            array_push($budge_array, $budge);
        endforeach;

        return $budge_array;
    }

} 