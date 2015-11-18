<?php

class Edge_AutoBuyPromotions_Model_Adminhtml_Observer
{
    public function addFieldToForm(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {

            $form = $observer->getForm();
            $fieldset = $form->getElement('base_fieldset');
            $model = Mage::registry('current_promo_quote_rule');

            $fieldset->removeField('coupon_type');
            $fieldset->removeField('coupon_code');
            $fieldset->removeField('use_auto_generation');
            $fieldset->removeField('uses_per_coupon');

            $model->setIsAutoBuyPromotion(true);
            $fieldset->addField('is_auto_buy_promotion', 'hidden', array(
                'name'  => 'is_auto_buy_promotion',
                'value' => true
            ));

            $form->setValues($model->getData());
        }
    }

    public function removeActionsFieldset(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {

            $form = $observer->getForm();
            $form->getElements()->remove('actions_fieldset');
        }
    }

    public function addProductIdsToRequest(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {

            $request = $observer->getRequest();
            $post = $request->getPost();

            if (isset($post['autobuyproduct_ids'])) {
                $productIds = $post['autobuyproduct_ids'];
                if ($productIds !== "") {
                    $productIds = explode('&', $productIds);
                    if (!empty($productIds)) {
                        $post['products'] = $productIds;
                    }
                }
            } elseif (isset($post['rule_id'])) {
                $post['products'] = Mage::getModel('salesrule/rule')
                    ->load($post['rule_id'])
                    ->getProductId();
            }

            if (isset($post['autobuytriggerproduct_ids'])) {
                $triggerProductIds = $post['autobuytriggerproduct_ids'];
                if ($triggerProductIds !== "") {
                    $triggerProductIds = explode('&', $triggerProductIds);
                    if (!empty($triggerProductIds)) {
                        $post['trigger_products'] = $triggerProductIds;
                    }
                }
            } elseif (isset($post['rule_id'])) {
                $post['trigger_products'] = Mage::getModel('salesrule/rule')
                    ->load($post['rule_id'])
                    ->getTriggerProductId();
            }

            $post['rule']['conditions'] = array('1' => array(
                'type'          => 'salesrule/rule_condition_combine',
                'aggregator'    => 'all',
                'value'         => '1',
                'new_child'     => null
            ));
            if ($post['keywords'] !== "" || !empty($post['categorys'])) {
                $post['rule']['conditions']['1--1'] = array(
                    'type'          => 'salesrule/rule_condition_product_subselect',
                    'attribute'     => 'qty',
                    'operator'      => '>=',
                    'value'         => '1',
                    'aggregator'    => 'all',
                    'new_child'     => null
                );

                $this->_createConditionsFromFilters($post);
            }
            elseif (isset($post['trigger_products'])) {
                $post['rule']['conditions']['1--1'] = array(
                    'type'          => 'salesrule/rule_condition_product_subselect',
                    'attribute'     => 'qty',
                    'operator'      => '>=',
                    'value'         => '1',
                    'aggregator'    => 'all',
                    'new_child'     => null
                );

                $conditionsSkus = array();
                foreach ($post['trigger_products'] as $triggerProductId) {
                    $conditionsSkus[] = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($triggerProductId)
                        ->getSku();
                }
                $conditionSkusCsv = implode(', ', $conditionsSkus);
                $post['rule']['conditions']['1--1--1'] = array(
                    'type'          => 'salesrule/rule_condition_product',
                    'attribute'     => 'sku',
                    'operator'      => '()',
                    'value'         => $conditionSkusCsv
                );
            }

            $post['rule']['actions'] = array('1' => array(
                'type'          => 'salesrule/rule_condition_product_combine',
                'aggregator'    => 'all',
                'value'         => '1',
                'new_child'     => null
            ));
            if (isset($post['products'])) {
                $actionsSkus = array();
                foreach ($post['products'] as $productId) {
                    $actionsSkus[] = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($productId)
                        ->getSku();
                }
                $actionSkusCsv = implode(', ', $actionsSkus);
                $post['rule']['actions']['1--1'] = array(
                    'type'          => 'salesrule/rule_condition_product',
                    'attribute'     => 'sku',
                    'operator'      => '()',
                    'value'         => $actionSkusCsv
                );
            }

            $request->setPost($post);
        }
    }

    protected function _createConditionsFromFilters(&$post)
    {
        $key = 1;

        if ($post['keywords'] !== "") {
            $post['rule']['conditions']["1--1--{$key}"] = array(
                'type'          => 'autobuypromotions/rule_condition_keyword',
                'attribute'     => 'keyword',
                'operator'      => '==',
                'value'         => $post['keywords']
            );
            $key++;
        }

        if (!empty($post['categorys'])) {
            $post['rule']['conditions']["1--1--{$key}"] = array(
                'type'          => 'salesrule/rule_condition_product',
                'attribute'     => 'category_ids',
                'operator'      => '()',
                'value'         => implode(', ', $post['categorys'])
            );
        }
    }
}
