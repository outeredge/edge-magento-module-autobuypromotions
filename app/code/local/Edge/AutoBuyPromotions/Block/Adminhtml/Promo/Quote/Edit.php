<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Promo_Quote_Edit extends Mage_Adminhtml_Block_Promo_Quote_Edit
{
    public function getFormActionUrl()
    {
        if ($this->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {
            return $this->getUrl('*/promo_autoBuyPromotions/save');
        }
        return parent::getFormActionUrl();
    }
}

