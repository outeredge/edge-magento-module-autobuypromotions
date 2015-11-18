<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $this->addTabAfter('trigger_filters', array(
            'label'     => Mage::helper('adminhtml')->__('Trigger Products By Filters'),
            'content'   => $this->getLayout()->createBlock('autobuypromotions/adminhtml_autoBuyPromotion_edit_tab_filters')->toHtml()
        ), 'main_section');

        $this->addTabAfter('trigger_products', array(
            'label'     => Mage::helper('adminhtml')->__('Trigger Products by Selection'),
            'url'       => $this->getUrl('*/*/triggerProductsTab', array('_current' => true)),
            'class'     => 'ajax'
        ), 'main_section');

        $this->addTabAfter('products', array(
            'label'     => Mage::helper('adminhtml')->__('Auto Buy Products'),
            'url'       => $this->getUrl('*/*/productsTab', array('_current' => true)),
            'class'     => 'ajax'
        ), 'labels_section');

        return parent::_beforeToHtml();
    }
}
