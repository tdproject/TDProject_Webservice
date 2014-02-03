<?php
ini_set("soap.wsdl_cache_enabled", 0);
set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/TestProject/');
set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/tdproject/pear/php');

require_once 'Zend/Soap/Client.php';

require_once 'TechDivision/Lang/Integer.php';
require_once 'TDProject/Project/Common/ValueObjects/TaskUserLightValue.php';


$client = new Zend_Soap_Client('http://localhost/TestProject/soap/soap_server.php?wsdl=1');

//test: getProjectsAndTasks
echo "<h2>test project and taks data:</h2>";

$object = $client->getProjectsAndTasks(5);

$mainArray = (array) $object;

$projectsObject = (array) $mainArray["_projects"];
$projects = $projectsObject["_items"];

echo "<pre>";
var_dump($projects);
echo "</pre>";

$tasksObject = (array) $mainArray["_tasks"];
$tasks = $tasksObject["_items"];

echo "<pre>";
var_dump($tasks);
echo "</pre>";


//test: getLoggings
echo "<h2>test getLoggings</h2>";

$object = $client->getLoggings();

$mainArray = (array)$object;
$loggingsObject = (array)$mainArray["_loggings"];
$loggings = $loggingsObject["_items"];

echo "<pre>";
var_dump($loggings);
echo "</pre>";

//test: saveLog
echo "<h2>test saveLog</h2>";

$from = new TechDivision_Lang_Integer(time());
$until = new TechDivision_Lang_Integer(time() + 18000);

$lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
$lvo->setFrom($from);
$lvo->setUntil($until);

$result = $client->saveLog($lvo);
var_dump($result);


//test: updateLog
echo "<h2>test updateLog</h2>";

$from = new TechDivision_Lang_Integer(time());
$until = new TechDivision_Lang_Integer(time() + 16000);

$lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
$lvo->setFrom($from);
$lvo->setUntil($until);

$result = $client->updateLog($lvo);
var_dump($result);

//test: removeLog
echo "<h2>test removeLog</h2>";

$result = $client->removeLog(5);
var_dump($result);
?>

