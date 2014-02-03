<?php

/**
 * TDProject_Webservice_Controller_Util_WebRequestKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TDProject/Common/Util/WebRequestKeys.php';

/**
 * @category    TDProject
 * @package     TDProject_Webservice
 * @copyright   Copyright (c) 2011 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Markus Berwanger <m.berwanger@techdivision.com>
 */
class TDProject_Webservice_Controller_Util_WebRequestKeys
    extends TDProject_Common_Util_WebRequestKeys
{
    /**
     * The Request parameter key with the autoDiscover-content
     * @var string
     */
    const AUTO_DISCOVER = "autoDiscover";
    
    /**
    * The Request parameter key with the soapServer-content
    * @var string
    */
    const SOAP_SERVER = "soapServer"; 
}