<?php

class Edge_AutoBuyPromotions_Model_Rule_Condition_Keyword extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'keyword' => Mage::helper('autobuypromotions')->__('Product Name')
        ));
        return $this;
    }

    public function getDefaultOperatorOptions()
    {
        return array(
            '==' => Mage::helper('autobuypromotions')->__('Matches')
        );
    }

    public function validate(Varien_Object $object)
    {
        if (!($object instanceof Mage_Sales_Model_Quote_Item)) {
            return false;
        }
        if ($this->getValue() && stristr($object->getName(), $this->getValue())) {
            return true;
        }
        return false;
    }
}