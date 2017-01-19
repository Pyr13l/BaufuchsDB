<?php
class action_hardware_attributes {

    public function run($action) {

        switch ($action) {
            case 'getHardwareAttributes' :
                $this->_getDeviceHardware();
                break;
            default :
                return false;
        }

        return true;
    }

    /**
     * URL: index.php?method=hardware_attributes&action=getHardwareAttributes&hardware_id=<ID>
     */
    protected function _getDeviceHardware() {
        if (!isset($_GET['hardware_id'])) {
            throw new Exception('hardware_id is not set');
        }

        $hardware = new Hardware();
        $hardware->load($_GET['hardware_id']);
        $data = $hardware->getAttributeList();

        $json = json_encode(array(
                'status' => 'success',
                'data'   => $data,
                'error'  => array(),
            )
        );

        echo $json;
    }
}