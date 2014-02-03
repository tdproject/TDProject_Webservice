<?php

/**
 * TDProject_Webservice_Controller_Api
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


class TDProject_Webservice_Controller_Api
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
     * The key of the ActionForward to the view displaying the wsdl-file
     * @var string
     */
    const WSDL = "Wsdl";

    /**
     * The key of the ActionForward to the view displaying soap-server results
     * @var string
     */
    const SOAP_SERVER = "SoapServer";

    /**
     * Holds the name of the class that provides the methods
     * that should be used by the webservice.
     * @var string
     */
    const WEBSERVICE_METHODS_CLASS 
    = "TDProject_Webservice_Controller_Api_SoapHandler";
    
    
    /**
     * (non-PHPdoc)
     * @see 
     * src/Interfaces/TechDivision_Controller_Interfaces_Action::preDispatch()
     */
    public function preDispatch()
    {
        // load the session
        $session = $this->_getRequest()->getSession();
        // try to load the ACL from the Session
        $acl = $session->getAttribute(
            TDProject_Core_Controller_Util_WebSessionKeys::ACL
        );
        // check if the ACL has already been loaded
        if ($acl == null) {
            // if not load the ACL and store them in the session
            $session->setAttribute(
                TDProject_Core_Controller_Util_WebSessionKeys::ACL,
                self::_getDelegate()->getAcl()
            );
        }
        // check if a system user has already been loaded
        if ($this->_getSystemUser() == null) {
            // if not, load the Guest user store him in the session
            $this->_setSystemUser(
                self::_getDelegate()->getGuestUser()
            );
        }
    }

    /**
     * This method is automatically invoked by the controller,
     * it offers the soap-server functionality
     * @return void
     */
    public function __defaultAction()
    {
        //get system settings
        $settings = $this->_getDelegate()->getSystemSettings();
        //create a new Zend_Soap_Server-instance
        $soapServer = new Zend_Soap_Server(
            $settings->getWsdlUri()->stringValue()
        );
        //configure the class to be used
        $soapServer->setClass(self::WEBSERVICE_METHODS_CLASS);
        //set soapServer-namespace
        $soapServer->setUri(
            $settings->getWebserviceUri()->stringValue() 
        );

        $result = $soapServer->handle();

        //hand the server-instance over to the view
        $this->getContext()->setAttribute(
            TDProject_Webservice_Controller_Util_WebRequestKeys::SOAP_SERVER,
            $result
        );
        return $this->_findForward(
            TDProject_Webservice_Controller_Api::SOAP_SERVER
        );
    }

    /**
     * This method is automatically invoked by the controller renders the
     * page to display the wsdl-file.
     * @return void
     */
    public function wsdlAction()
    {
        try{
            //get system settings
            $settings = $this->_getDelegate()->getSystemSettings();
            //create a new Zend_Soap_AutoDiscover-instance
            $autoDiscover = new Zend_Soap_AutoDiscover();
            //configure class
            $autoDiscover->setClass(self::WEBSERVICE_METHODS_CLASS);
            //set the uri where the soap-server can be found
            $autoDiscover->setUri(
                $settings->getWebserviceUri()->stringValue() 
            );
            //hand auto-configure instance over to the view where it is displayed
            $this->getContext()->setAttribute(
                TDProject_Webservice_Controller_Util_WebRequestKeys::AUTO_DISCOVER,
                $autoDiscover
            );

            return $this->_findForward(
                TDProject_Webservice_Controller_Api::WSDL
            );

        } catch (Exception $e) {
            $this->_getLogger()->error($e->getMessage(), __LINE__);

            return $this->_findForward(
                TDProject_Webservice_Controller_Index::FAILURE
            );
        }
    }

    /**
     * Tries to load the Block class specified as path parameter
     * in the ActionForward. If a Block was found and the class
     * can be instanciated, the Block was registered to the Request
     * with the path as key.
     *
     * @param TechDivision_Controller_Action_Forward $actionForward
     * 		The ActionForward to initialize the Block for
     * @return void
     */
    protected function _getBlock(
    TechDivision_Controller_Action_Forward $actionForward) 
    {
        // check if the class required to initialize the Block is included
        if (!class_exists($path = $actionForward->getPath())) {
            return;
        }
        // initialize the messages and add the Block
        $page = new $path($this->getContext());
        // register the Block in the Request
        $this->_getRequest()->setAttribute($path, $page);
    }
}