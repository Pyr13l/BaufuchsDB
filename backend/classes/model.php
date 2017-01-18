<?php

class Model {

    protected $coreTable = '';

    protected $tableFields = array();

    protected $id = -1;

    protected $_isLoaded = false;

    protected $_lastError = null;

    public function load ($id) : bool {
        $sql = 'select * from ' . $this->getTableName() . ' where id = ? limit 1';

        $result = Database::getDB()->getRow($sql, array($id));

        if($result !== null && $result !== false) {
            foreach ($result as $key => $value) {
                $this->tableFields[$this->coreTable . '__' . $key] = new ModelField($key, $value);
            }

            $this->_isLoaded = true;
            return true;
        }

        return false;
    }

    public function getTableName () {
        return DataBase::getDB()->prefix . $this->coreTable;
    }

    public function __get($name) {
        if(isset($this->tableFields[$name])) {
            return $this->tableFields[$name];
        } else {
            return new ModelField(null, null);
        }
    }

    public function __set($name, $value) {
        $this->tableFields[$name] = $value;
    }

    public function assign ($params) {
        foreach ($params as $key => $param) {
            $this->tableFields[$this->coreTable . '__' . $key] = new ModelField($key, $param);
        }
    }

    public function save () : bool{
        if ($this->getId() === -1) {
            return $this->_insertInto();
        }

        $sql = 'update ' . $this->getTableName() . ' set ';
        foreach ($this->tableFields as $field) {
            $delimiter = ',';
            if ($field === end($this->tableFields)) {
                $delimiter = '';
            }
            $sql .= $field->name . ' = ' . DataBase::getDB()->escape($field->value) . $delimiter;
        }
        $sql .= ' where id = ' . DataBase::getDB()->escape($this->getId());

        $result = Database::getDB()->execute($sql);

        if ($result === false) {
            return false;
        }

        return true;
    }

    protected function _insertInto () : bool {

        $db = Database::getDB(PDO::FETCH_ASSOC);
        $sql = 'insert into ' . $this->getTableName() . ' (';

        if ($this->getId() == -1) {
            $this->{$this->coreTable . '__id'}->value = null;
        }


        foreach ($this->tableFields as $field) { 
            $delimiter = ',';
            if ($field === end($this->tableFields)) {
                $delimiter = '';
            }

            $sql .= $field->name . $delimiter;
        }

        $sql .= ') VALUES (';

        foreach ($this->tableFields as $field) {
            $delimiter = ',';
            if ($field === end($this->tableFields)) {
                $delimiter = '';
            }
            if ($field->value !== null) {
                $sql .= $db->escape($field->value) . $delimiter;
            } else {
                $sql .= 'NULL' . $delimiter;
            }
        }

        $sql .= ');';

        $dbResult = $db->execute($sql);
        if ($db->isSuccessSubmit !== true) {
            $this->_lastError = $dbResult->errorInfo()[2];

            return false;
        }

        return true;
    }

    public function getError () {
        return $this->_lastError;
    }

    public function getId() {
        if (
            isset($this->tableFields[$this->coreTable . '__id'])
            && $this->tableFields[$this->coreTable . '__id'] instanceof ModelField
        ) {
            $id = $this->tableFields[$this->coreTable . '__id']->value;
        } else {
            $id = -1;
        }
        return $id;
    }

    public function getField ($name) {
        return $this->__get($this->coreTable . '__' . $name);
    }

    public function getFieldValue ($name) {
        return $this->getField($name)->value;
    }

    public function getSimpleFieldList () : array {

        $list = array();
        foreach ($this->tableFields as $name => $field) {
            $name = str_replace($this->coreTable . '__', '', $name);
            $list[$name] = $field->value;
        }

        return $list;
    }
}

?>