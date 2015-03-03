<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Edit_Tabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $this->addTabAfter('auto_buy_promotions_products', array(
            'label'     => Mage::helper('adminhtml')->__('Auto Buy Promotions'),
            'url'       => $this->getUrl('*/autoBuyPromotions/productsTab', array('_current' => true)),
            'class'     => 'ajax'
        ), 'coupons_section');

        return parent::_beforeToHtml();
    }
}
