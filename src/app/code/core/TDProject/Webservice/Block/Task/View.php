<?php

/**
 * TDProject_Webservice_Block_Task_View
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
class TDProject_Webservice_Block_Task_View
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
            'www/design/webservice/templates/task/view.phtml'
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
     * Displayes a list-tree representation of the given taskObject.
     * @param TaskOverviewData $tasksObject
     * @return void
     */
    public function displayTasks($tasksObject){
        echo '<li>';

        //loggings can only be booked on tasks that are selectable
        if ($tasksObject->selectable === true) {
            echo '<a href = "'.$this->getEditUrl($tasksObject).'">'.$tasksObject->name.'</a>';
        }
        else{
            echo $tasksObject->name;
        }

        $numberOfSubtasks = count($tasksObject->children);

        if ( $numberOfSubtasks > 0 ){
            echo '<ul>';
            for ( $i = 0; $i < $numberOfSubtasks ;$i++){
                $this->displayTasks($tasksObject->children[$i]);
            }
            echo '</ul>';

        }
        echo '</li>';
        return;
    }
    
    /**
    * Generates the url to be used to continue to the createLogging-page
    * and returns it.
    * @param string url
    */
    public function getEditUrl($taskObject){
        $params = array(
                	'path' => '/webservice',
                	'method' => 'createLogging',
                	'taskId' => $taskObject->taskId,
                	'taskName' => $taskObject->name
        );
        
        return $this->getUrl($params);
    }
}