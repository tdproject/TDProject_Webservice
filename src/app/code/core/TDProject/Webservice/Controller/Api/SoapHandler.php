<?php

/**
 * TDProject_Webservice_Model_SoapHandler
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */


/**
 * @category    TDProject
 * @package     TDProject_Webservice
 * @copyright   Copyright (c) 2011 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Markus Berwanger <m.berwanger@techdivision.com>
 */

ini_set("soap.wsdl_cache_enabled", 0);

class TDProject_Webservice_Controller_Api_SoapHandler
{

	/**
	 * This method returns the logger of the requested
	 * type for logging purposes.
	 *
     * @param string The log type to use
	 * @return TechDivision_Logger_Logger Holds the Logger object
	 * @throws Exception Is thrown if the requested logger type is not initialized or doesn't exist
	 * @deprecated 0.6.24 - 2011/12/16
	 */
	protected function _getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
		return TDProject_Factory::get()->getLogger($logType);
    }

    /**
     * This method returns the logger of the requested
     * type for logging purposes.
     *
     * @param string The log type to use
     * @return TechDivision_Logger_Logger Holds the Logger object
     * @since 0.6.25 - 2011/12/16
     */
	public function getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
	{
	    return $this->_getLogger($logType);
	}
    
    /**
     * dummy Login
     * @param String $userName
     * @param String $password
     * @return String
     */
    public function login($userName, $password)
    {
        //check if a session was already started, if not start one
        $sessionId = session_id();
        if ($sessionId === '') {
            session_start();
            $sessionId = session_id();
        }
        
        // get the userData from the delegate
        $delegate
            = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
                session_id()
            );
        $userLVO 
            = $delegate->getUserByUsername(new TechDivision_Lang_String($userName));
        // if no values fo the user were found return an empty string
        if ($userLVO === null) {
            $this->_getLogger()->error( $userName.' was not found in the database', __LINE__);
            return '';
        }
        //get the stored and given password
        $storedPasswordHash = $userLVO->getPassword();
        $givenPasswordHash = new TechDivision_Lang_String(md5($password));
        //compare the passwords
        if ($storedPasswordHash->equals($givenPasswordHash)) {
            
            $this->_getLogger()->error( $userName.' sucessfully logged in.', __LINE__);
            //store the user in the session
            $_SESSION['user'] = $userLVO;
            //return the sessionId to access the data of the session
            return $sessionId;
        }
        //if password did not match
        return '';
        $this->_getLogger()->error('the given password did not match the given one', __LINE__);
    }
    
    /**
     * dummy return-function
     * @return string
     */
    public function getLogin()
    {
        //check if a session was already started, if not start one
        $sessionId = session_id();
        if ($sessionId === '') {
            session_start();
        }
        $test = $_SESSION['user']->getUsername()->StringValue();
        
        return $test;
    }
    
    /**
     * returns available Projects for the given user
     * @return object
     */
    
    public function getProjects()
    {
        //start the session and check if a user is logged in
        $sessionId = session_id();
        if ($sessionId === '') {
            session_start();
        }
        if (! isset($_SESSION['user'])) {
            return null;
        }
        $user = $_SESSION['user'];
        
        //get delegate
        $delegate
        = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
            session_id()
        );

        return $delegate
        ->getProjectOverviewDataByUserId($user->getUserId())
        ->toArray();

    }
    /**
     * returns a list of tasks for the given project
     * @param int $projectId
     * @return object
     */
    public function getTasks($projectId)
    {
        $delegate
        = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
            session_id()
        );

        return $delegate->getTaskOverviewDataByProjectId(
            new TechDivision_Lang_Integer($projectId)
        )->toSimpleClass();

    }

    /**
     * returns the last 10 loggings of the user with the given id
     * @return object
     */

    public function getLoggings()
    {
        //start the session and check if a user is logged in
        $sessionId = session_id();
        if ($sessionId === '') {
            session_start();
        }
        if (! isset($_SESSION['user'])) {
            return null;
        }
        $user = $_SESSION['user'];
        
        $delegate
        = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
            session_id()
        );

        $userId = $user->getUserId();
        
        return $delegate->getLoggingViewDataByUserId($userId);

    }

    /**
     * returns details for the logging-entry with the given Id
     * @param int $logId
     * @return object
     */
    public function getLogDetails($logId)
    {
        $delegate
        = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
            session_id()
        );

        $dto = $delegate->getLoggingViewDataByTaskId(
            new TechDivision_Lang_Integer($logId)
        );

        return $dto;
    }


    /**
     * adds the given logging-object, returns it for testing
     * @param object $stdClassObject
     * @return object
     */
    public function saveLog($stdClassObject)
    {
        //start the session and check if a user is logged in
        $sessionId = session_id();
        if ($sessionId === '') {
            session_start();
        }
        if (! isset($_SESSION['user'])) {
            return null;
        }
        $user = $_SESSION['user'];

        //regenerate LightValue-object from stdClass-object
        $lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();


        $lvo->setTaskUserId(new TechDivision_Lang_Integer(0)); //value to signalize a new TaskUser-relation
        $lvo->setTaskIdFk(
            new TechDivision_Lang_Integer($stdClassObject->taskIdFk->value)
        );
        $lvo->setUserIdFk(
            $user->getUserId()
        );
        $lvo->setProjectIdFk(
            new TechDivision_Lang_Integer($stdClassObject->projectIdFk->value)
        );
        $lvo->setTaskName(
            new TechDivision_Lang_String($stdClassObject->taskName->_value)
        );
        $lvo->setProjectName(
            new TechDivision_Lang_String($stdClassObject->projectName->_value)
        );
        $lvo->setUsername(
            $user->getUsername()
        );
        $lvo->setDescription(
            new TechDivision_Lang_String($stdClassObject->description->_value)
        );
        $lvo->setFrom(
            new TechDivision_Lang_Integer($stdClassObject->from->value)
        );
        $lvo->setUntil(
            new TechDivision_Lang_Integer($stdClassObject->until->value)
        );


        $delegate = TDProject_Webservice_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(
            session_id()
        );

        $delegate->saveTaskUser($lvo);

        return $lvo;
    }

    /**
     * updates the given logging-object, returns it for testing
     *     (dummy implementation)
     * @param object $lvo
     * @return object
     */
    public function updateLog(
        TDProject_Project_Common_ValueObjects_TaskUserLightValue $lvo)
    {
        return $lvo;
    }

    /**
     * removes the logging-object with the given id,
     *     returns dummyObject for testing
     * @param int $LogId
     * @return object
     */
    public function removeLog($logId)
    {
        $from = new TechDivision_Lang_Integer(time());
        $until = new TechDivision_Lang_Integer(time() + 10000);

        $lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
        $lvo->setFrom($from);
        $lvo->setUntil($until);

        return $lvo;
    }

}