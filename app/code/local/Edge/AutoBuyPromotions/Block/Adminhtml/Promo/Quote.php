<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Promo_Quote extends Mage_Adminhtml_Block_Promo_Quote
{
    public function __construct()
    {
        parent::__construct();

        if ($this->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {
            $this->_headerText = Mage::helper('salesrule')->__('Auto Buy Price Rules');
        }
    }
}