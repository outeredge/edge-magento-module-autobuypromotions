<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $this->addTabAfter('products', array(
            'label'     => Mage::helper('adminhtml')->__('Auto Buy Products'),
            'url'       => $this->getUrl('*/*/productsTab', array('_current' => true)),
            'class'     => 'ajax'
        ), 'labels_section');

        return parent::_beforeToHtml();
    }
}
