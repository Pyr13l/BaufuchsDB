<?php
class Database {

    protected static $connection = null;

    protected $server_database = null;

    protected $server_hostname = null;

    protected $server_username = null;

    protected $server_password = null;

    protected $server_driver = null;

    protected static $prefix = null;

    public function __construct($hostname, $database, $username, $password, $driver = 'mysql', $prefix = '') {
        $this->server_hostname  = $hostname;
        $this->server_database  = $database;
        $this->server_username  = $username;
        $this->server_password  = $password;
        $this->server_driver    = $driver;
        self::$prefix           = $prefix;
        $this->connect();
    }

    public function connect () {
        self::$connection = new PDO($this->server_driver . ':host=' . $this->server_hostname . ';dbname=' . $this->server_database, $this->server_username, $this->server_password);
    }

    public static function getDB ($mode = PDO::FETCH_ASSOC) {
        if(self::$connection === null) {
            return null;
        }

        $dbObject = new dbObject(self::$connection, $mode, self::$prefix);

        return $dbObject;
    }
}
