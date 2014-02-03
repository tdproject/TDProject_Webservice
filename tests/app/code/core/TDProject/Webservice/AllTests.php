<?php

/**
 * TDProject_Webservice_AllTests
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

// set the include path necessary for running the tests
set_include_path(
    get_include_path() . PATH_SEPARATOR
    . getcwd() . PATH_SEPARATOR
    . '${pear.lib.dir}'
);

require_once 'TechDivision/XHProfPHPUnit/TestSuite.php';
require_once 'TDProject/Webservice/ImplementationTest.php';

/**
 * The TestSuite that initializes all PHPUnit tests.
 *
 * @category    TDProject
 * @package     TDProject_Webservice
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Webservice_AllTests
{

    /**
     * Initializes the TestSuite.
     *
     * @return TechDivision_XHProfPHPUnit_TestSuite The initialized TestSuite
     */
    public static function suite()
    {
        // initialize the TestSuite
        $suite = new TechDivision_XHProfPHPUnit_TestSuite(
            '${an.project.name}',
            '',
            '${release.version}'
        );
        // add a test
        $suite->addTestSuite('TDProject_Webservice_ImplementationTest');
        // return the TestSuite itself
        return $suite;
    }
}