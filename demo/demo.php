<?php
/**
 * Created by PhpStorm.
 * User: zjian
 * Date: 2015/12/24
 * Time: 9:27
 */
require_once("../lib/pump.php");

$tpl = file_get_contents('demo.pump');
$data = array(
    'list' => array(
        array(
            'name' => '讨论组',
            'age' => 18,
        ),
        array(
            'name' => '私は',
            'age' => 28,
        ),
    ),

    'maps' => array(
        array(
            'name' => 'li',
            'age' => 38,
        ),
        array(
            'name' => 'الصين',
            'age' => 48,
        ),
    ),
    'page'=>3,
);
$pump = new pump(dirname(__FILE__).DS);
//$pump->setTags('operationOpen','<%');

echo $pump->ParseTemplate($tpl, $data);