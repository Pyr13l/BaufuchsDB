<?php
class action_device {

    public function run($action) {

        switch ($action) {
            case 'getDevices' :
                $this->_getDevices();
                break;
            case 'getEmployeeDevices' :
                $this->_getEmployeeDevices();
                break;
            default :
                return false;
        }

        return true;
    }

    /**
     * url/index.php?method=device&action=getDevices
     */
    protected function _getDevices () {
        $deviceList = new ModelList(new Device());
        $deviceList->setSelectTable($deviceList->getBaseObject()->getTableName());
        $deviceList->execute();

        $data = $this->__buildDeviceList($deviceList);

        $json = json_encode(array(
                'status' => 'success',
                'data'   => $data,
                'error'  => array(),
            )
        );

        echo $json;
    }

    /**
     * url/index.php?method=device&action=getEmployeeDevices&employee_id=<ID>
     */
    protected function _getEmployeeDevices () {
        if (!isset($_GET['employee_id'])) {
            throw new Exception('employee_id is not set');
        }

        /*
         * Device Liste anhand der User Id
         */
        $sql = 'select device_id as id from employee2devices where employee_id = ?';
        $deviceList = new ModelList(new Device());
        $deviceList->selectString($sql, array($_GET['employee_id']));

        /**
         * Liste aufbauen
         */
        $data = $this->__buildDeviceList($deviceList);

        $json = json_encode(array(
                'status' => 'success',
                'data'   => $data,
                'error'  => array(),
            )
        );

        echo $json;
    }

    /**
     * Baut anhand einer ModelList (Device) ein Array auf
     * @param $deviceList
     * @return array
     */
    protected function __buildDeviceList ($deviceList) {
        $data = array();
        foreach ($deviceList as $id => $device) {
            $item = $device->getSimpleFieldList();

            /**
             * Zugehörigen Mitarbeiter laden
             */
            $employee = new Employee();
            $employee->load($device->getDeviceEmployee());
            $item['employee'] = $employee->getFieldValue('firstname') . ' ' . $employee->getFieldValue('lastname');

            /**
             * Standort Namen
             */
            $location = new Location();
            $location->load($device->getFieldValue('location_id'));
            $item['location'] = utf8_encode($location->getFieldValue('name')) ;

            /**
             * Hardware mit Typ Namen holen
             */
            //$item['hardware'] = $value->getFullList();
            $item['hardware'] = $device->getHardwareTypeList();

            unset($item['location_id']);

            $data[] = $item;
        }

        return $data;
    }
}