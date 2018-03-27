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
            $request->setPost($post);
        } elseif (isset($post['rule_id'])) {
            $post['products'] = Mage::getModel('salesrule/rule')
                ->load($post['rule_id'])
                ->getProductId();
            $request->setPost($post);
        }
            
        if (Mage::app()->getRequest()->getControllerModule() === Mage::helper('autobuypromotions')->getControllerModule()) {

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

            if ($post['keywords'] !== "" || !empty($post['brands']) || !empty($post['categorys'])) {
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
            
            if ($post['order_total'] !== "" && $post['order_total'] > 0) {
                $totalConditionKey = isset($post['rule']['conditions']['1--1']) ? '1--2' : '1--1';
                $post['rule']['conditions'][$totalConditionKey] = array(
                    'type'      => 'salesrule/rule_condition_address',
                    'attribute' => 'base_subtotal',
                    'operator'  => '>=',
                    'value'     => $post['order_total']
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
                if ($post['order_total'] !== "" && $post['order_total'] > 0) {
                    $post['rule']['actions']['1--2'] = array(
                        'type'          => 'salesrule/rule_condition_product',
                        'attribute'     => 'quote_item_auto_buy_promotion_rule',
                        'operator'      => '==',
                        'value'         => isset($post['rule_id']) ? $post['rule_id'] : '0'
                    );
                    if (!isset($post['rule_id'])) {
                        $post['save_actions_rule_id_after'] = $post['rule']['actions'];
                    }
                } else {
                    $post['rule']['actions']['1--2'] = array(
                        'type'          => 'salesrule/rule_condition_product',
                        'attribute'     => 'quote_item_auto_buy_promotion_link',
                        'operator'      => '>=',
                        'value'         => '1'
                    );
                }
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
            $key++;
        }

        if (!empty($post['brands'])) {
            $post['rule']['conditions']["1--1--{$key}"] = array(
                'type'          => 'salesrule/rule_condition_product_combine',
                'aggregator'    => 'any',
                'value'         => '1',
                'new_child'     => null
            );

            $brandKey = 1;
            foreach ($post['brands'] as $brandId) {
                $post['rule']['conditions']["1--1--{$key}--{$brandKey}"] = array(
                    'type'          => 'salesrule/rule_condition_product',
                    'attribute'     => 'brand_id',
                    'operator'      => '==',
                    'value'         => $brandId
                );
                $brandKey++;
            }
        }
    }
    
    public function addBrandsToFilters($observer)
    {
        $model = Mage::registry('current_promo_quote_rule');
        $form = $observer->getForm();
        $fieldset = $form->getElement('filters_fieldset');

        $brands = array();
        $brandCollection = Mage::getModel('brand/brand')
            ->getCollection();
        foreach ($brandCollection as $brand) {
            $brands[] = array(
                'value' => $brand->getId(),
                'label' => $brand->getName()
            );
        }
        $fieldset->addField('brand_id', 'multiselect', array(
            'label'     => Mage::helper('autobuypromotions')->__('Brands'),
            'name'      => 'brands[]',
            'values'    => $brands
        ));

        $form->setValues($model->getData());
    }
}
