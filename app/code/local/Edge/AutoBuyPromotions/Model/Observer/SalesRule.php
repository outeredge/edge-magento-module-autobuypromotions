<?php

class Edge_AutoBuyPromotions_Model_Observer_SalesRule
{
    public function addKeywordCondition(Varien_Event_Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = (array)$additional->getConditions();
        $additional->setConditions(array_merge_recursive($conditions, array(
            array('value' => 'autobuypromotions/rule_condition_keyword','label' => Mage::helper('autobuypromotions')->__('Keywords'))
        )));
    }
}
