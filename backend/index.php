<?php
header("Content-Type: text/html; charset=utf-8");
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
define('ROOT' ,  dirname(__FILE__) . "/");

include ROOT . 'classes/basecontrol.php';

$baseControl = new BaseControl();
$baseControl->start();

try {
    $baseControl->runAction($_GET['method'], $_GET['action']);
} catch (Exception $e) {
    $error = array(
        'status' => 'error',
        'data'   => array(),
        'error'  => array(
            'file'   => $e->getFile(),
            'line'   => $e->getLine(),
            'message'=> $e->getMessage(),
        ),
    );

    echo json_encode($error);
}
$baseControl->stop();