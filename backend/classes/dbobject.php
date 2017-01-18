<?php
class dbObject {

    protected $connection;

    protected $mode;

    public $isSuccessSubmit = false;

    public $prefix;

    public function __construct($connection, $mode = PDO::FETCH_ASSOC, $prefix) {
        $this->connection   = $connection;
        $this->mode         = $mode;
        $this->prefix       = $prefix;
    }

    public function execute ($sql, $vars = array()) {
        $statement = $this->connection->prepare ($sql);
        $this->isSuccessSubmit = $statement->execute($vars);

        return $statement;
    }

    public function getRow ($sql, $vars = array()) {
        $statement = $this->execute($sql, $vars);

        $row = $statement->fetch($this->mode);

        return $row;
    }

    public function getRows ($sql, $vars = array()) {
        $statement = $this->execute($sql, $vars);

        $fetch = array();
        while ($row = $statement->fetch($this->mode)) {
            $fetch[] = $row;
        }

        return $fetch;
    }

    public function escape ($string) {
        return $this->connection->quote($string);
    }

    public function getErrorInfo () {

    }
}
?>