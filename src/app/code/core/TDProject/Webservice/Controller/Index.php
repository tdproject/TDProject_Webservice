<?php

/**
 * TDProject_Webservice_Controller_Index
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

//deactivate the wsdl-cache
ini_set("soap.wsdl_cache_enabled", 0);

class TDProject_Webservice_Controller_Index
    extends TDProject_Webservice_Controller_Abstract
{
    /**
     * The key of the ActionForward returned after an successfull operation.
     * @var string
     */
    const SUCCESS = "Success";

    /**
     * The key of the ActionForward returned after a failure.
     * @var string
     */
    const FAILURE = "Failure";


    /**
     * The key of the ActionForward forwarding to the index view.
     * @var string
     */
    const INDEX_VIEW = "IndexView";

    /**
     * The key of the ActionForward forwarding to the project view.
     * @var string
     */
    const PROJECT_VIEW = "ProjectView";

    /**
     * The key of the ActionForward forwarding to the task view.
     * @var string
     */
    const TASK_VIEW = "TaskView";

    /**
     * The key of the ActionForward forwarding to the logging-create view.
     * @var string
     */
    const CREATE_LOGGING_VIEW = "CreateLoggingView";
    /**
     * The key of the ActionForward forwarding to the logging-create view.
     * @var string
     */
    const EDIT_LOGGING_VIEW = "EditLoggingView";
    /**
     * The key of the ActionForward forwarding to the logging view.
     * @var string
     */
    const LOGGING_VIEW = "LoggingView";

    /**
     * The url where the wsdl-file can be found.
     * @var string
     */
    //const WSDL_URL = "http://localhost/tdproject/pear/php/soap.php?wsdl=1";
    const WSDL_URL = "http://localhost/tdproject/pear/php/?path=/api&method=wsdl";

    /**
     * This method is automatically invoked by the controller renders the
     * the index page.
     * @return void
     */
    public function __defaultAction()
    {
        $this->getContext()->setAttribute(
            'pageHeadline', 'SOAP-Webservice Tests'
        );

        return $this->_findForward(
            TDProject_Webservice_Controller_Index::INDEX_VIEW
        );
    }

    /**
    * This method is automatically invoked by the controller it calls the 
    * webservice to login the user and forwards to the page displaying the user's loggings.
    * @return void
    */
    public function loginAction()
    {
        //start local session
        $localSession = $this->_getRequest()->getSession();
        // get the username form the  request
        $userName = $this->_getRequest()->getParameter(
            'userName'
        );
        $password = $this->_getRequest()->getParameter(
        	'password'
       	);
        try {
            $client = new Zend_Soap_Client(self::WSDL_URL);
            $webserviceSessionId = $client->login($userName, $password);
        }catch(SoapFault $soapfault){
            $this->_getLogger()->error(
            	'A soap fault occured: '.$soapfault->__toString(), __LINE__
            );
            return $this->_findForward(
            TDProject_Webservice_Controller_Index::FAILURE
            );
        }
        
        $this->_getLogger()->error(
        	'webserviceSessionId: '.var_export($webserviceSessionId, true), __LINE__
        );
        //if a session Id was returned save it in an local-session
        //and return to the view with the logging-data
        if ($webserviceSessionId) {
            $localSession->setAttribute(
                'webserviceSessionId', $webserviceSessionId
            );        
            return $this->loadLoggingsAction();
        }
        else {
            return $this->_findForward(
            TDProject_Webservice_Controller_Index::FAILURE
            );
        }
        
    }
    /**
     * dummy-Action returning the stored logIn
     */
    public function getLoginAction()
    {
        //start local session
        $localSession = $this->_getRequest()->getSession();
        //get the webservice's sessionId from the local session
        $webserviceSessionId = $localSession->getAttribute(
        	'webserviceSessionId'
        );
        try {
            $client = new Zend_Soap_Client(self::WSDL_URL);
            //set the sessionId to be used by the webservice
            $client->setCookie('PHPSESSID', $webserviceSessionId);
            $login = $client->getLogin();
        }catch(SoapFault $soapfault){
            $this->_getLogger()->error(
            	'A soap fault occured: '.$soapfault->__toString(), __LINE__
            );
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
                );
        }
        $this->_getLogger()->error(
            'stored login: '.var_export($login, true), __LINE__
        );
        return $this->__defaultAction();
    }
    
    /**
     * This method is automatically invoked by the controller renders the
     * page with available projects for the user.
     * @return void
     */
    public function loadProjectsAction()
    {
        //start local session
        $localSession = $this->_getRequest()->getSession();
        //get the webservice's sessionId from the local session
        $webserviceSessionId = $localSession->getAttribute(
        	'webserviceSessionId'
        );
        // check for stored WebserviceSessionId
        if ($webserviceSessionId == null) {

            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Project_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                    'no webserviceSessionId was found'
                )
            );
            // return to the login-page
            return $this->loginAction();
        }
        try {
            $client = new Zend_Soap_Client(self::WSDL_URL);
            //set the sessionId to be used by the webservice
            $client->setCookie('PHPSESSID', $webserviceSessionId);
            $projects = $client->getProjects();

            $this->getContext()->setAttribute(
                'pageHeadline', 'SOAP-Webservice Tests'
            );
            $this->getContext()->setAttribute(
                'headline', 'available projects'
            );
            $this->getContext()->setAttribute(
                'projects', $projects
            );

            // return to the project detail page
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::PROJECT_VIEW
            );
        }catch(SoapFault $soapfault){
            $this->_getLogger()->error(
                'A soap fault occured: '.$soapfault->__toString(), __LINE__
            );
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }
    }

    /**
     * This method is automatically invoked by the controller renders the
     * page with available taks for the user and project.
     * @return void
     */
    public function loadTasksAction()
    {
        //check if required values are handed over
        if (($this->_getRequest()->getParameter('projectId')=== NULL) ||
            ($this->_getRequest()->getParameter('projectName')===NULL)) {
            // return to the error page
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }
        //get values from the request
        $projectId = (int) $this->_getRequest()->getParameter('projectId');
        $projectName = $this->_getRequest()->getParameter('projectName');

        $session = $this->_getRequest()->getSession();
        $session->setAttribute('projectId', $projectId);
        $session->setAttribute('projectName', $projectName);

        try{
            $client = new Zend_Soap_Client(self::WSDL_URL);
            $tasksObject = $client->getTasks($projectId);

            $this->getContext()->setAttribute(
                'pageHeadline', 'SOAP-Webservice Tests'
            );
            $this->getContext()->setAttribute(
                'headline', 'getTasks for project '.$projectName
            );
            $this->getContext()->setAttribute(
                'tasksObject', $tasksObject
            );

            // return to the project detail page
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::TASK_VIEW
            );
        } catch(SoapFault $soapFault){
            $this->_getLogger()->error(
                'A soap fault occured: '.$soapfault->__toString(), __LINE__
            );
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }
    }

    /**
     * This method is automatically invoked by the controller renders the
     * page to create a new logging-entry.
     * @return void
     */
    public function createLoggingAction()
    {
        //check required values are handed over
        if (($this->_getRequest()->getParameter('taskId') === NULL) ||
        ($this->_getRequest()->getParameter('taskName') === NULL)) {
            // return to the error page
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }
        //get values from the request
        $taskId = (int) $this->_getRequest()->getParameter('taskId');
        $taskName = $this->_getRequest()->getParameter('taskName');

        $session = $this->_getRequest()->getSession();
        $session->setAttribute('taskId', $taskId);
        $session->setAttribute('taskName', $taskName);

        //get projectName from session
        $projectName = $session->getAttribute('projectName');


        $this->getContext()->setAttribute(
            'pageHeadline', 'SOAP-Webservice Tests'
        );
        $this->getContext()->setAttribute('headline', 'create new Logging');
        $this->getContext()->setAttribute('projectName', $projectName);
        $this->getContext()->setAttribute('taskName', $taskName);


        return $this->_findForward(
            TDProject_Webservice_Controller_Index::CREATE_LOGGING_VIEW
        );

    }

    /**
     * This method is automatically invoked by the controller.
     * Sends the collected data to the webservice to save the logging.
     * @return void
     */
    public function saveLoggingAction()
    {
        //start local session
        $session = $this->_getRequest()->getSession();
        //get the webservice's sessionId from the local session
        $webserviceSessionId = $session->getAttribute(
        	'webserviceSessionId'
        );
        // check for stored WebserviceSessionId
        if ($webserviceSessionId == null) {
        
            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
            new TechDivision_Controller_Action_Error(
                TDProject_Project_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                'no webserviceSessionId was found'
                )
            );
            // return to the login-page
            return $this->loginAction();
        }
        //check if required values are handed over
        if (($this->_getRequest()->getParameter('from') === NULL) ||
        ($this->_getRequest()->getParameter('until') === NULL)||
        $this->_getRequest()->getParameter('from') === NULL ) {
            // return to the error page
            return $this->_findForward(
            TDProject_Webservice_Controller_Index::FAILURE
            );
        }
        //get values from the request
        $fromString = $this->_getRequest()->getParameter('from');
        $untilString = $this->_getRequest()->getParameter('until');
        $description = $this->_getRequest()->getParameter('description');
        
        //convert time-string into timestamps
        $dateFrom = new Zend_Date($fromString);
        $timestmapFrom = $dateFrom->toValue();
        
        $dateUntil = new Zend_Date($untilString);
        $timestampUntil = $dateUntil->toValue();
        
        try {
            //$userId = $session->getAttribute('userId');
            //$userName = $session->getAttribute('userName');

            $projectId = $session->getAttribute('projectId');
            $projectName = $session->getAttribute('projectName');

            $taskId = $session->getAttribute('taskId');
            $taskName = $session->getAttribute('taskName');

            //add values to lightValue-object
            $lvo = new TDProject_Project_Common_ValueObjects_TaskUserLightValue();
            $lvo->setTaskIdFk(new TechDivision_Lang_Integer($taskId));
            //$lvo->setUserIdFk(new TechDivision_Lang_Integer($userId));
            $lvo->setProjectIdFk(new TechDivision_Lang_Integer($projectId));
            $lvo->setTaskName(new TechDivision_Lang_String($taskName));
            $lvo->setProjectName(new TechDivision_Lang_String($projectName));
            //$lvo->setUsername(new TechDivision_Lang_String($userName));
            $lvo->setDescription(new TechDivision_Lang_String($description));
            $lvo->setFrom(new TechDivision_Lang_Integer($timestmapFrom));
            $lvo->setUntil(new TechDivision_Lang_Integer($timestampUntil));
            $client = new Zend_Soap_Client(self::WSDL_URL);
            $client->setCookie('PHPSESSID', $webserviceSessionId);
            $client->saveLog($lvo);

        } catch(SoapFault $soapFault) {
            $this->_getLogger()->error(
                'A soap fault occured: ' . $soapfault->__toString(), __LINE__
            );
            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );

        } catch(Exception $e) {

            $this->_getLogger()->error($e->getMessage(), __LINE__);

            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }

        return $this->loadLoggingsAction();
    }

    /**
     * This method is automatically invoked by the controller renders the
     * page to display the last 10 loggings of the user.
     * @return void
     */
    public function loadLoggingsAction()
    {
        //start local session
        $localSession = $this->_getRequest()->getSession();
        //get the webservice's sessionId from the local session
        $webserviceSessionId = $localSession->getAttribute(
        	'webserviceSessionId'
        );
        // check for stored WebserviceSessionId
        if ($webserviceSessionId == null) {
        
            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
            new TechDivision_Controller_Action_Error(
            TDProject_Project_Controller_Util_ErrorKeys::SYSTEM_ERROR,
            	'no webserviceSessionId was found'
                )
            );
            // return to the login-page
            return $this->loginAction();
        }
        $client = new Zend_Soap_Client(self::WSDL_URL);
        $client->setCookie('PHPSESSID', $webserviceSessionId);
        
        $loggings = $client->getLoggings();

        $this->getContext()->setAttribute(
            'pageHeadline', 'SOAP-Webservice Tests'
        );
        $this->getContext()->setAttribute(
            'headline', 'test loadLoggings '
        );
        $this->getContext()->setAttribute('loggings', $loggings);

        //return to the project detail page
        return $this->_findForward(
            TDProject_Webservice_Controller_Index::LOGGING_VIEW
        );

    }

    //TODO: edit loggings
    /*
    public function editLoggingAction(){
    //start session and get userId
    $session = $this->_getRequest()->getSession();
    $userId = $session->getAttribute('userId');

    //get projectId from request
    if ( ($this->_getRequest()->getParameter('logId')) !== null) {
    $logId = (int)$this->_getRequest()->getParameter('logId');
    }

    //call soap-server
    $client = new Zend_Soap_Client(self::WSDL_URL);
    $log = $client->getLogDetails($logId);

    $this->getContext()->setAttribute('pageHeadline', 'SOAP-Webservice Tests');
    $this->getContext()->setAttribute('headline', 'test editLog for logId='.$logId);
    $this->getContext()->setAttribute('log', $log);

    return $this->_findForward(
    TDProject_Webservice_Controller_Index::EDIT_LOGGING_VIEW
    );
    }
    */
}