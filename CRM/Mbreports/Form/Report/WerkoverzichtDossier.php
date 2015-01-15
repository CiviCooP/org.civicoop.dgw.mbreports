<?php
set_time_limit(0);

/**
 * Util functions for mbreports
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Jan-Derek Vos (CiviCooP) <helpdesk@civicoop.org>
 * @date 12 May 2014
 * 
 * Copyright (C) 2014 CoÃ¶peratieve CiviCooP U.A. <http://www.civicoop.org>
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
        //'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier type'),
          //'operatorType' => CRM_Report_Form::OP_SELECT,
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'operator' => 'like',
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->caseTypes,
          'options' => $this->mbreportsConfig->caseTypes,
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'case_type_id',
        ),
        'order_bys' => array(),
      ),
      'case_sub_type' => array(
        'title' => ts('Dossier sub type'),
        'name' => 'subject',
        'filter_name' => 'case_sub_type_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'case_uitkomst' => array(
        'title' => ts('Dossier uitkomst'),
        'name' => 'subject',
        'filter_name' => 'case_uitkomst_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'case_melder' => array(
        'title' => ts('Dossier melder'),
        'name' => 'subject',
        'filter_name' => 'case_melder_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'case_status' => array(
        'title' => ts('Dossier status'),
        'name' => 'status_id',
        'filter_name' => 'case_status_op',
        //'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier status'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->caseStatus,
          'options' => $this->mbreportsConfig->caseStatus,
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
          'required' => TRUE,
          'dbAlias' => 'case_start_date_stamp',
        ),
        'order_bys' => array(
          'name' => 'start_date',
          'title' => ts('Dossier begindatum'),
          'alias' => 'start_date',
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
          'options' => $this->mbreportsConfig->deurwaarderList,
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'deurwaarder_id',
        ),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) ontruimt, ontruim id is 41
      /*'ontruiming' => array(
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
      ),*/
      'ontruiming_status' => array(
        'title' => ts('Ontruiming status'),
        'name' => 'ontruiming_status',
        'filter_name' => 'ontruiming_status_op',
        'filters' => array(
          'title' => ts('Ontruiming status '),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->activityStatus,
          'options' => $this->mbreportsConfig->activityStatus,
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
      // vonnis 
      'vonnis_deurwaarder_nr' => array(
        'title' => ts('Vonnis deurwaarder nr.'),
        'name' => 'vonnis_deurwaarder_nr',
        'filter_name' => 'vonnis_deurwaarder_nr_op',
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
      'street_address' => array(
        'title' => ts('Straat adres'),
        'name' => 'street_address',
        'filter_name' => 'street_address_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'property_complex_id' => array(
        'title' => ts('Complex'),
        'name' => 'complex_id',
        'filter_name' => 'property_complex_id_op',
        'filters' => array(
          'title' => ts('Complex'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->complexList,
          'options' => $this->mbreportsConfig->complexList,
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_complex_id',
        ),
        'order_bys' => array(
          'name' => 'complex_id',
          'title' => ts('Complex'),
          'alias' => 'complex_id',
        ),
      ),
      'property_city_region' => array(
        'title' => ts('Wijk'),
        'name' => 'block',
        'filter_name' => 'property_city_region_op',
        'filters' => array(
          'title' => ts('Wijk'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->wijkList,
          'options' => $this->mbreportsConfig->wijkList,
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_city_region',
        ),
        'order_bys' => array(
          'name' => 'block',
          'title' => ts('Wijk'),
          'alias' => 'block',
        ),
      ),
      'property_block' => array(
        'title' => ts('Buurt'),
        'name' => 'city_region',
        'filter_name' => 'property_block_op',
        'filters' => array(
          'title' => ts('Buurt'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->buurtList,
          'options' => $this->mbreportsConfig->buurtList,
          'type' => CRM_Utils_Type::T_STRING,
          'dbAlias' => 'property_block',
        ),
        'order_bys' => array(
          'name' => 'city_region',
          'title' => ts('Buurt'),
          'alias' => 'city_region',
        ),
      ),
      'property_vge_type' => array(
        'title' => ts('VGE type'),
        'name' => 'property_vge_type',
        'filter_name' => 'property_vge_type_op',
        'filters' => array(
          'title' => ts('VGE type'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'operator' => 'like',
          //'options' => array('' => ts('- select -')) + $this->mbreportsConfig->VgeTypeList,
          'options' => $this->mbreportsConfig->VgeTypeList,
          'type' => CRM_Utils_Type::T_INT,
          'dbAlias' => 'property_vge_type_id',
        ),
        'order_bys' => array(
          'name' => 'property_vge_type_id',
          'title' => ts('VGE type'),
          'alias' => 'property_vge_type_id',
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
      'hoofdhuurder_birth_date' => array(
        'title' => ts('Hoofdhuurder geboortedatum'),
        'name' => 'hoofdhuurder_birth_date',
        //'required' => FALSE,
        'filter_name' => 'hoofdhuurder_birth_date_op',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_street_address' => array(
        'title' => ts('Hoofdhuurder adres'),
        'name' => 'hoofdhuurder_street_address',
        //'required' => TRUE,
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
      'medehuurder_birth_date' => array(
        'title' => ts('Medehuurder geboortedatum'),
        'name' => 'medehuurder_birth_date',
        //'required' => FALSE,
        'filter_name' => 'medehuurder_birth_date_op',
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
        
    // check if it`s relative or it has a start and end date
    if(!empty($this->_formValues['case_start_date_relative']) or (!empty($this->_formValues['case_start_date_from']) and !empty($this->_formValues['case_start_date_to']))) { // if not empty add to filter
      $filter = array(
        'operatorType' => CRM_Report_Form::OP_DATE,
        'relative' => $this->_formValues['case_start_date_relative'],
        'from' => $this->_formValues['case_start_date_from'],
        //'from_display' => $this->_formValues['case_start_date_from_display'],
        'to' => $this->_formValues['case_start_date_to'],
        //'to_display' => $this->_formValues['case_start_date_to_display'],
        'field' => 'civicrm_case.start_date',
      );
      
      if(isset($this->_formValues['case_start_date_from_display'])){
        $filter['from_display'] = $this->_formValues['case_start_date_from_display'];
      }
      
      if(isset($this->_formValues['case_start_date_to_display'])){
        $filter['to_display'] = $this->_formValues['case_start_date_to_display'];
      }

      $clause = $this->dateClause($filter['field'], $filter['relative'], $filter['from'], $filter['to'], CRM_Utils_Type::T_DATE);      
      $this->_where .= " AND ( " . $clause . " ) ";

    }
  }
  
  function orderBy() {
    $this->_orderBy = "ORDER BY case_id ASC";
  }
  
  function postProcess() {
    /*set_time_limit(0);
    @ini_set('memory_limit', '128M');*/
    
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
    $this->formFields = $this->_formValues['fields'];
            
    /*
     * add field at orderby
     * add field at groupby
     * add field if it exists in order by
     */
    foreach($this->_formValues['order_bys'] as $key => $order_bys){
      if('-' != $order_bys['column']){ // if orderby is not empty
        $this->formOrderBy[$order_bys['column']] = $order_bys; // add field at orderby

        if(isset($order_bys['section']) and $order_bys['section']){
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
    foreach($this->_formValues as $filter => $value){
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
            
            if('property_vge_type' == $field){
              $filter_name = 'property_vge_type_id';
            }
            
            // add field at filter  
            if(CRM_Report_Form::OP_DATE == $values['filters']['operatorType']){ // OP_DATE
                
              // check if it`s relative or it has a start and end date
              if(!empty($this->_formValues[$field . '_relative']) or (!empty($this->_formValues[$field . '_from']) and !empty($this->_formValues[$field . '_to']))) { // if not empty add to filter
                $this->formFilter[$filter_name] = array(
                  'operatorType' => $values['filters']['operatorType'],
                  'relative' => $this->_formValues[$field . '_relative'],
                  'from' => $this->_formValues[$field . '_from'],
                  //'from_display' => $this->_formValues[$field . '_from_display'],
                  'to' => $this->_formValues[$field . '_to'],
                  //'to_display' => $this->_formValues[$field . '_to_display'],
                  'field' => $values['filters'],
                );
                
                if(isset($this->_formValues[$field . '_from_display'])){
                  $this->formFilter[$filter_name]['from_display'] = $this->_formValues[$field . '_from_display'];
                }

                if(isset($this->_formValues[$field . '_to_display'])){
                  $this->formFilter[$filter_name]['to_display'] = $this->_formValues[$field . '_to_display'];
                }
                
                if(!isset($this->formFields[$field])){ // add field if it exists in filter
                  $this->formFields[$field] = true; 
                }
              }

            }else {

              if(!empty($this->_formValues[$field . '_value'])){ // if not empty add to filter
                $this->formFilter[$filter_name] = array(
                  'operatorType' => $values['filters']['operatorType'],
                  'op' => $this->_formValues[$field . '_op'],
                  'value' => $this->_formValues[$field . '_value'],
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
    foreach($this->fields as $field => $values){
      if(isset($this->formFields[$field])){
        $this->_columnHeaders[$field] = array('title' => $values['title']);
      }
    }
  }
  
  public function buildRows($sql, &$rows) {
    //set_time_limit(0);
        
    /*
     * create temporary table to for case and additional data
     */
    $this->createTempTable(); 
    $this->truncateTempTable();
    
    $daoTemp = CRM_Core_DAO::executeQuery($sql);
      
    /*
     * get case type ids
     */
    $case_types = $this->mbreportsConfig->caseTypes;

    $overlast_id = array_search('Overlast', $case_types); 
    $woonfraude_id = array_search('Woonfraude', $case_types); 
    $actienavonnis_id = array_search('ActienaVonnis', $case_types); 
    
    /*
     * get list ov_types, wf_types, wf_uitkomst, wf_melder and anv_uitkomst
     */
    $ov_types = $this->mbreportsConfig->ovTypeList;
    $wf_types = $this->mbreportsConfig->wfTypeList;
    $wf_uitkomst = $this->mbreportsConfig->wfUitkomstList;
    
    /*
     * add records to temporary table
     */
    while ($daoTemp->fetch()) {
      /*
       * get household and hoofdhuurder
       */
      // check if it is a household
      $household = array();
      $hoofdhuurder = array();
      $sql = "SELECT civicrm_contact.id, civicrm_contact.contact_type FROM civicrm_contact
        WHERE civicrm_contact.id = '" . $daoTemp->case_contact_id . "' 
        LIMIT 1";

      $dao = CRM_Core_DAO::executeQuery($sql);
      $dao->fetch();
            
      if($dao->N){

        if('Household' == $dao->contact_type){
          $household = $dao;
          
          // get hoofdhuurder from household
          $sql = "SELECT civicrm_contact.id, civicrm_contact.contact_type FROM civicrm_contact
            LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id

            WHERE civicrm_relationship.contact_id_b = '" . $daoTemp->case_contact_id . "'
            AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->hoofdhuurderRelationshipTypeId . "'
            AND civicrm_relationship.is_active = '1'
            LIMIT 1";

          $dao = CRM_Core_DAO::executeQuery($sql);
          $dao->fetch();
          
          if($dao->N){
            $hoofdhuurder = $dao;
          }
          
          
        }else if ('Individual' == $dao->contact_type){
          $hoofdhuurder = $dao;
          
          $sql = "SELECT civicrm_contact.id, civicrm_contact.contact_type FROM civicrm_contact

            LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id

            WHERE civicrm_relationship.contact_id_a = '" . $daoTemp->case_contact_id . "'
            AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->hoofdhuurderRelationshipTypeId . "'
            AND civicrm_relationship.is_active = '1'
            LIMIT 1";
          
          $dao = CRM_Core_DAO::executeQuery($sql);
          $dao->fetch();
          
          if($dao->N){
            if('Household' == $dao->contact_type){
              $household = $dao;
            }
          }
        }
      }      
      
      $case_sub_types = array();
      $case_uitkomst = array();
      $case_melder = array();
      
      $case_type_ids = array();
      $case_type_ids = explode(CRM_Core_DAO::VALUE_SEPARATOR, $daoTemp->case_type_id);
      
      foreach($case_type_ids as $case_type_id){
        
        /*
         * set overlast
         */
        if($overlast_id == $case_type_id){          
          // get all entity_id`s and ov_types`s
          $sql = "SELECT entity_id, ov_type FROM " . $this->mbreportsConfig->ovCustomTableName . " WHERE entity_id = '" . $daoTemp->case_id . "'";
          $dao = CRM_Core_DAO::executeQuery($sql);
          
          // get all labels by ov_type
          while ($dao->fetch()) {            
            $ov_typess = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->ov_type);
            
            foreach($ov_typess as $key => $ov_type){
              if(!empty($ov_type)){
                $case_sub_types[] = $ov_types[$ov_type];
              }
            }
          }

          unset($sql);
          unset($dao);

        }
      
        /*
         * set woonfraude type, uitkomst, melder
         */
        if($woonfraude_id == $case_type_id){
          $sql = "SELECT wf_type, wf_uitkomst FROM " . $this->mbreportsConfig->wfUitkomstCustomTableName . "
            LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = " . $this->mbreportsConfig->wfUitkomstCustomTableName . ".entity_id
            LEFT JOIN civicrm_activity ON civicrm_activity.id = " . $this->mbreportsConfig->wfUitkomstCustomTableName . ".entity_id
            WHERE civicrm_case_activity.case_id =  '" . $daoTemp->case_id . "'
            ORDER BY civicrm_activity.activity_date_time DESC LIMIT 1";
                    
          $dao = CRM_Core_DAO::executeQuery($sql);        
          
          // get all labels by wf_type
          while ($dao->fetch()) {
            /*
             * get woonfraude type, uitkomst
             */
            // woonfraude type
            $wf_typess = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->wf_type);
            
            foreach($wf_typess as $key => $wf_type){
              if(!empty($wf_type) and !empty($wf_types[$wf_type])){
                $case_sub_types[] = $wf_types[$wf_type];
              }
            }

            // woonfraude uitkomst            
            if(!empty($dao->wf_uitkomst) and !empty($wf_uitkomst[$dao->wf_uitkomst])){
              $case_uitkomst[] = $wf_uitkomst[$dao->wf_uitkomst];
            }
          }
          
          unset($sql);
          unset($dao);
          
          // woonfraude melder
          $sql = "SELECT wf_melder FROM " . $this->mbreportsConfig->wfMelderCustomTableName . " WHERE entity_id = '" . $daoTemp->case_id . "'";
          $dao = CRM_Core_DAO::executeQuery($sql); 
          while ($dao->fetch()) { 
            if(!empty($dao->wf_melder)){
              $case_melder[] = $dao->wf_melder;
            }
          }
        }
        
        /*
         * set actie na vonnis uitkomst
         */
        if($actienavonnis_id == $case_type_id){
          $sql = "SELECT " . $this->mbreportsConfig->wfUitkomstActieNaVonnisCustomFieldName . " FROM " . $this->mbreportsConfig->wfUitkomstCustomTableName . "
            LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = " . $this->mbreportsConfig->wfUitkomstCustomTableName . ".entity_id
            LEFT JOIN civicrm_activity ON civicrm_activity.id = " . $this->mbreportsConfig->wfUitkomstCustomTableName . ".entity_id
            WHERE civicrm_case_activity.case_id =  '" . $daoTemp->case_id . "'
            ORDER BY civicrm_activity.activity_date_time DESC LIMIT 1";
          
          $dao = CRM_Core_DAO::executeQuery($sql);         
                     
          // get all labels by wf_type
          while ($dao->fetch()) {
            // actie na vonis uitkomst            
            if(!empty($dao->{$this->mbreportsConfig->wfUitkomstActieNaVonnisCustomFieldName})){
              $case_uitkomst[] = $dao->{$this->mbreportsConfig->wfUitkomstActieNaVonnisCustomFieldName};
            }
          }
          
          unset($sql);
          unset($dao);
        }
      }
            
      /*
       * insert case
       */
      $sql = "INSERT INTO werkoverzicht_dossier 
        (case_id, case_subject, case_type_id, case_case_type, case_sub_type, case_uitkomst, case_melder, case_status_id, case_status, case_start_date_stamp, case_start_date, case_contact_id)
        VALUES ('" . $daoTemp->case_id . "', '" . addslashes($daoTemp->case_subject) . "', '" . $daoTemp->case_type_id . "', 
          '" . addslashes($daoTemp->case_case_type) . "', '" . addslashes(implode(',', $case_sub_types)) . "', '" . addslashes(implode(',', $case_uitkomst)) . "',
          '" . addslashes(implode(',', $case_melder)) . "', '" . $daoTemp->case_status_id . "', '" . addslashes($daoTemp->case_status) . "', 
          '" . str_replace('-', '', $daoTemp->case_start_date) . "', '" . $daoTemp->case_start_date . "', '" . $daoTemp->case_contact_id . "' )";
      
      CRM_Core_DAO::executeQuery($sql);
      
      /*
      * add ontruiming to temporary table
      * one ontruiming at the time
      */
      if((isset($this->formFields['ontruiming']) and $this->formFields['ontruiming']) or (isset($this->formFields['ontruiming_status']) and $this->formFields['ontruiming_status'])
      or (isset($this->formFields['ontruiming_activity_date_time']) and $this->formFields['ontruiming_activity_date_time'])){
        $this->addTempOntruiming($daoTemp);
      }
      
      /*
      * add vonnis to temporary table
      * one vonnis at the time
      */
      if((isset($this->formFields['vonnis']) and $this->formFields['vonnis']) or (isset($this->formFields['vonnis_activity_date_time']) and $this->formFields['vonnis_activity_date_time'])){
        $this->addTempVonnis($daoTemp);
      } 
      
      /*
      * add vge to temporary table
      * one vge at the time
      */
      if((isset($this->formFields['property_vge_id']) and $this->formFields['property_vge_id']) or (isset($this->formFields['property_complex_id']) and $this->formFields['property_complex_id'])
      or (isset($this->formFields['property_block']) and $this->formFields['property_block']) or (isset($this->formFields['property_city_region']) and $this->formFields['property_city_region'])
      or (isset($this->formFields['property_vge_type']) and $this->formFields['property_vge_type'])){
        $this->addTempVge($daoTemp, $hoofdhuurder);
      }
      
      /*
      * add hoofdhuurder to temporary table
      * one hoofdhuurder at the time
      */
      if((isset($this->formFields['hoofdhuurder']) and $this->formFields['hoofdhuurder']) or (isset($this->formFields['hoofdhuurder_street_address']) and $this->formFields['hoofdhuurder_street_address'])
      or (isset($this->formFields['hoofdhuurder_email']) and $this->formFields['hoofdhuurder_email']) or (isset($this->formFields['hoofdhuurder_phone']) and $this->formFields['hoofdhuurder_phone'])){
        $this->addTempHoofdhuurder($daoTemp, $hoofdhuurder);
      }
      
      /*
      * add medehuurder to temporary table
      * one medehuurder at the time
      */
      if((isset($this->formFields['medehuurder']) and $this->formFields['medehuurder']) or (isset($this->formFields['medehuurder_email']) and $this->formFields['medehuurder_email'])
      or isset($this->formFields['medehuurder_phone']) and $this->formFields['medehuurder_phone']){
        $this->addTempMedehuurder($daoTemp, $household);
      }
    }
    
    unset($sql);
    unset($daoTemp);
    
    /*
    * add dossiermanager to temporary table
    * all dossiermanagers at once
    */
    if(isset($this->formFields['dossiermanager']) and $this->formFields['dossiermanager']){
      $this->addTempDossiermanager();
    }
    
    /*
    * add deurwaarders to temporary table
    * all deurwaarders at once
    */
    if(isset($this->formFields['deurwaarder']) and $this->formFields['deurwaarder']){
      $this->addTempDeurwaarder();
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
        
        if('case_type_id' == $field){          
          if(!is_array($filter['value'])){
            $where .= " ( " . $field . " LIKE CONCAT ('%" . CRM_Core_DAO::VALUE_SEPARATOR . "'," . $filter['value'] . ",'" . CRM_Core_DAO::VALUE_SEPARATOR . "%') ) AND ";
          }else {
            $where .= " ( ";

            $clause = array();
            foreach($filter['value'] as $key => $value){
              switch($filter['op']){
                case 'notin':
                  $clause[] = " ( " . $field . " NOT LIKE CONCAT ('%" . CRM_Core_DAO::VALUE_SEPARATOR . "'," . $value . ",'" . CRM_Core_DAO::VALUE_SEPARATOR . "%') ) ";
                  break;
                default:
                  $clause[] = " ( " . $field . " LIKE CONCAT ('%" . CRM_Core_DAO::VALUE_SEPARATOR . "'," . $value . ",'" . CRM_Core_DAO::VALUE_SEPARATOR . "%') ) ";
              }
            }

            switch($filter['op']){
              case 'notin':
                $where .= implode(" AND ", $clause);
                break;
              default:
                $where .= implode(" OR ", $clause);
            }

            $where .= " ) AND ";
          }
          
        }else if (CRM_Report_Form::OP_DATE == $filter['operatorType']) {
          $clause = $this->dateClause($field, $filter['relative'], $filter['from'], $filter['to'], CRM_Utils_Type::T_DATE);
          $where .= " ( " . $clause . " ) AND ";
          
        }else {
          $clause = $this->whereClause($filter['field'], $filter['op'], $filter['value'], $filter['min'], $filter['max']);
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
        
    unset($this->fields);
    unset($this->formFields);
    unset($this->formFilter);
    unset($this->formOrderBy);
    unset($this->formGroupBy);
    unset($this->mbreportsConfig);
    
    $rows = array();
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $row = array();
      foreach($this->_columnHeaders as $field => $title){
        
        switch($field){
          case 'case_start_date':
          case 'vonnis_activity_date_time':
          case 'hoofdhuurder_birth_date':
          case 'medehuurder_birth_date':
            if(empty($dao->$field)){
              $row[$field] = $dao->$field;
            }else {
              $row[$field] = date('d-m-Y', strtotime($dao->$field));
            }
            break;
            
          case 'ontruiming_activity_date_time':  
            if(empty($dao->$field)){
              $row[$field] = $dao->$field;
            }else {
              $row[$field] = date('d-m-Y H:i', strtotime($dao->$field));
            }
            break;
            
          default:
            $row[$field] = $dao->$field;
        }
      }
      
      $rows[] = $row;
      unset($row);
    }
    
    unset($sql);
    unset($dao);
  }
    
  private function createTempTable(){
    /*$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS werkoverzicht_dossier (*/  
    /*$sql = "CREATE TABLE IF NOT EXISTS werkoverzicht_dossier (*/
    
    $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS werkoverzicht_dossier (
      case_id INT(11),
      case_subject VARCHAR(128),
      case_type_id VARCHAR(128),
      case_case_type VARCHAR(128),
      case_sub_type VARCHAR(128),
      case_uitkomst VARCHAR(128),
      case_melder VARCHAR(128),
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
      vonnis_deurwaarder_nr VARCHAR(25), 
      vonnis_activity_date_time DATETIME,
      property_vge_id INT(11),
      street_address VARCHAR(96),
      property_complex_id VARCHAR(45),
      property_block VARCHAR(128),
      property_city_region VARCHAR(128),
      property_vge_type_id INT(11),
      property_vge_type VARCHAR(128),
      hoofdhuurder_id INT(11),
      hoofdhuurder VARCHAR(128),
      hoofdhuurder_birth_date DATE,
      hoofdhuurder_street_address VARCHAR(96),
      hoofdhuurder_email VARCHAR(64),
      hoofdhuurder_phone VARCHAR(32),
      medehuurder_id INT(11),
      medehuurder VARCHAR(128),
      medehuurder_birth_date DATE,
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
  
  private function addTempDossiermanager(){
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_relationship.case_id FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->dossierManagerRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1' ";
    
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
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->deurwaarderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1'";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET deurwaarder = '" . addslashes($dao->sort_name) . "', deurwaarder_id = '" . $dao->id . "'
        WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempOntruiming($daoTemp){    
    $sql = "SELECT (CASE WHEN 1 = status_id THEN 'J' ELSE 'N' END) AS ontruiming, civicrm_activity.status_id, civicrm_case_activity.case_id, civicrm_activity.activity_date_time, civicrm_option_value.label FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_option_value ON civicrm_option_value.value = civicrm_activity.status_id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->ontruimingActTypeId . "'
      AND civicrm_option_value.option_group_id = '" . $this->mbreportsConfig->activityStatusTypeOptionGroupId . "'
      AND civicrm_activity.is_current_revision = '1' 
      AND civicrm_case_activity.case_id = '" . $daoTemp->case_id . "'
      ORDER BY civicrm_activity.activity_date_time DESC LIMIT 1";
        
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET ontruiming = '" . $dao->ontruiming . "', ontruiming_status_id = '" . $dao->status_id . "', ontruiming_status= '" . addslashes($dao->label) . "', ontruiming_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempVonnis($daoTemp){
    $sql = "SELECT " . $this->mbreportsConfig->vongegeCustomTableName . "." . $this->mbreportsConfig->vongegeDeurCustomFieldName . " AS vonnis_deurwaarder_nr, civicrm_case_activity.case_id, civicrm_activity.activity_date_time FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      LEFT JOIN " . $this->mbreportsConfig->vongegeCustomTableName . " ON " . $this->mbreportsConfig->vongegeCustomTableName . ".entity_id = civicrm_activity.id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->vonnisActTypeId . "'
      AND civicrm_activity.is_current_revision = '1' 
      AND civicrm_case_activity.case_id = '" . $daoTemp->case_id . "'
      ORDER BY civicrm_activity.activity_date_time DESC LIMIT 1";
        
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {      
      $sql = "UPDATE werkoverzicht_dossier SET vonnis_deurwaarder_nr = '" . $dao->vonnis_deurwaarder_nr . "', vonnis_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
    
    unset($sql);
    unset($dao);
  }
  
  private function addTempVge($daoTemp, $hoofdhuurder)
  {
    $caseVgeData = CRM_Utils_MbreportsUtils::getCaseVgeData($daoTemp->case_id);
    
    $sql = "SELECT civicrm_property.vge_id, civicrm_property.vge_street_name, civicrm_property.vge_street_number, civicrm_property.complex_id, civicrm_property.block, civicrm_property.city_region, civicrm_property.vge_type_id, civicrm_property_type.label AS vge_type FROM civicrm_property
      LEFT JOIN civicrm_property_type ON civicrm_property_type.id = civicrm_property.vge_type_id
      WHERE civicrm_property.vge_id = '" . $caseVgeData['vge_nummer_first_6'] . "' ";
     
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
        
    if($dao->N){
      // update street_address from vge and vge data
      $sql = "UPDATE werkoverzicht_dossier SET property_vge_id = '" . $dao->vge_id . "', street_address = '" . $dao->vge_street_name . " " . $dao->vge_street_number . "', property_complex_id = '" . $dao->complex_id . "',
        property_block = '" . $dao->block . "', property_city_region = '" . $dao->city_region . "', property_vge_type_id = '" . $dao->vge_type_id . "', 
        property_vge_type = '" . $dao->vge_type . "'      
        WHERE case_id = '" . $daoTemp->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
      
    }else {
      // het is niet meer nodig om het contact adres op te halen als er geen vge adres is
      /*// get street address from contact
      $sql = "SELECT street_address FROM civicrm_address WHERE contact_id = '" . $hoofdhuurder->id . "' AND is_primary = '1' LIMIT 1";
      $dao = CRM_Core_DAO::executeQuery($sql);
      $dao->fetch();
      
      // update street_address
      $sql = "UPDATE werkoverzicht_dossier SET street_address = '" . $dao->street_address . "' WHERE case_id = '" . $daoTemp->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);*/
    }
    
    unset($caseVgeData);
    unset($sql);
    unset($dao);
  }
  
  private function addTempHoofdhuurder($daoTemp, $hoofdhuurder){ 
    if(empty($hoofdhuurder)){
      return false;
    }
        
    // get id, name and birth date hoofdhuurder
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_contact.birth_date FROM civicrm_contact
      WHERE civicrm_contact.id = '" . $hoofdhuurder->id . "' LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $sortName = CRM_Core_DAO::escapeString($dao->sort_name);
      $sql = "UPDATE werkoverzicht_dossier SET hoofdhuurder_id =  '" . $dao->id . "', hoofdhuurder = '" . $sortName . "', hoofdhuurder_birth_date = '" . $dao->birth_date . "'
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }

    unset($sql);
    unset($dao);
    
    // get address hoofdhuurder
    $sql = "SELECT civicrm_address.street_address FROM civicrm_contact
      LEFT JOIN civicrm_address ON civicrm_address.contact_id = civicrm_contact.id
      WHERE civicrm_contact.id = '" . $hoofdhuurder->id . "'
      ORDER BY civicrm_address.is_primary DESC LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $streetAddress = CRM_Core_DAO::escapeString($dao->street_address);
      $sql = "UPDATE werkoverzicht_dossier SET hoofdhuurder_street_address = '" . $streetAddress . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }

    unset($sql);
    unset($dao);
    
    // get email hoofdhuurder
    $sql = "SELECT civicrm_email.email FROM civicrm_contact
      LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id 

      WHERE civicrm_contact.id = '" . $hoofdhuurder->id . "' 
      ORDER BY civicrm_email.is_primary DESC LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    // get address

    if($dao->N){
      $sql = "UPDATE werkoverzicht_dossier SET hoofdhuurder_email = '" . $dao->email . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }

    unset($sql);
    unset($dao);
    
    // get phone hoofdhuurder
    $sql = "SELECT civicrm_phone.phone FROM civicrm_contact
      LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id

      WHERE civicrm_contact.id = '" . $hoofdhuurder->id . "'
      ORDER BY civicrm_phone.is_primary DESC LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $sql = "UPDATE werkoverzicht_dossier SET hoofdhuurder_phone = '" . $dao->phone . "'
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }

    unset($sql);
    unset($dao);
  }
  
  private function addTempMedehuurder($daoTemp, $household){
    if(empty($household)){
      return false;
    }
    
    // get id, name and birth date from medehuurder by household
    $sql = "SELECT civicrm_contact.id, civicrm_contact.sort_name, civicrm_contact.birth_date FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id

      WHERE civicrm_relationship.contact_id_b = '" . $household->id . "' 
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->medehuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1' LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $sortName = CRM_Core_DAO::escapeString($dao->sort_name);
      $sql = "UPDATE werkoverzicht_dossier SET medehuurder_id =  '" . $dao->id . "', medehuurder = '" . $sortName . "', 
        medehuurder_birth_date = '" . $dao->birth_date . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }
        
    unset($sql);
    unset($dao);
    
    // get email from medehuurder by household
    $sql = "SELECT civicrm_email.email FROM civicrm_contact
      LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id

      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id

      WHERE civicrm_relationship.contact_id_b = '" . $household->id . "' 
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->medehuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1'
      ORDER BY civicrm_email.is_primary DESC LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $sql = "UPDATE werkoverzicht_dossier SET medehuurder_email = '" . $dao->email . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }
        
    unset($sql);
    unset($dao);
    
    // get phone from medehuurder by household
    $sql = "SELECT civicrm_phone.phone FROM civicrm_contact
      LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id

      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_a = civicrm_contact.id

      WHERE civicrm_relationship.contact_id_b = '" . $household->id . "' 
      AND civicrm_relationship.relationship_type_id = '" .  $this->mbreportsConfig->medehuurderRelationshipTypeId . "'
      AND civicrm_relationship.is_active = '1'
      ORDER BY civicrm_phone.is_primary DESC LIMIT 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    
    if($dao->N){
      $sql = "UPDATE werkoverzicht_dossier SET medehuurder_phone = '" . $dao->phone . "'
        WHERE case_id = '" . $daoTemp->case_id . "'";

      CRM_Core_DAO::executeQuery($sql);
    }
        
    unset($sql);
    unset($dao);
  }
}