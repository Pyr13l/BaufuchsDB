<?php
/**
 * TODO: Dokumentation schreiben
 * TODO: Foreachable bauen (Iterator)
 * Class ModelList
 */
class ModelList implements Iterator {

    protected $_list;

    protected $_modelType;

    /**
     * Array mit Parametern, die im where Abschnitt des Queries berücksichtigt werden sollen
     * @var array
     */
    protected $_whereParams = array();

    /**
     * Feld, nachdem sortiert werden soll.
     * fieldname => ASC
     * OR
     * fieldname => DESC
     * @var string
     */
    protected $_orderBy = array();

    /**
     * Felder, die selectiert werden sollen
     * @var array
     */
    protected $_selectFields = array();

    /**
     * Name der Tabelle
     * @var string
     */
    protected $_tableName = '';

    public function __construct($modelType) {
        $this->_modelType = $modelType;

        $this->addSelectField('id');
    }

    /**
     * Sucht anhand des Queries nach IDs und baut ein Array auf
     * @param $sql
     * @param array $params
     */
    public function selectString ($sql, $params = array()) {
        $db = Database::getDB();

        $result = $db->getRows($sql, $params);

        if (count($result) === 0) {
            $this->_list = array();
            return;
        }

        foreach ($result as $item) {
            $id = $item['id'];
            $model = clone $this->_modelType;
            $model->load($id);
            $this->_list[$id] = $model;
        }
    }

    /**
     * Gibt das BaseObjekt der Liste zurück
     * @return mixed
     */
    public function getBaseObject () {
        return clone $this->_modelType;
    }

    /**
     * Gibt ein Array mit Elementen zurück
     * @return mixed
     */
    public function getElements () {
        return $this->_list;
    }

    /**
     * Gibt die anzahl der Elemente zurück
     * @return int
     */
    public function count() {
        return count($this->_list);
    }

    //Funktionen zum Aufbauen des Queries über Funktionen
    //TODO: Need to be

    /**
     * @param $field
     * @param $value
     */
    public function addWhereParameter ($field, $value) {
        $this->_whereParams[$field] = $value;
    }

    /**
     * @param $field
     * @param $type
     */
    public function addOrderByField ($field, $type) {
        $this->_orderBy[$field] = $type;
    }

    /**
     * @param $field
     * @param string $asName
     */
    public function addSelectField ($field, $asName = '') {
        $this->_selectFields[$field] = $asName;
    }

    /**
     * @param $tableName
     */
    public function setSelectTable ($tableName) {
        $this->_tableName = $tableName;
    }

    protected function _buildQuery () {
        if ($this->_tableName === '') {
            throw new Exception('you have to set a table name befor run _buildQuery in execute');
        }

        if (count($this->_selectFields) === 0) {
            throw new Exception('you have to set one or more select fields befor run _buildQuery in execute');
        }

        $db = Database::getDB(PDO::FETCH_ASSOC);

        /**
         * Zusammensetzten der select Felder
         */
        $sql = 'select ';
        foreach ($this->_selectFields as $nameField => $asField) {
            $limiter = ',';

            end($this->_selectFields);
            if (key($this->_selectFields) === $nameField) {
                $limiter = '';
            }

            if ($asField === '') {
                $sql .= $nameField . $limiter;
            } else {
                $sql .= $nameField . ' as ' . $asField . $limiter;
            }
        }

        $sql .= ' from ' . $this->_tableName;

        /*
         * Zusammensetzten der Where Parameter, wenn welche vorhanden
         */
        if (count($this->_whereParams) > 0) {
            $sql .= ' where ';

            foreach ($this->_whereParams as $field => $value) {
                $limiter = ' and ';
                end($this->_whereParams);
                if (key($this->_whereParams) == $field) {
                    $limiter = '';
                }

                $sql .= ' ' . $field . ' = ' . $db->escape($value) . $limiter;
            }
        }

        /**
         * Zusammenbauen der order by parameter
         */
        if (count($this->_orderBy) > 0) {
            $sql .= ' order by ';

            foreach ($this->_orderBy as $field => $type) {
                $limiter = ',';
                if (end($this->_orderBy) && key($this->_orderBy) === $field) {
                    $limiter = '';
                }

                $sql .= $field . ' ' . $type . $limiter;
            }
        }

        return $sql;
    }

    public function execute() {
        $query = $this->_buildQuery();
        var_dump($query);
        $this->selectString($query);
    }

    public function getArray () {
        $array = array();
        foreach ($this->_list as $item) {
            $array[] = $item->getSimpleFieldList();
        }

        return $array;
    }

    /**
     * Iterator Funktionen
     */
    public function rewind()
    {
        reset($this->_list);
    }

    public function current()
    {
        return current($this->_list);
    }

    public function key()
    {
        return key($this->_list);
    }

    public function next()
    {
        $var = next($this->_list);

        return $var;
    }

    public function valid()
    {
        $valid = $this->current() !== false;

        return $valid;
    }
}