<?php
class action_hardware {

    public function run($action) {

        switch ($action) {
            case 'getDeviceHardware' :
                $this->_getDeviceHardware();
                break;
            default :
                return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    protected function _getDeviceHardware() {
        if (!isset($_GET['device_id'])) {
            throw new Exception('employee_id is not set');
        }

        $device = new Device();
        $device->load($_GET['device_id']);
        $data = $device->getFullList();

        $json = json_encode(array(
                'status' => 'success',
                'data'   => $data,
                'error'  => array(),
            ), JSON_UNESCAPED_UNICODE
        );

        echo $json;
    }
}