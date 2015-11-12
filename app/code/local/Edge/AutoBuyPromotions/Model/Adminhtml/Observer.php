<?php

class Edge_AutoBuyPromotions_Model_Adminhtml_Observer
{
    public function addFieldToForm(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getRequest()->getControllerModule() === 'Edge_AutoBuyPromotions_Adminhtml') {

            $form = $observer->getForm();
            $fieldset = $form->getElement('base_fieldset');

            $fieldset->removeField('coupon_type');
            $fieldset->removeField('coupon_code');
            $fieldset->removeField('use_auto_generation');
            $fieldset->removeField('uses_per_coupon');

            $fieldset->addField('is_auto_buy_promotion', 'hidden', array(
                'name'  => 'is_auto_buy_promotion',
                'value' => true
            ));
        }
    }

    public function addProductIdsToRequest(Varien_Event_Observer $observer)
    {
        $request = $observer->getRequest();

        $post = $request->getPost();
        if (isset($post['product_ids'])) {
            $productIds = $request->getPost('product_ids');
            if ($productIds !== "") {
                $productIds = explode('&', $productIds);
                if (!empty($productIds)) {
                    $post['products'] = $productIds;
                }
            }
        } elseif (isset($post['id'])) {
            $post['products'] = Mage::getModel('salesrule/rule')
                ->load($post['id'])
                ->getProductId();
        }

        $request->setPost($post);
    }
}
