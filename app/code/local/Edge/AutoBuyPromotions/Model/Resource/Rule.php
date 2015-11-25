<?php

class Edge_AutoBuyPromotions_Model_Resource_Rule extends Mage_SalesRule_Model_Resource_Rule
{
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveFilter($object, 'product', 'autobuypromotions', 'product');
        $this->_saveFilter($object, 'category');
        $this->_saveFilter($object, 'trigger_product', 'product');

        return parent::_afterSave($object);
    }

    protected function _saveFilter($object, $type, $tableName=null, $columnName=null)
    {
        if (!$object->has("{$type}s")) {
            return;
        }
        
        $old = $this->lookupFilterIds($object->getId(), $type, $tableName, $columnName);
        $new = (array)$object->getData("{$type}s");

        $columnId = ($columnName ? $columnName : ($tableName ? $tableName : $type));

        $table = $this->getTable('autobuypromotions/' . ($tableName ? $tableName : $type));

        $insert = array_diff($new, $old);
        $delete = array_diff($old, $new);

        if ($delete) {
            $where = array(
                "rule_id = ?" => (int) $object->getId(),
                "{$columnId}_id IN (?)" => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();
            foreach ($insert as $id) {
                $data[] = array(
                    "rule_id" => (int) $object->getId(),
                    "{$columnId}_id" => (int) $id
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $this->_loadFilter($object, 'product', 'autobuypromotions', 'product');
        $this->_loadFilter($object, 'category');
        $this->_loadFilter($object, 'trigger_product', 'product');

        return parent::_afterLoad($object);
    }

    protected function _loadFilter($object, $type, $tableName=null, $columnName=null)
    {
        $ids = $this->lookupFilterIds($object->getId(), $type, $tableName, $columnName);
        $object->setData("{$type}_id", $ids);
    }

    public function lookupFilterIds($id, $type, $tableName=null, $columnName=null)
    {
        $adapter = $this->_getReadAdapter();

        $columnId = ($columnName ? $columnName : ($tableName ? $tableName : $type));
        $select  = $adapter->select()
            ->from($this->getTable('autobuypromotions/' . ($tableName ? $tableName : $type)), "{$columnId}_id")
            ->where("rule_id = ?",(int)$id);

        return $adapter->fetchCol($select);
    }
}
