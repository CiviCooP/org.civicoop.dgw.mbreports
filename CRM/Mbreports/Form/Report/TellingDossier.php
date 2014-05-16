<?php
/**
/**
 * Report totaaltellingen dossiers
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 13 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */

class CRM_Mbreports_Form_Report_TellingDossier extends CRM_Report_Form {

  protected $_addressField = FALSE;
  protected $_emailField = FALSE;
  protected $_customGroupFilters = FALSE;
  protected $_add2groupSupported = FALSE;
  protected $_summary = NULL;
  protected $_formValues = array();
  protected $_groupFields = array();

  function __construct() {
    $this->setColumns();
    $this->setGroupBys();
    parent::__construct();
  }

  function preProcess() {
    $this->assign('reportTitle', ts('Totaaltellingen dossier Mens en Buurt'));
    parent::preProcess();
  }

  function select() {

    $this->_select = 'SELECT a.id AS case_id, f.label AS case_type, a.start_date
      , g.label AS status, a.end_date, b.contact_id_b AS manager_id, c.sort_name AS manager_name
      , d.ov_type, e.wf_melder';
  }

  function from() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $this->_from = 'FROM civicrm_case a 
      JOIN civicrm_relationship b ON a.id = b.case_id
      JOIN civicrm_contact c ON b.contact_id_b = c.id
      LEFT JOIN civicrm_value_ov_data d ON a.id = d.entity_id
      LEFT JOIN civicrm_value_wf_data e ON a.id = e.entity_id
      LEFT JOIN civicrm_option_value f ON a.case_type_id = f.value AND f.option_group_id = '
      .$mbreportsConfig->caseTypeOptionGroupId
      .' LEFT JOIN civicrm_option_value g ON a.status_id = g.value AND g.option_group_id = '
      .$mbreportsConfig->caseStatusOptionGroupId;
  }

  function where() {
    $inArray = array();
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    foreach ($mbreportsConfig->caseTypes as $caseType) {
      $inArray[] = $caseType;
    }
    //$this->_where = 'WHERE a.is_deleted = 0 AND f.label IN("'
    //  .implode('", "', $inArray).'")';
    $this->_where = 'WHERE a.is_deleted = 0 AND b.contact_id_b IN(40873, 27)';
  }

  function orderBy() {
    $this->_orderBy = "";
  }

  function postProcess() {
    $this->beginPostProcess();
    $this->select();
    $this->from();
    $this->where();
    $sql = $this->_select.' '.$this->_from.' '.$this->_where;
    
    $this->_formValues = $this->exportValues();
    $this->getGroupFields();
    $rows = array();
    $this->buildRows($sql, $rows);

    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  function alterDisplay(&$rows) {
  }
  
  private function setColumns() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $this->_columns = array(
      'civicrm_case' => array(
      'dao' => 'CRM_Case_DAO_Case',
      'filters' => array(
        'case_type'       => array(
          'title'         => ts('Case Type'),
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->caseTypes
        ),
        'complex_id' => array(
          'title'         => 'Complex',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->complexList,
        ),
        'wijk_id' => array(
          'title'         => 'Wijk',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->wijkList
        ),
        'buurt_id' => array(
          'title'         => 'Buurt',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->buurtList
        ),
        'manager_id' => array(
          'title'         => 'Dossiermanager',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->dossierManagerList
        ),
        'ov_type_id' => array(
          'title'         => 'Overlast typering',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_SELECT,
          'options'       => $mbreportsConfig->ovTypeList
        ),
        'wf_type_id' => array(
          'title'         => 'Woonfraude typering',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_SELECT,
          'options'       => $mbreportsConfig->wfTypeList
        ),
        'periode' => array(
          'title'        => 'Periode',
          'default'      => 'this.month',
          'operatorType' => CRM_Report_Form::OP_DATE,
    ))));        
  }
  
  public function buildRows($sql, &$rows) {
    // temp
    /*
     * create temporary table to for case and additional data
     */
    $this->createTempTable();    
    $daoTemp = CRM_Core_DAO::executeQuery($sql);
    if (!is_array($rows)) {
      $rows = array();
    }
    /*
     * add records to temporary table
     */
    while ($daoTemp->fetch()) {
      $this->addTempTable($daoTemp);
    }
    /*
     * now select records from temp and build row from them
     */
    $query = 'SELECT COUNT(*) AS countCases, '.implode(', ', $this->_groupFields)
      .' FROM data_rows GROUP BY '.implode(', ', $this->_groupFields)
      .' ORDER BY '.implode(', ', $this->_groupFields);
    $dao = CRM_Core_DAO::executeQuery($query);
    $previousLevel = NULL;
    $levelCount = 0;
    while ($dao->fetch()) {
      $rows[] = $this->buildSingleRow($dao->countCases, $previousLevel, $levelCount, $dao);
    }
  }
  /**
   * Function to create temporary data to hold rows that are partially filled
   * from civicrm_case and partially updated when building the rows
   */
  private function createTempTable() {
    $query = 'CREATE TABLE IF NOT EXISTS data_rows (
      case_id INT(11),
      complex VARCHAR(25),
      wijk VARCHAR(128),
      buurt VARCHAR(128),
      case_manager VARCHAR(255),
      case_type VARCHAR(255),
      ov_type VARCHAR(255),
      wf_type VARCHAR(255),
      wf_melder VARCHAR(255),
      wf_uitkomst VARCHAR(255),
      ov_uitkomst VARCHAR(255),
      status VARCHAR(25), 
      start_date VARCHAR(25), 
      end_date VARCHAR(25))';
    CRM_Core_DAO::executeQuery($query);
    //temp
    CRM_Core_DAO::executeQuery('TRUNCATE TABLE data_rows');
  }
  /**
   * Function to add  a record to temp table
   */
  private function addTempTable($dao) {    
    $insert = 'INSERT INTO data_rows (case_id, case_manager, case_type, status, start_date,
      end_date, wf_melder, ov_type, wijk, buurt, complex, wf_type, wf_uitkomst, ov_uitkomst)';
    $elementIndex = 1;
    $insValues = array();
    $insParams = array();
    
    $this->setValueLine($dao->case_id, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->manager_name, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->case_type, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->status, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->start_date, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->end_date, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->wf_melder, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->ov_type, 'String',  $elementIndex, $insParams, $insValues);
    /*
     * retrieve data for VGE and for wf_uitkomst
     */
    $vgeData = $this->getCaseVgeData($dao->case_id);
    $this->setValueLine($vgeData['block'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($vgeData['city_region'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($vgeData['complex_id'], 'String', $elementIndex, $insParams, $insValues);

    $wfUitkomstData = $this->getWfUitkomstData($dao->case_id);
    $this->setValueLine($wfUitkomstData['wf_type'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($wfUitkomstData['wf_uitkomst'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($wfUitkomstData['ov_uitkomst'], 'String', $elementIndex, $insParams, $insValues);

    $insert = $insert.' VALUES('.implode(', ', $insValues).')';
    CRM_Core_DAO::executeQuery($insert, $insParams);
  }
  /**
   * Function to retrieve vge data for case
   */
  private function getCaseVgeData($caseId) {
    $hovVgeData = CRM_Utils_MbreportsUtils::getCaseVgeData($caseId);
    $params = array('name' => 'VGE_nummer_First', 'return' => 'column_name');
    $vgeNummerFirstField = civicrm_api3('CustomField', 'Getvalue', $params);
    if (isset($hovVgeData[$vgeNummerFirstField])) {
      $vgeData = CRM_Mutatieproces_Property::getByVgeId($hovVgeData[$vgeNummerFirstField]);
    } else {
      $vgeData = array();
    }
    if (empty($vgeData['complex_id'])) {
      $vgeData['complex_id'] = 'Onbekend';
    }
    if (empty($vgeData['block'])) {
      $vgeData['block'] = 'Onbekend';
    }
    if (empty($vgeData['city_region'])) {
      $vgeData['city_region'] = 'Onbekend';
    }
    return $vgeData;
  }
  /**
   * Function get get wf_uitkomst data for case (linked to activity change case status)
   */
  private function getWfUitkomstData($caseId) {
    return array();
  } 
    
  private function setValueLine($field, $type, &$elementIndex, &$insParams, &$insValues) {
    if (!empty($field)) {
      $insParams[$elementIndex] = array($field, $type);
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
  }
  
  private function setGroupBys() {        
    $this->addElement('checkbox', 'wijkGroupBy', ts('Wijk'));
    $this->addElement('checkbox', 'buurtGroupBy', ts('Buurt'));
    $this->addElement('checkbox', 'complexGroupBy', ts('Complex'), NULL, array('checked'));
    $this->addElement('checkbox', 'caseTypeGroupBy', ts('Case Type'), NULL, array('checked'));
    $this->addElement('checkbox', 'caseManagerGroupBy', ts('Case Manager'), NULL, array('checked'));
  }
  
  private function getGroupFields() {
    if ($this->_formValues['wijkGroupBy'] == TRUE) {
      $this->_groupFields[] = 'wijk';
      $this->_columnHeaders['wijk'] = array('title' => 'Wijk');
    }
    if ($this->_formValues['buurtGroupBy'] == TRUE) {
      $this->_groupFields[] = 'buurt';
      $this->_columnHeaders['buurt'] = array('title' => 'buurt');
    }
    if ($this->_formValues['complexGroupBy'] == TRUE) {
      $this->_groupFields[] = 'complex';
      $this->_columnHeaders['complex'] = array('title' => 'Complex');
    }
    if ($this->_formValues['caseTypeGroupBy'] == TRUE) {
      $this->_groupFields[] = 'case_type';
      $this->_columnHeaders['case_type'] = array('title' => 'Dossiertype');
    }
    if ($this->_formValues['caseManagerGroupBy']== TRUE) {
      $this->_groupFields[] = 'case_manager';
      $this->_columnHeaders['case_manager'] = array('title' => 'Dossiermanager');
    }
    $this->_groupFields[] = 'status';
    $this->_columnHeaders['status'] = array('title' => 'Status');
    $this->_columnHeaders['count'] = array('title' => 'Aantal');
  }
  
  private function buildSingleRow($countCases, &$previousLevel, &$levelCount, $dao) {
    $row = array();
    $levelField = $this->_groupFields[0];
    if ($dao->$levelField == $previousLevel) {
      $row['level_break'] = false;
      $row['total_count'] = 0;
      $row['previous'] = '';
      $row['col_span'] = 0;
    } else {
      $row['level_break'] = true;
      $row['total_count'] = $levelCount;
      $row['previous'] = $previousLevel;
      $row['col_span'] = count($this->_groupFields);
      $previousLevel = $dao->$levelField;
      $levelCount = 0;
    }
    foreach ($this->_groupFields as $fieldId => $fieldValue) {
      $row[$fieldValue] = $dao->$fieldValue;
    }
    $row[count] = $countCases;
    $levelCount = $levelCount + $countCases;
    return $row;
  }
}    