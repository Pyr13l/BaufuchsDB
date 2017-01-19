<?php

class Device extends Model {

    protected $coreTable = 'devices';

    public function getFullList () {
        $sql = 'select id from hardware where device_id = ?';

        $list = new ModelList(new Hardware());
        $list->selectString($sql, array($this->getId()));

        $hardwareList = array();
        foreach ($list as $id => $hwItem) {
            $newHwItem = $hwItem->getSimpleFieldList();

            /*
             * Hersteller laden
             */
            $manufacturer = new Manufacturer();
            $manufacturer->load($newHwItem['manufacturer_id']);
            $newHwItem['manufacturer'] = $manufacturer->getFieldValue('name');

            unset($newHwItem['manufacturer_id']);


            /*
             * Hardware Type Objekt laden
             */
            $hardwareType = new Hardware_Type();
            $hardwareType->load($newHwItem['type']);
            $newHwItem['type'] = $hardwareType->getFieldValue('type');

            $newHwItem['attributes'] = $hwItem->getAttributeList();

            $hardwareList[] = $newHwItem;
        }

        return $hardwareList;
    }

    public function getHardwareTypeList () {
        $sql = 'select id from hardware where device_id = ?';

        $list = new ModelList(new Hardware());
        $list->selectString($sql, array($this->getId()));

        $hardwareList = array();
        foreach ($list as $id => $hwItem) {
            /*
             * Hardware Type Objekt laden
             */
            $hardwareType = new Hardware_Type();
            $hardwareType->load($hwItem->getFieldValue('type'));
            $newHwItem['type'] = $hardwareType->getFieldValue('type');

            $hardwareList[] = $newHwItem;
        }

        return $hardwareList;
    }

    public function getDeviceEmployee ($id = null) {
        if ($id === null) {
            $id = $this->getId();
        }

        $sql = 'select employee_id from employee2devices where device_id = ?';

        $db = Database::getDB(PDO::FETCH_BOTH);
        $result = $db->getRow($sql, array($id));
        return $result[0];
    }

    public function getLicenseList($id = null) {
        if ($id === null) {
            $id = $this->getId();
        }

        $list = new ModelList(new License());
        $list->setSelectTable($list->getBaseObject()->getTableName());
        $list->addWhereParameter('device_id', $id);
    }
}