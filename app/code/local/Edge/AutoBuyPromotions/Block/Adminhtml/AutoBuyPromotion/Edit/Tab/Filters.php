<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tab_Filters extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('autobuypromotions_');

        $fieldset = $form->addFieldset('filters_fieldset', array(
            'legend' => Mage::helper('autobuypromotions')->__('Filters'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('keywords', 'text', array(
            'label' => Mage::helper('autobuypromotions')->__('Keywords'),
            'name'  => 'keywords',
            'after_element_html' => '<span>Matches product name</span>'
        ));

        $categoryFieldset = $form->addFieldset('filters_category_fieldset', array(
            'legend' => Mage::helper('autobuypromotions')->__('Categories'),
            'class'  => 'fieldset-wide'
        ));
        $categoryFieldset->addType(
            'category_tree',
            'Edge_Base_Block_Adminhtml_Form_Renderer_CategoryTree_Element'
        );
        $categoryTree = $categoryFieldset->addField('category_id', 'category_tree', array(
            'field_name' => 'categorys'
        ));
        $categoryTree->setRenderer($this->getLayout()->createBlock('edge/adminhtml_form_renderer_categoryTree_row'));

        $form->setValues($model->getData());
        $this->setForm($form);

        Mage::dispatchEvent('autobuypromotions_edit_tab_filters_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
