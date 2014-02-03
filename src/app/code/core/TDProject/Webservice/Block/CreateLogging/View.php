<?php

/**
 * TDProject_Webservice_Block_Project_View
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TDProject/Core/Block/Abstract.php';
require_once 'Zend/Date.php';

/**
 * @category    TDProject
 * @package     TDProject_Webservice
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Webservice_Block_CreateLogging_View
extends TDProject_Core_Block_Abstract
{
    /**
     * Initialize the block with the
     * apropriate template and name.
     *
     * @return void
     */
    public function __construct(
    TechDivision_Controller_Interfaces_Context $context)
    {
        // set the template name
        $this->_setTemplate(
            'www/design/webservice/templates/create_logging/view.phtml'
        );
        // call the parent constructor
        parent::__construct($context);
    }

    /**
    * Gets the headline from the context and returns it.
    * @return string headline
    */
    public function getHeadline()
    {
        return $this->getContext()->getAttribute('headline');
    }
    
    /**
     * Gets the page-headline from the context and returns it.
     * @return string page-headline
     */
    public function getPageHeadline()
    {
        return $this->getContext()->getAttribute('pageHeadline');
    }
    
    /**
    * Gets the projectName from the context and returns it.
    * @return string projectName
    */
    public function getProjectName()
    {
        return $this->getContext()->getAttribute('projectName');
    }
    
    /**
    * Gets the taskName from the context and returns it.
    * @return string taskName
    */
    public function getTaskName()
    {
        return $this->getContext()->getAttribute('taskName');
    }
    
    /**
     * Generates a new Zend_Date instance with the current date und time
     * and returns it.
     * @return Zend_Date
     */
    public function getDateFrom()
    {
        return $dateFrom = new Zend_Date();
    }
    
    /**
    * Generates a new Zend_Date instance with the current date und time 
    * +30 minutes and returns it.
    * @return Zend_Date
    */
    public function getDateUntil()
    {
        $dateUntil = new Zend_Date();
        $dateUntil->add(30, ZEND_DATE::MINUTE);
        
        return $dateUntil;
    }
}

