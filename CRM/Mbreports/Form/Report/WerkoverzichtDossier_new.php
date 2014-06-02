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
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->caseTypes),
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
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->caseStatus),
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
        'filter_name' => 'case_start_date',
        'filters' => array(
          'title' => ts('Dossier begindatum'),
          'default'      => 'this.month',
          'operatorType' => CRM_Report_Form::OP_DATE,
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
        'filter_name' => 'CA_case_type_id',
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
        'filter_name' => 'DOSS_dossiermanager',
        'filters' => array(
          'title' => ts('Dossiermanager'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $this->mbreportsConfig->dossierManagerList,
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
        'filter_name' => 'DEUR_deurwaarder',
        'filters' => array(
          'title' => ts('Deurwaarder'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $this->mbreportsConfig->dossierManagerList,
        ),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) ontruimt, ontruim id is 41
      'ontruiming' => array(
        'title' => ts('Ontruiming'),
        'name' => 'ontruiming',
        'filter_name' => 'ONT_ontruiming',
        'filters' => array(
          'title' => ts('Ontruiming'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array('' => ts('- elke - '), 'J' => ts('Ja'), 'N' => ts('Nee')),
        ),
        'order_bys' => array(),
      ),
      'ontruiming_status' => array(
        'title' => ts('Ontruiming status'),
        'name' => 'ontruiming_status',
        'filter_name' => 'ONT_status',
        'filters' => array(
          'title' => ts('Ontruiming status '),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->activityStatus),
        ),
        'order_bys' => array(),
      ),
      'ontruiming_activity_date_time' => array(
        'title' => ts('Ontruiming datum'),
        'name' => 'ontruiming_activity_date_time',
        'filter_name' => 'ONT_activity_date_time',
        'filters' => array(),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) vonnis, vonnis id = 40
      'vonnis' => array(
        'title' => ts('Vonnis'),
        'name' => 'vonnis',
        'filter_name' => 'VONN_vonnis',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'vonnis_activity_date_time' => array(
        'title' => ts('Vonnis datum'),
        'name' => 'vonnis_activity_date_time',
        'filter_name' => 'vonnis_activity_date_time',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'property_vge_id' => array(
        'title' => ts('VGE nummer'),
        'name' => 'property_vge_id',
        'filter_name' => 'property_vge_id',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'property_complex_id' => array(
        'title' => ts('Complex'),
        'name' => 'complex_id',
        'filter_name' => 'property_complex_id',
        'filters' => array(
          'title' => ts('Complex'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->complexList),
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
        'filter_name' => 'property_block',
        'filters' => array(
          'title' => ts('Wijk'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->wijkList),
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
        'filter_name' => 'property_city_region',
        'filters' => array(
          'title' => ts('Buurt'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $city_regions,
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->buurtList),
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
        'filter_name' => 'property_vge_type',
        'filters' => array(
          'title' => ts('VGE type'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array_merge(array('' => ts('- elke - ')), $this->mbreportsConfig->VgeTypeList),
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
        'filter_name' => 'hoofdhuurder',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_street_address' => array(
        'title' => ts('Hoofdhuurder adres'),
        'name' => 'hoofdhuurder_street_address',
        'required' => TRUE,
        'filter_name' => 'hoofdhuurder_street_address',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_email' => array(
        'title' => ts('Hoofdhuurder e-mail'),
        'name' => 'hoofdhuurder_email',
        'filter_name' => 'hoofdhuurder_email',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'hoofdhuurder_phone' => array(
        'title' => ts('Hoofdhuurder telefoon'),
        'name' => 'hoofdhuurder_phone',
        'filter_name' => 'hoofdhuurder_phone',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder' => array(
        'title' => ts('Medehuurder naam'),
        'name' => 'medehuurder',
        'filter_name' => 'medehuurder',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder_email' => array(
        'title' => ts('Medehuurder e-mail'),
        'name' => 'medehuurder_email',
        'filter_name' => 'medehuurder_email',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'medehuurder_phone' => array(
        'title' => ts('Medehuurder telefoon'),
        'name' => 'medehuurder_phone',
        'filter_name' => 'medehuurder_phone',
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
    $this->_select = "SELECT civicrm_case.id AS case_id, civicrm_case.subject AS case_subject, 
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
    $this->beginPostProcess();
    //$this->_formValues = $this->exportValues();
    $this->select();
    $this->from();
    $this->where();
    $this->orderBy();
    $sql = $this->_select.' '.$this->_from.' '.$this->_where. ' '.$this->_orderBy;
    
    //$this->getGroupFields();
    $rows = array();
    $this->buildRows($sql, $rows);

    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }
  
  public function buildRows($sql, &$rows) {
    set_time_limit(0);
    
    /*
     * create temporary table to for case and additional data
     */
    $this->createTempTable(); 
    $this->truncateTempTable();
    
    //echo('sql: ' . $sql);
    
    $daoTemp = CRM_Core_DAO::executeQuery($sql);
    if (!is_array($rows)) {
      $rows = array();
    }
    /*
     * add records to temporary table
     */
    while ($daoTemp->fetch()) {
      $sql = "INSERT INTO werkoverzicht_dossier 
        (case_id, case_subject, case_case_type, case_status, case_start_date, case_contact_id)
        VALUES ('" . $daoTemp->case_id . "', '" . addslashes($daoTemp->case_subject) . "', '" . addslashes($daoTemp->case_case_type) . "', '" . addslashes($daoTemp->case_status) . "', '" . $daoTemp->case_start_date . "', '" . $daoTemp->case_contact_id . "' )";
      
      //echo('insert sql: ' . $sql);
      CRM_Core_DAO::executeQuery($sql);
      
      /*
      * add vge to temporary table
      * one vge at the time
      */
      $this->addTempVge($daoTemp);
      
      /*
      * add hoofdhuurder to temporary table
      * one hoofdhuurder at the time
      */
      $this->addTempHoofdhuurder($daoTemp);
      
      /*
      * add medehuurder to temporary table
      * one medehuurder at the time
      */
      $this->addTempMedehuurder($daoTemp);
    }
    
    /*
    * add typeringen to temporary table
    * all typeringen at once
    */
    $this->addTempTyperingen();
    
    /*
    * add dossiermanager to temporary table
    * all dossiermanagers at once
    */
    $this->addTempDossiermanager();
    
    /*
    * add deurwaarders to temporary table
    * all deurwaarders at once
    */
    $this->addTempDeurwaarder();
    
    /*
    * add ontruiming to temporary table
    * all ontruiming at once
    */
    $this->addTempOntruiming();
    
    /*
    * add vonnis to temporary table
    * all vonnis at once
    */
    $this->addTempVonnis();
    
    
  }
  
  private function createTempTable(){
    /*$sql = "";*/
    $sql = "CREATE TABLE IF NOT EXISTS werkoverzicht_dossier (
      case_id INT(11),
      case_subject VARCHAR(128),
      case_case_type VARCHAR(128),
      case_status VARCHAR(225),
      case_start_date DATE,
      case_contact_id INT(11),
      typeringen VARCHAR(128),
      dossiermanager VARCHAR(128),
      deurwaarder VARCHAR(128),
      ontruiming VARCHAR(2),
      ontruiming_status VARCHAR(255),
      ontruiming_activity_date_time DATETIME, 
      vonnis VARCHAR(2), 
      vonnis_activity_date_time DATETIME,
      property_vge_id INT(11),
      property_complex_id VARCHAR(45),
      property_block VARCHAR(128),
      property_city_region VARCHAR(128),
      property_vge_type VARCHAR(128),
      hoofdhuurder VARCHAR(128),
      hoofdhuurder_street_address VARCHAR(96),
      hoofdhuurder_email VARCHAR(64),
      hoofdhuurder_phone VARCHAR(32),
      medehuurder VARCHAR(128),
      medehuurder_email VARCHAR(64),
      medehuurder_phone VARCHAR(32))";
    
    //echo('temp sql: ' . $sql);
    
    CRM_Core_DAO::executeQuery($sql);
  }
  
  private function truncateTempTable(){
    $sql = "TRUNCATE TABLE werkoverzicht_dossier";
    CRM_Core_DAO::executeQuery($sql);
  }
  
  private function removeTempTable(){
    
  }
  
  private function addTempTyperingen(){
    $sql = "SELECT civicrm_value_ov_data.entity_id, civicrm_property_type.label FROM civicrm_value_ov_data 
      LEFT JOIN civicrm_property_type ON civicrm_property_type.id = civicrm_value_ov_data.ov_type";
    $dao = CRM_Core_DAO::executeQuery($sql);
        
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET typeringen = '" . addslashes($dao->label) . "' WHERE case_id = '" . $dao->entity_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempDossiermanager(){
    $sql = "SELECT civicrm_contact.sort_name, civicrm_relationship.case_id FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->dossierManagerRelationshipTypeId . "'";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET dossiermanager = '" . addslashes($dao->sort_name) . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempDeurwaarder(){
    $sql = "SELECT civicrm_contact.sort_name, civicrm_relationship.case_id FROM civicrm_contact
      LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_b = civicrm_contact.id
      WHERE civicrm_relationship.relationship_type_id = '" . $this->mbreportsConfig->deurwaarderRelationshipTypeId . "'";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET deurwaarder = '" . addslashes($dao->sort_name) . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempOntruiming(){    
    $sql = "SELECT (CASE WHEN 3 = status_id THEN 'J' ELSE 'N' END) AS ontruiming, civicrm_case_activity.case_id, civicrm_activity.activity_date_time, civicrm_option_value.label FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_option_value ON civicrm_option_value.value = civicrm_activity.status_id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->ontruimingActTypeId . "'
      AND civicrm_option_value.option_group_id = '" . $this->mbreportsConfig->activityStatusTypeOptionGroupId . "' 
      GROUP BY civicrm_case_activity.case_id ORDER BY civicrm_activity.activity_date_time DESC ";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET ontruiming = '" . $dao->ontruiming . "', ontruiming_status= '" . addslashes($dao->label) . "', ontruiming_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempVonnis(){
    $sql = "SELECT (CASE WHEN status_id IS NULL THEN 'N' ELSE 'J' END) AS vonnis, civicrm_case_activity.case_id, civicrm_activity.activity_date_time FROM civicrm_activity 
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      WHERE civicrm_activity.activity_type_id = '" . $this->mbreportsConfig->vonnisActTypeId . "'
      AND civicrm_option_value.option_group_id = '" . $this->mbreportsConfig->activityStatusTypeOptionGroupId . "' 
      GROUP BY civicrm_case_activity.case_id ORDER BY civicrm_activity.activity_date_time DESC ";
        
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET vonnis = '" . $dao->vonnis . "', vonnis_activity_date_time = '" . $dao->activity_date_time . "' WHERE case_id = '" . $dao->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempVge($daoTemp)
  {
    $caseVgeData = CRM_Utils_MbreportsUtils::getCaseVgeData($daoTemp->case_id);
    /*echo('case_id: ' . $daoTemp->case_id) . '<br/>' . PHP_EOL;
    echo('<pre>');
    print_r($caseVgeData);
    echo('</pre>') . '<br/>' . PHP_EOL;*/
    
    /*Array
    (
        [id] => 34297
        [entity_id] => 75355
        [hov_nummer_first_5] => 41511
        [vge_nummer_first_6] => 7355
        [vge_adres_first_7] => Klingelbeek 123
        [correspondentienaam_first_8] => Dhr A. Hekkert
        [begindatum_hov_9] => 2008-02-25 00:00:00
        [einddatum_hov_10] => 2009-06-09 00:00:00
    )*/
    
    /*property_vge_id INT(11),
      property_complex_id VARCHAR(45),
      property_block VARCHAR(128),
      property_city_region VARCHAR(128),
      property_vge_type_id INT(11),
      property_vge_type VARCHAR(128)*/
    
    $sql = "SELECT civicrm_property.vge_id, civicrm_property.complex_id, civicrm_property.block, civicrm_property.city_region, civicrm_property.vge_type_id, civicrm_property_type.label AS vge_type FROM civicrm_property
      LEFT JOIN civicrm_property_type ON civicrm_property_type.id = civicrm_property.vge_type_id
      WHERE civicrm_property.id = '" . $caseVgeData['id'] . "' ";
    
    //echo('sql vge: ' . $sql) . '<br/>' . PHP_EOL;
    
    $dao = CRM_Core_DAO::executeQuery($sql);    
    while ($dao->fetch()) {
      $sql = "UPDATE werkoverzicht_dossier SET property_vge_id = '" . $dao->vge_id . "', property_complex_id = '" . $dao->complex_id . "',
        property_block = '" . $dao->block . "', property_city_region = '" . $dao->city_region . "', property_vge_type = '" . $dao->vge_type . "' 
        WHERE case_id = '" . $daoTemp->case_id . "'";
      CRM_Core_DAO::executeQuery($sql);
    }
  }
  
  private function addTempHoofdhuurder($daoTemp){
    $hoofdhuurder = CRM_Utils_DgwUtils::getHoofdhuurders($daoTemp->case_contact_id);
    echo('case_id: ' . $daoTemp->case_id) . '<br/>' . PHP_EOL;
    echo('<pre>');
    print_r($hoofdhuurder);
    echo('</pre>') . '<br/>' . PHP_EOL;
  }
  
  private function addTempMedehuurder($daoTemp){
    $medehuurder = CRM_Utils_DgwUtils::getMedeHuurders($daoTemp->case_contact_id);
    echo('case_id: ' . $daoTemp->case_id) . '<br/>' . PHP_EOL;
    echo('<pre>');
    print_r($medehuurder);
    echo('</pre>') . '<br/>' . PHP_EOL;
  }
}