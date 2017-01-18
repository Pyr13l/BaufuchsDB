<?php
class action_employee {

    public function run($action) {

        switch ($action) {
            case 'getDevices' :
                $this->_getEmployee();
                break;
            default :
                return false;
        }

        return true;
    }

    /**
     * url/index.php?method=employee&action=getEmployee
     */
    protected function _getEmployee () {
        $employeeList = new ModelList(new Employee());
        $employeeList->setSelectTable($employeeList->getBaseObject()->getTableName());
        $employeeList->execute();

        $data = $employeeList->getArray();
        $json = json_encode(array(
                'status' => 'success',
                'data'   => $data,
                'error'  => array(),
            )
        );

        echo $json;
    }
}