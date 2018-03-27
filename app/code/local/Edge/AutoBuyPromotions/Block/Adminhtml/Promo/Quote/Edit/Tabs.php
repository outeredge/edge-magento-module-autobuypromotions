<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Promo_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $this->addTabAfter('auto_buy_tab', array(
            'label'     => Mage::helper('adminhtml')->__('Auto Buy Products'),
            'url'       => $this->getUrl('*/promo_autoBuyPromotions/productsTab', array('_current' => true)),
            'class'     => 'ajax'
        ), 'labels_section');

        return parent::_beforeToHtml();
    }
}
