<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/tdproject/pear/php');

require_once 'TechDivision/Collections/ArrayList.php';

require_once 'TDProject/Project/Common/ValueObjects/TaskUserLightValue.php';
require_once 'TDProject/Project/Common/ValueObjects/LoggingViewData.php';
require_once 'TDProject/Project/Common/ValueObjects/ProjectViewData.php';


class soap_methods {
	/**
	 * returns available Projects and Taks: dummyData
	 * @param int $userId (not used at the moment)
	 * @return object
	 *
	 */
	// @return TDProject_Project_Common_ValueObjects_ProjectViewData


	public function getProjectsAndTasks ($userId){
		$dummyProjects = new TechDivision_Collections_ArrayList();
		$dummyProjects->add("TestProject1");
		$dummyProjects->add("TestProject2");
			
		$dummyTasks = new TechDivision_Collections_ArrayList();
		$dummyTasks->add("TestTask1");
		$dummyTasks->add("TestTask2");
		$dummyTasks->add("TestTask3");



		$dto = new TDProject_Project_Common_ValueObjects_ProjectViewData();
		$dto->setProjects($dummyProjects);
		$dto->setTasks($dummyTasks);

		return $dto;
	}

	/**
	 * returns the last 10 loggings of the user with the given id
	 * @param int $userId
	 * @return object
	 */

	public function getLoggings($userId){
		$dummyLoggings = new TechDivision_Collections_ArrayList();
		$dummyLoggings->add("dummyLog1");
		$dummyLoggings->add("dummyLog2");

		$dto = new TDProject_Project_Common_ValueObjects_LoggingViewData;
		$dto->setLoggings($dummyLoggings);
		
		
		return $dto;

	}


	/**
	 * adds the given logging-object, returns it for testing
	 * @param object $lvo
	 * @return object
	 */
	public function saveLog(TDProject_Project_Common_ValueObjects_TaskUserLightValue $lvo){
		return $lvo;
	}

	/**
	 * adds the given logging-object, returns it for testing
	 * @param object $lvo
	 * @return object
	 */
	public function updateLog(TDProject_Project_Common_ValueObjects_TaskUserLightValue $lvo){
		return $lvo;
	}

	/**
	 * removes the logging-object with the given id, retruns dummyObject for testing
	 * @param int $LogId
	 * @return object
	 */
	public function removeLog($logId){
		
		$from = new TechDivision_Lang_Integer(time());
		$until = new TechDivision_Lang_Integer(time() + 10000);
		
		$lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
			$lvo->setFrom($from);
			$lvo->setUntil($until);
		
		return $lvo; 
	}

}
