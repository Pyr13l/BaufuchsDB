<?php

class Hardware extends Model {

    protected $coreTable = 'hardware';

    public function getAttributeList ($hardwareItemId = null) {

        if ($hardwareItemId === null) {
            $hardwareItemId = $this->getId();
        }

        $db = Database::getDB();
        $sql = 'SELECT
                      hp.name,
                      ha.value
                    FROM
                      hardware_attributes ha,
                      hardware_property hp
                    WHERE
                      hp.id = ha.hardware_property_id
                      AND ha.hardware_id = ?
                    ';

        $rows = $db->getRows($sql, array($hardwareItemId));

        if ($rows === false) {
            throw new Exception('Fehler! Attribute Query wirft Fehler!');
        }

        return $rows;

    }
}