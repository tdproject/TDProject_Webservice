<?php

/**
 * TDProject_Project_Model_Services_DomainProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   	TDProject
 * @package    	TDProject_Project
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Webservice_Model_Services_DomainProcessor
    extends TDProject_Webservice_Model_Services_AbstractDomainProcessor
{

	/**
	 * This method returns the logger of the requested
	 * type for logging purposes.
	 *
     * @param string The log type to use
	 * @return TechDivision_Logger_Logger Holds the Logger object
	 * @throws Exception Is thrown if the requested logger type is not initialized or doesn't exist
	 * @deprecated 0.1.14 - 2011/12/19
	 */
    protected function _getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
    	return $this->getLogger();
    }   
    
    /**
     * This method returns the logger of the requested
     * type for logging purposes.
     *
     * @param string The log type to use
     * @return TechDivision_Logger_Logger Holds the Logger object
     * @since 0.1.15 - 2011/12/19
     */
    public function getLogger(
    	$logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
    	return $this->getContainer()->getLogger();
    }
    
    /**
     * Gets the userData by the username.
     * @param TechDivision_Lang_String $username
     * @return void
     */
    public function getUserByUsername(TechDivision_Lang_String $username)
    {
        $userLocalHome = TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer());
        return $userLocalHome->findByUsername($username)->getLightValue();
    }

    /**
     * (non-PHPdoc)
     * @see
     * TDProject/Webservice/Common/Delegates/Interfaces/DomainProcessorDelegate
     *      #getProjectOverviewDataByUserId(TechDivision_Lang_Integer $userId)
     */
    public function getProjectOverviewDataByUserId(
        TechDivision_Lang_Integer $userId)
    {
        try {

            $list = new TechDivision_Collections_ArrayList();

            // initialize the Home for loading the project's company
            $collection = TDProject_Project_Model_Utils_ProjectUtil::getHome($this->getContainer())
            ->findAllByUserIdFk($userId);

            foreach ($collection as $element) {
                $list->add($element->getLightValue());
            }

            // assemble and return the initialized LVO's
            return $list;
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see
     * TDProject/Project/Common/Delegates/Interfaces/DomainProcessorDelegate
     * 	   #getTaskOverviewDataByProjectId(TechDivision_Lang_Integer $projectId)
     */
    public function getTaskOverviewDataByProjectId(
        TechDivision_Lang_Integer $projectId)
    {
        try{

            return TDProject_Project_Model_Assembler_Task::create($this->getContainer())
                ->getTaskOverviewDataByProjectId($projectId);

        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }

    }

    /**
     * (non-PHPdoc)
     * @see
     * TDProject/Project/Common/Delegates/Interfaces/DomainProcessorDelegate
     *     #getLoggingViewData(
     *         TechDivision_Lang_Integer $userId,
     *         TechDivision_Lang_Integer $taskUserId = null)
     */
    public function getLoggingViewDataByUserId(
        TechDivision_Lang_Integer $userId)
    {
        $dto = TDProject_Project_Model_Assembler_Logging::create($this->getContainer())
            ->getLoggingViewData($userId);

        $detailedLoggingList = $dto->getLoggings();

        $loggingList = new TechDivision_Collections_ArrayList();

        foreach ($detailedLoggingList as $logging) {
            $loggingList->add($logging->toStdClass());
        }

        $loggingArray = $loggingList->toArray();

        return $loggingArray;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Webservice/Common/Delegates/Interfaces/DomainProcessorDelegate
     *     #saveTaskUser(
     *         TDProject_Project_Common_ValueObjects_TaskUserLightValue $lvo)
     */
    public function saveTaskUser(
    TDProject_Project_Common_ValueObjects_TaskUserLightValue $lvo)
    {
        try {
            // begin the transaction
            $this->beginTransaction();
            // save the logging entry
            $taskUserId = TDProject_Project_Model_Actions_Logging::create($this->getContainer())
            ->saveTaskUser($lvo);
            // commit the transaction
            $this->commitTransaction();
            // return the ID of the task-user relation
            return $taskUserId;
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }


    //TODO: edit feature
    public function getLoggingViewDataByTaskId(
        TechDivision_Lang_Integer $taskId)
    {
        $dto = new TDProject_Project_Common_ValueObjects_LoggingViewData(
            TDProject_Project_Model_Utils_TaskUserUtil::getHome($this->getContainer())
            ->findByPrimaryKey($taskId)->getLightValue()
        );
        return $dto;

        $lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
        $lvo->setTaskUserId($dto->getTaskUserId());
        $lvo->setTaskIdFk($dto->getTaskIdFk());
        $lvo->setUserIdFk($dto->getUserIdFk());
        $lvo->setProjectIdFk($dto->getProjectIdFk());
        $lvo->setTaskName($dto->getTaskName());
        $lvo->setProjectName($dto->getProjectName());
        $lvo->setUsername($dto->getUsername());
        $lvo->setDescription($dto->getDescription());
        $lvo->setFrom($dto->getFrom());
        $lvo->setUntil($dto->getUntil());

        return $lvo;


        $dto = TDProject_Project_Model_Assembler_Logging::create($this->getContainer())
            ->getLoggingViewData(new TechDivision_Lang_Integer(1));

        $detailedLoggingList = $dto->getLoggings();

        $loggingList = new TechDivision_Collections_ArrayList();

        foreach ($detailedLoggingList as $logging) {
            $loggingList->add($logging->toStdClass());
        }
        //$loggingArray = $loggingList->toArray();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Webservice/Common/Delegates/Interfaces/DomainProcessorDelegate#getAcl()
     */
    public function getAcl()
    {
        try {
            // load and return the ACL
            return TDProject_Core_Model_Actions_Acl::create($this->getContainer())
                ->getAcl();
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Webservice/Common/Delegates/Interfaces/DomainProcessorDelegate#getGuestUser()
     */
    public function getGuestUser()
    {
        try {
            // load and return the guest user DTO
            return TDProject_Core_Model_Utils_System_UserUtil::getHome($this->getContainer())
                ->findByUsername(new TechDivision_Lang_String('guest'))
                ->getValue();
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Webservice/Common/Delegates/Interfaces/DomainProcessorDelegate#getSystemSettings()
     */
    public function getSystemSettings()
    {
        try {

            // load and return the guest user DTO
            $settings = TDProject_Core_Model_Utils_SettingUtil::getHome($this->getContainer())
                ->findAll();

            foreach ($settings as $setting) {
                return $setting->getLightValue();
            }

        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }
}