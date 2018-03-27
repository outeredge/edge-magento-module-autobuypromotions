<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tab_OrderFilters
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('autobuypromotions_');

        $fieldset = $form->addFieldset('order_filters_fieldset', array(
            'legend' => Mage::helper('autobuypromotions')->__('Order Filters'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('order_total', 'text', array(
            'label' => Mage::helper('autobuypromotions')->__('Order Subtotal (Excl. VAT)'),
            'name'  => 'order_total'
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
