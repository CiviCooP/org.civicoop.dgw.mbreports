<?php
/**
 * Util functions for mbreports
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Jan-Derek Vos (CiviCooP) <helpdesk@civicoop.org>
 * @date 12 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */

class CRM_Mbreports_Form_Report_WerkoverzichtDossier extends CRM_Report_Form {
  
  protected $fields = array();
  
  protected $formFields = array();
  protected $formFilter = array();
  protected $formOrderBy = array();
  protected $formGroupBy = array();
  
  protected $mbreportsConfig = array();       
  
  function __construct() {
    $this->mbreportsConfig = CRM_Mbreports_Config::singleton();
    
    $this->fields = array
    (
      'case_id' => array(
        'title' => ts('Dossier ID'),
        'name' => 'id',
        'filter_name' => 'case_id',
        'required' => TRUE,
        'filters' => array(),
        'order_bys' => array(
          'name' => 'id',
          'title' => ts('Dossier ID'),
          'alias' => 'id',
        ),
      ),
      'case_subject' => array(
        'title' => ts('Dossier onderwerp'),
        'name' => 'subject',
        'filter_name' => 'case_subject',
        'required' => TRUE,
        'filters' => array(),
        'order_bys' => array(),
      ),
      'case_case_type' => array(
        'title' => ts('Dossier type'),
        'name' => 'case_type',
        'filter_name' => 'case_case_type_op',
        'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier type'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->caseTypes),
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'case_type_id',
        ),
        'order_bys' => array(),
      ),
      'case_status' => array(
        'title' => ts('Dossier status'),
        'name' => 'status_id',
        'filter_name' => 'case_status_op',
        'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier status'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->caseStatus),
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'case_status_id',
        ),
        'order_bys' => array(
          'name' => 'status_id',
          'title' => ts('Dossier status'),
          'alias' => 'status_id',
        ),
      ),
      'case_start_date' => array(
        'title' => ts('Dossier begindatum'),
        'name' => 'start_date',
        'filter_name' => 'case_start_date_relative',
        'filters' => array(
          'title' => ts('Dossier begindatum'),
          'default'      => 'this.month',
          'operatorType' => CRM_Report_Form::OP_DATE,
          'type' => CRM_Utils_Type::T_DATE,
          'dbAlias' => 'case_start_date_stamp',
        ),
        'order_bys' => array(
          'name' => 'start_date',
          'title' => ts('Dossier begindatum'),
          'alias' => 'start_date',
        ),
      ),
      'typeringen' => array(
        'title' => ts('Typeringen'),
        'name' => 'typeringen',
        'filter_name' => 'typeringen_op',
        'filters' => array(),
        'order_bys' => array(
          'name' => 'typeringen',
          'title' => ts('Typeringen'),
          'alias' => 'typeringen',
        ),
      ),
      'dossiermanager' =>  array(
        'title' => ts('Dossiermanager'),
        'name' => 'dossiermanager',
        'filter_name' => 'dossiermanager_op',
        'filters' => array(
          'title' => ts('Dossiermanager'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $this->mbreportsConfig->dossierManagerList,
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'dossiermanager_id',
        ),
        'order_bys' => array(
          'name' => 'dossiermanager',
          'title' => ts('Dossiermanager'),
          'alias' => 'dossiermanager',
        ),
      ),
      'deurwaarder' => array(
        'title' => ts('Deurwaarder'),
        'name' => 'deurwaarder',
        'filter_name' => 'deurwaarder_op',
        'filters' => array(
          'title' => ts('Deurwaarder'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $this->mbreportsConfig->dossierManagerList,
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'deurwaarder_id',
        ),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) ontruimt, ontruim id is 41
      'ontruiming' => array(
        'title' => ts('Ontruiming'),
        'name' => 'ontruiming',
        'filter_name' => 'ontruiming_op',
        'filters' => array(
          'title' => ts('Ontruiming'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array('' => ts('- select -'), 'J' => ts('Ja'), 'N' => ts('Nee')),
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'ontruiming',
        ),
        'order_bys' => array(),
      ),
      'ontruiming_status' => array(
        'title' => ts('Ontruiming status'),
        'name' => 'ontruiming_status',
        'filter_name' => 'ontruiming_status_op',
        'filters' => array(
          'title' => ts('Ontruiming status '),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->activityStatus),
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'ontruiming_status_id',
        ),
        'order_bys' => array(),
      ),
      'ontruiming_activity_date_time' => array(
        'title' => ts('Ontruiming datum'),
        'name' => 'ontruiming_activity_date_time',
        'filter_name' => 'ontruiming_activity_date_time_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) vonnis, vonnis id = 40
      'vonnis' => array(
        'title' => ts('Vonnis'),
        'name' => 'vonnis',
        'filter_name' => 'vonnis_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'vonnis_activity_date_time' => array(
        'title' => ts('Vonnis datum'),
        'name' => 'vonnis_activity_date_time',
        'filter_name' => 'vonnis_activity_date_time_relative',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'property_vge_id' => array(
        'title' => ts('VGE nummer'),
        'name' => 'property_vge_id',
        'filter_name' => 'property_vge_id_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'property_complex_id' => array(
        'title' => ts('Complex'),
        'name' => 'complex_id',
        'filter_name' => 'property_complex_id_op',
        'filters' => array(
          'title' => ts('Complex'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->complexList),
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_complex_id',
        ),
        'order_bys' => array(
          'name' => 'complex_id',
          'title' => ts('Complex'),
          'alias' => 'complex_id',
        ),
      ),
      'property_block' => array(
        'title' => ts('Wijk'),
        'name' => 'block',
        'filter_name' => 'property_block_op',
        'filters' => array(
          'title' => ts('Wijk'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->wijkList),
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_block',
        ),
        'order_bys' => array(
          'name' => 'block',
          'title' => ts('Wijk'),
          'alias' => 'block',
        ),
      ),
      'property_city_region' => array(
        'title' => ts('Buurt'),
        'name' => 'city_region',
        'filter_name' => 'property_city_region_op',
        'filters' => array(
          'title' => ts('Buurt'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $city_regions,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->buurtList),
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_city_region',
        ),
        'order_bys' => array(
          'name' => 'city_region',
          'title' => ts('Buurt'),
          'alias' => 'city_region',
        ),
      ),
      'property_vge_type' => array(
        'title' => ts('VGE type'),
        'name' => 'vge_type_id',
        'filter_name' => 'property_vge_type_op',
        'filters' => array(
          'title' => ts('VGE type'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- select -')), $this->mbreportsConfig->VgeTypeList),
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'property_vge_type',
        ),
        'order_bys' => array(
          'name' => 'vge_type_id',
          'title' => ts('VGE type'),
          'alias' => 'vge_type_id',
        ),
      ),
      'hoofdhuurder' => array(
        'title' => ts('Hoofdhuurder naam'),
        'name' => 'hoofdhuurder',
        'required' => TRUE,
        'filter_name' => 'hoofdhuurder_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_street_address' => array(
        'title' => ts('Hoofdhuurder adres'),
        'name' => 'hoofdhuurder_street_address',
        'required' => TRUE,
        'filter_name' => 'hoofdhuurder_street_address_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_email' => array(
        'title' => ts('Hoofdhuurder e-mail'),
        'name' => 'hoofdhuurder_email',
        'filter_name' => 'hoofdhuurder_email_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_phone' => array(
        'title' => ts('Hoofdhuurder telefoon'),
        'name' => 'hoofdhuurder_phone',
        'filter_name' => 'hoofdhuurder_phone_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder' => array(
        'title' => ts('Medehuurder naam'),
        'name' => 'medehuurder',
        'filter_name' => 'medehuurder_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder_email' => array(
        'title' => ts('Medehuurder e-mail'),
        'name' => 'medehuurder_email',
        'filter_name' => 'medehuurder_email_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder_phone' => array(
        'title' => ts('Medehuurder telefoon'),
        'name' => 'medehuurder_phone',
        'filter_name' => 'medehuurder_phone_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
    );
    
    $this->setFields();
    $this->setFilters();
    $this->setOrderBys();
    
    parent::__construct();
  }
  
  private function setFields() {
    $this->_columns = array(
      'civicrm_case' =>
      array(
        'dao' => 'CRM_Case_DAO_Case',
        'fields' => array(),
      ),
    );
    
    foreach($this->fields as $field => $values){
      $this->_columns['civicrm_case']['fields'][$field] = array();
      foreach($values as $key => $value){
        if('filter_name' != $key and 'filters' != $key and 'order_bys' != $key){
          $this->_columns['civicrm_case']['fields'][$field][$key] = $value;
        }
      }
    }
  }
  
  private function setFilters(){
    $this->_columns['civicrm_case']['filters'] = array();
    
    foreach($this->fields as $field => $values){
      foreach($values as $key => $value){
             
        if('filters' == $key and !empty($value)){
          $this->_columns['civicrm_case']['filters'][$field] = array();
          foreach($value as $filters => $filter){
            $this->_columns['civicrm_case']['filters'][$field][$filters] = $filter;
          }
        }
      }
    }
  }
  
  private function setOrderBys(){
    $this->_columns['civicrm_case']['order_bys'] = array();
    
    foreach($this->fields as $field => $values){
      foreach($values as $key => $value){
             
        if('order_bys' == $key and !empty($value)){
          $this->_columns['civicrm_case']['order_bys'][$field] = array();
          foreach($value as $order_bys => $order_by){
            $this->_columns['civicrm_case']['order_bys'][$field][$order_bys] = $order_by;
          }
        }
      }
    }
  }
  
  function preProcess() {
    $this->assign('reportTitle', ts('Werkoverzicht dossier'));
    parent::preProcess();
  }
  
  function select() {
    $this->_select = "SELECT civicrm_case.id AS case_id, civicrm_case.case_type_id AS case_type_id, civicrm_case.status_id AS case_status_id, civicrm_case.subject AS case_subject, 
    (SELECT label FROM civicrm_option_value WHERE option_group_id = '" . $this->mbreportsConfig->caseTypeOptionGroupId . "' AND value = civicrm_case.case_type_id) AS case_case_type, 
    (SELECT label FROM civicrm_option_value WHERE option_group_id = '" . $this->mbreportsConfig->caseStatusOptionGroupId . "' AND value = civicrm_case.status_id) AS case_status, 
    civicrm_case.start_date AS case_start_date, civicrm_case_contact.contact_id AS case_contact_id ";
  }
  
  function from() {
    $this->_from = "FROM civicrm_case ";
    // case contact
    $this->_from .= "LEFT JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id ";
  }
  
  function where() {
    $this->_where = "WHERE civicrm_case.is_deleted = '0' ";
  }
  
  function orderBy() {
    $this->_orderBy = "ORDER BY case_id ASC";
  }
  
  function postProcess() {
    set_time_limit(0);
    
    $this->beginPostProcess();
    
    $this->setformFields();  
    
    $this->select();
    $this->from();
    $this->where();
    $this->orderBy();
    $sql = $this->_select.' '.$this->_from.' '.$this->_where. ' '.$this->_orderBy;
    
    $this->setColumnHeaders();
    
    $rows = array();
    $this->buildRows($sql, $rows);

    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }
  
  private function setformFields(){
    $this->formFields = $this->_submitValues['fields'];
    
    /*
     * add field at orderby
     * add field at groupby
     * add field if it exists in order by
     */
    foreach($this->_submitValues['order_bys'] as $key => $order_bys){
      if('-' != $order_bys['column']){ // if orderby is not empty
        $this->formOrderBy[$order_bys['column']] = $order_bys; // add field at orderby

        if($order_bys['section']){
          $this->formGroupBy[$order_bys['column']] = true;
        }
        
        if(!isset($this->formFields[$order_bys['column']])){ // add field if it exists in order by
          $this->formFields[$order_bys['column']] = true; 
        }
      }
    }
    
    /*
     * add field at filter
     * add field if it exists in filter
     */
    foreach($this->_submitValues as $filter => $value){
      if('qfKey' != $filter and '_qf_default' != $filter and 'fields' != $filter and 'order_bys' != $filter and '_qf_WerkoverzichtDossier_submit' != $filter){
        
        foreach($this->fields as $field => $values){
          if($filter == $values['filter_name']){ 
               
            $filter_name = $field;
            
            /*
             * diffrent filter field then the orginal field
             */
            if('case_case_type' == $field){
              $filter_name = 'case_type_id';
            }
            
            if('case_start_date' == $field){
              $filter_name = 'case_start_date_stamp';
            }
            
            if('case_status' == $field){
              $filter_name = 'case_status_id';
            }
            
            if('dossiermanager' == $field){
              $filter_name = 'dossiermanager_id';
            }
            
            if('deurwaarder' == $field){
              $filter_name = 'deurwaarder_id';
            }
            
            if('ontruiming_status' == $field){
              $filter_name = 'ontruiming_status_id';
            }
            
            // add field at filter  
            if(CRM_Report_Form::OP_DATE == $values['filters']['operatorType']){ // OP_DATE

              if('' != $this->_submitValues[$field . '_relative']) { // if not empty add to filter
                $this->formFilter[$filter_name] = array(
                  'operatorType' => $values['filters']['operatorType'],
                  'relative' => $this->_submitValues[$field . '_relative'],
                  'from' => $this->_submitValues[$field . '_from'],
                  'from_display' => $this->_submitValues[$field . '_from_display'],
                  'to' => $this->_submitValues[$field . '_to'],
                  'to_display' => $this->_submitValues[$field . '_to_display'],
                  'field' => $values['filters'],
                );

                if(!isset($this->formFields[$field])){ // add field if it exists in filter
                  $this->formFields[$field] = true; 
                }
              }

            }else {

              if('' != $this->_submitValues[$field . '_value']){ // if not empty add to filter
                $this->formFilter[$filter_name] = array(
                  'operatorType' => $values['filters']['operatorType'],
                  'op' => $this->_submitValues[$field . '_op'],
                  'value' => $this->_submitValues[$field . '_value'],
                  'field' => $values['filters'],
                  'min' => '',
                  'max' => '',
                );

                if(!isset($this->formFields[$field])){ // add field if it exists in filter
                  $this->formFields[$field] = true; 
                }
              }
            } 
          }
        }
      }
    }
  }
  
  private function setColumnHeaders(){
    foreach($this->formFields as $field => $boolean){
      $this->_columnHeaders[$field] = array('title' => $this->fields[$field]['title']);
    }
  }
  
  public function buildRows($sql, &$rows) {
    set_time_limit(0);
        
    /*
     * create temporary table to for case and additional data
     */
    $this->createTempTable(); 
    $this->truncateTempTable();
    
    $daoTemp = CRM_Core_DAO::executeQuery($sql);
    
    /*
     * add records to temporary table
     */
    while ($daoTemp->fetch()) {
      $sql = "INSERT INTO werkoverzicht_dossier 
        (case_id, case_subject, case_type_id, case_case_type, case_status_id, case_status, case_start_date_stamp, case_start_date, case_contact_id)
        VALUES ('" . $daoTemp->case_id . "', '" . addslashes($daoTemp->case_subject) . "', '" . $daoTemp->case_type_id . "', '" . addslashes($daoTemp->case_case_type) . "', '" . $daoTemp->case_status_id . "', '" . addslashes($daoTemp->case_status) . "', '" . str_replace('-', '', $daoTemp->case_start_date) . "', '" . $daoTemp->case_start_date . "', '" . $daoTemp->case_contact_id . "' )";
      
      CRM_Core_DAO::executeQuery($sql);
      
      /*
      * add vge to temporary table
      * one vge at the time
      */
      if($this->formFields['property_vge_id'] or $this->formFields['property_complex_id']
      or $this->formFields['property_block'] or $this->formFields['property_city_region']
      or $this->formFields['property_vge_type']){
        $this->addTempVge($daoTemp);
      }
      
      /*
      * add hoofdhuurder to temporary table
      * one hoofdhuurder at the time
      */
      if($this->formFields['hoofdhuurder'] or $this->formFields['hoofdhuurder_street_address'] 
      or $this->formFields['hoofdhuurder_email'] or $this->formFields['hoofdhuurder_phone']){
        $this->addTempHoofdhuurder($daoTemp);
      }
      
      /*
      * add medehuurder to temporary table
      * one medehuurder at the time
      */
      if($this->formFields['medehuurder'] or $this->formFields['medehuurder_email'] 
      or $this->formFields['medehuurder_phone']){
        $this->addTempMedehuurder($daoTemp);
      }
    }
    
    unset($sql);
    unset($daoTemp);
    
    /*
    * add typeringen to temporary table
    * all typeringen at once
    */
    if($this->formFields['typeringen']){
      $this->addTempTyperingen();
    }
    
    /*
    * add dossiermanager to temporary table
    * all dossiermanagers at once
    */
    if($this->formFields['dossiermanager']){
      $this->addTempDossiermanager();
    }
    
    /*
    * add deurwaarders to temporary table
    * all deurwaarders at once
    */
    if($this->formFields['deurwaarder']){
      $this->addTempDeurwaarder();
    }
    
    /*
    * add ontruiming to temporary table
    * all ontruiming at once
    */
    if($this->formFields['ontruiming'] or $this->formFields['ontruiming_status'] 
    or $this->formFields['ontruiming_activity_date_time']){
      $this->addTempOntruiming();
    }
    
    /*
    * add vonnis to temporary table
    * all vonnis at once
    */
    if($this->formFields['vonnis'] or $this->formFields['vonnis_activity_date_time']){
      $this->addTempVonnis();
    } 
        
    /*
     * now select records from temp and build row from them
     */
    $sql = "SELECT ";
    
    /*
     * add fields
     */
    $fields = "";
    foreach($this->formFields as $field => $boolean){
      $fields .= " `" . $field . "`, ";
    }
    
    $fields = substr($fields, 0, -2);
    $sql .= $fields;
    
    // from
    $sql .= " FROM werkoverzicht_dossier ";
    
    /*
     * add where
     */
    $where = "";
    if(!empty($this->formFilter)){
      $where = " WHERE ";
      foreach($this->formFilter as $field => $filter){
                
        if (CRM_Report_Form::OP_DATE == $filter['operatorType']) {
          $clause = $this->dateClause($field, $filter['relative'], $filter['from'], $filter['to'], CRM_Utils_Type::T_DATE);
          $where .= " ( " . $clause . " ) AND ";
          
        }else {
          $clause = $this->whereClause($filter['field'], $filter['op'], '\'' . $filter['value'] . '\'', $filter['min'], $filter['max']);
          $where .= $clause . " AND ";
        }
      }
    }
    
    $where = substr($where, 0, -4);
    $sql .= $where;
    
    /*
     * add group by
     */
    $groupby = "";
    if(!empty($this->formGroupBy)){
      $groupby = " GROUP BY ";
      foreach($this->formGroupBy as $field => $boolean){
        $groupby .= " " . $field . ", ";
      }
      
      $groupby = substr($groupby, 0, -2);
    }
    
    $sql .= $groupby;
    
    /*
     * add order by
     */
    $orderby = "";
    if(!empty($this->formOrderBy)){
      $orderby = " ORDER BY ";
      foreach($this->formOrderBy as $field => $order_by){
        $orderby .= " " . $order_by['column'] . " " . $order_by['order'] . ", ";
      }
      
      $orderby = substr($orderby, 0, -2);
    }
    
    $sql .= $orderby;
        
    echo('sql: ' . $sql) . '<br/>' . PHP_EOL;
    
    $rows = array();
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $row = array();
      foreach($this->_columnHeaders as $field => $title){
        $row[$field] = $dao->$field;
      }
      
      $rows[] = $row;
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function createTempTable(){
    $sql = "CREATE TABLE IF NOT EXISTS werkoverzicht_dossier (
      case_id INT(11),
      case_subject VARCHAR(128),
      case_type_id VARCHAR(128),
      case_case_type VARCHAR(128),
      case_status_id INT(10),
      case_status VARCHAR(225),
      case_start_date_stamp VARCHAR(255),
      case_start_date DATE,
      case_contact_id INT(11),
      typeringen VARCHAR(128),
      dossiermanager_id INT(11),
      dossiermanager VARCHAR(128),
      deurwaarder_id VARCHAR(11),
      deurwaarder VARCHAR(128),
      ontruiming VARCHAR(2),
      ontruiming_status_id INT(10),
      ontruiming_status VARCHAR(255),
      ontruiming_activity_date_time DATETIME, 
      vonnis VARCHAR(2), 
      vonnis_activity_date_time DATETIME,
      property_vge_id INT(11),
      property_complex_id VARCHAR(45),
      property_block VARCHAR(128),
      property_city_region VARCHAR(128),
      property_vge_type VARCHAR(128),
      hoofdhuurder_id INT(11),
      hoofdhuurder VARCHAR(128),
      hoofdhuurder_street_address VARCHAR(96),
      hoofdhuurder_email VARCHAR(64),
      hoofdhuurder_phone VARCHAR(32),
      medehuurder_id INT(11),
      medehuurder VARCHAR(128),
      medehuurder_email VARCHAR(64),
      medehuurder_phone VARCHAR(32))";
    
    CRM_Core_DAO::executeQuery($sql);
    
    unset($sql);
  }
  
  private function truncateTempTable(){
    $sql = "TRUNCATE TABLE werkoverzicht_dossier";
    CRM_Core_DAO::executeQuery($sql);
    
    unset($sql);
  }
  
  private function removeTempTable(){
    
  }
  
  private function addTempTyperingen(){
    $sql = "SELECT civicrm_value_ov_data.entity_id, civicrm_property_type.label FROM civicrm_value_ov_data
      LEFT JOIN civicrm_property_type ON civicrm_value_ov_data.ov_type LIKE CONCAT('%',civicrm_property_type.id,'%')";
    $dao = CRM_Core_DAO::executeQuery($sql);
        
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET typeringen = '" . addslashes($dao->label) . "' WHERE case_id = '" . $dao->entity_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempDossiermanager(){
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_relationship.case_id FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id
      AND civicrm_relationship.is_active = '1'
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->dossierManagerRelationshipTypeId . "'";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET dossiermanager = '" . addslashes($dao->sort_name) . "', dossiermanager_id = '" . $dao->id . "' 
        WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempDeurwaarder(){
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_relationship.case_id FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id
      AND civicrm_relationship.is_active = '1'
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->deurwaarderRelationshipTypeId . "'";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET deurwaarder = '" . addslashes($dao->sort_name) . "', deurwaarder_id = '" . $dao->id . "'
        WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempOntruiming(){    
    $sql = "SELECT (CASE WHEN 1 = status_id THEN 'J' ELSE 'N' END) AS ontruiming, civicrm_activity.status_id, civicrm_case_activity.case_id, civicrm_activity.activity_date_time, civicrm_option_value.label FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_option_value ON civicrm_option_value.value = civicrm_activity.status_id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->ontruimingActTypeId . "'
      AND civicrm_option_value.option_group_id = '" . $this->mbreportsConfig->activityStatusTypeOptionGroupId . "'
      AND civicrm_activity.is_current_revision = '1' 
      ORDER BY civicrm_activity.activity_date_time DESC LIMIT 1";
        
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET ontruiming = '" . $dao->ontruiming . "', ontruiming_status_id = '" . $dao->status_id . "', ontruiming_status= '" . addslashes($dao->label) . "', ontruiming_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempVonnis(){
    $sql = "SELECT (CASE WHEN status_id IS NULL THEN 'N' ELSE 'J' END) AS vonnis, civicrm_case_activity.case_id, civicrm_activity.activity_date_time FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->vonnisActTypeId . "'
      AND civicrm_activity.is_current_revision = '1' 
      ORDER BY civicrm_activity.activity_date_time DESC  LIMIT 1";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET vonnis = '" . $dao->vonnis . "', vonnis_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempVge($daoTemp)
  {
    $caseVgeData = CRM_Utils_MbreportsUtils::getCaseVgeData($daoTemp->case_id);
    
    $sql = "SELECT civicrm_property.vge_id, civicrm_property.complex_id, civicrm_property.block, civicrm_property.city_region, civicrm_property.vge_type_id, civicrm_property_type.label AS vge_type FROM civicrm_property
      LEFT JOIN civicrm_property_type ON civicrm_property_type.id = civicrm_property.vge_type_id
      WHERE civicrm_property.vge_id = '" . $caseVgeData['vge_nummer_first_6'] . "' ";
        
    $dao = CRM_Core_DAO::executeQuery($sql);    
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET property_vge_id = '" . $dao->vge_id . "', property_complex_id = '" . $dao->complex_id . "',
        property_block = '" . $dao->block . "', property_city_region = '" . $dao->city_region . "', property_vge_type = '" . $dao->vge_type . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($caseVgeData);
    unset($sql);
    unset($dao);
  }
  
  private function addTempHoofdhuurder($daoTemp){
    // check if it is a household
    $sql = "SELECT civicrm_contact.id, civicrm_contact.contact_type, civicrm_contact.sort_name, civicrm_address.street_address, civicrm_email.email, civicrm_phone.phone FROM civicrm_contact
      LEFT JOIN civicrm_address ON civicrm_address.contact_id = civicrm_contact.id
      LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id
      LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id
      WHERE civicrm_contact.id = '" . $daoTemp->case_contact_id . "' 
      ORDER BY civicrm_address.is_primary DESC, civicrm_phone.is_primary DESC, civicrm_email.is_primary DESC LIMIT 1";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    
    $dao->fetch();
    if('Household' == $dao->contact_type){
      // get hoofdhuurder from household
      $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_email.email, civicrm_phone.phone FROM civicrm_contact
      LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id
      LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id
      
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id

      WHERE civicrm_relationship.contact_id_b = '" . $daoTemp->case_contact_id . "'
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->hoofdhuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1'
      ORDER BY civicrm_phone.is_primary DESC, civicrm_email.is_primary DESC LIMIT 1";
      
      $dao = CRM_Core_DAO::executeQuery($sql);
      $dao->fetch();
    }
    
    $sql = "UPDATE werkoverzicht_dossier SET hoofdhuurder_id =  '" . $dao->id . "', hoofdhuurder = '" . $dao->sort_name . "', hoofdhuurder_street_address = '" . $dao->street_address . "',
      hoofdhuurder_email = '" . $dao->email . "', hoofdhuurder_phone = '" . $dao->phone . "'
      WHERE case_id = '" . $daoTemp->case_id . "'";
    
    CRM_Core_DAO::executeQuery($sql);
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempMedehuurder($daoTemp){
    // get hoofdhuurder id
    $sql = "SELECT hoofdhuurder_id FROM werkoverzicht_dossier WHERE case_id = '" . $daoTemp->case_id . "' LIMIT 1";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    // get household from hoofhuurder id
    $sql = "SELECT civicrm_relationship.contact_id_b FROM civicrm_relationship 
      WHERE civicrm_relationship.contact_id_a = '" . $dao->hoofdhuurder_id . "'
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->hoofdhuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1' LIMIT 1 ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    // get medehuurder from household
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_email.email, civicrm_phone.phone FROM civicrm_contact
      LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id
      LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id
      
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id
      
      WHERE civicrm_relationship.contact_id_b = '" . $dao->contact_id_b . "' 
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->medehuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1'
      ORDER BY civicrm_phone.is_primary DESC, civicrm_email.is_primary DESC LIMIT 1";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    $sql = "UPDATE werkoverzicht_dossier SET medehuurder_id =  '" . $dao->id . "', medehuurder = '" . $dao->sort_name . "', 
      medehuurder_email = '" . $dao->email . "', medehuurder_phone = '" . $dao->phone . "'
      WHERE case_id = '" . $daoTemp->case_id . "'";
    
    CRM_Core_DAO::executeQuery($sql);
    
    unset($sql);
    unset($dao);
  }
}