<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Promo_Quote_Edit_Tab_Autobuy
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('autobuy_');

        $fieldset = $form->addFieldset('autobuy_fieldset', array(
            'legend' => Mage::helper('autobuypromotions')->__('Auto Buy Products'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('is_', 'text', array(
            'label' => Mage::helper('autobuypromotions')->__('Order Subtotal (Excl. VAT)'),
            'name'  => 'order_total'
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
