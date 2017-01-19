<?php
class BaseControl {

    protected $_validActions = array(
        'employee' => array(
            'getEmployee' => true,
            'addEmployee' => true,
            'updateEmployee' => true,
        ),
        'device' => array(
            'getDevices' => true,
            'getEmployeeDevices' => true,
        ),
        'hardware' => array(
            'getDeviceHardware' => true,
        ),
        'hardware_attributes' => array(
            'getHardwareAttributes' => true,
        ),
    );

    public function start() {
        $this->_loadClassLoader();
        $this->_initDatabase();
    }

    public function stop() {

    }

    protected function _loadClassLoader () {
        spl_autoload_register('interface_classloader');
    }

    protected function _initDatabase() {
        $driver     = 'mysql';
        $hostname   = 'localhost';
        $username   = 'root';
        $password   = '';
        $database   = 'Schule';
        $prefix     = '';

        new Database($hostname, $database, $username, $password, $driver, $prefix);
    }

    /**
     * @param $method
     * @param $action
     * @throws Exception
     */
    public function runAction ($method, $action) {
        if (isset($this->_validActions[$method]) === false) {
            throw new Exception ('Fehler! Die Methode ' . $method . ' ist nicht gültig');
        }

        if (isset($this->_validActions[$method][$action]) === false || $this->_validActions[$method][$action] !== true) {
            throw new Exception ('Fehler! Die Funktion ' . $action . ' ist nicht gültig');
        }

        $method = 'action_' . $method;
        $class = new $method;

        $run = $class->run($action);

        if ($run === false) {
            throw new Exception('Fehler beim ausführen der Action');
        }
    }
}

function interface_classloader ($className) {
    if (file_exists(ROOT . '/classes/' . strtolower($className) . '.php')) {
        include ROOT . '/classes/' . strtolower($className) . '.php';
    } else if (file_exists(ROOT . '/application/controllers/' . strtolower($className) . '.php')) {
        include ROOT . '/application/controllers/' . strtolower($className) . '.php';
    } else if (file_exists(ROOT . '/application/models/' . strtolower($className) . '.php')) {
        include ROOT . '/application/models/' . strtolower($className) . '.php';
    } else {
        throw new Exception('class not found');
    }
}