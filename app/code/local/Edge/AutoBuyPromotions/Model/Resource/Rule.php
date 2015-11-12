<?php

class Edge_AutoBuyPromotions_Model_Resource_Rule extends Mage_SalesRule_Model_Resource_Rule
{
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveProductIds($object);
        return parent::_afterSave($object);
    }

    protected function _saveProductIds($object)
    {
        $old = $this->lookupProductIds($object->getId());
        $new = (array)$object->getData("products");

        $table = $this->getTable('autobuypromotions/autobuypromotions');

        $insert = array_diff($new, $old);
        $delete = array_diff($old, $new);

        if ($delete) {
            $where = array(
                "rule_id = ?" => (int) $object->getId(),
                "product_id IN (?)" => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();
            foreach ($insert as $productId) {
                $data[] = array(
                    "rule_id" => (int) $object->getId(),
                    "product_id" => (int) $productId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('product_id', (array)$this->lookupProductIds($object->getId()));
        parent::_afterLoad($object);
        return $this;
    }

    public function lookupProductIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('autobuypromotions/autobuypromotions'), "product_id")
            ->where("rule_id = ?",(int)$id);

        return $adapter->fetchCol($select);
    }
}
