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

/**
 * @category    TDProject
 * @package     TDProject_Webservice
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Webservice_Block_Logging_View
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
            'www/design/webservice/templates/logging/view.phtml'
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
    
    public function getNewLoggingUrl()
    {
        $parameter = array(
            'path' => '/webservice',
            'method' => 'loadProjects'
        );
        return $this->getUrl($parameter);
    }   
}

