<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DojoController
 *
 * @author tsc
 */
    class DojoController extends Zend_Controller_Action
    {


        public function autocompleteAction()
        {
            $this->view->form = $this->getForm();
        }

        public function recordsAction()
        {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT ID, username FROM user';
            $result = $db->fetchAll($sql);
            $dojoData= new Zend_Dojo_Data('ID',$result,'username');
            echo $dojoData->toJson();
            exit;
        }

        public function getForm()
        {
           if (null === $this->_form) {
                $this->_form = new Zend_Form();
                $this->_form->setMethod('get')
                ->setAction(
                $this->getRequest()->getBaseUrl() . '/test/process'
                )
                ->addElements(array(
                'test' => array('type' => 'text', 'options' => array(
                'filters'        => array('StringTrim'),
                'dojoType'       => array('dijit.form.ComboBox'),
                'store'          => 'testStore',
                'autoComplete'   => 'false',
                'hasDownArrow'   => 'true',
                'label' => 'Your input:',
                )),
                'go' => array('type' => 'submit',
                'options' => array('label' => 'Go!'))
                ));
            }
            return $this->_form;
        }
    }
?>
