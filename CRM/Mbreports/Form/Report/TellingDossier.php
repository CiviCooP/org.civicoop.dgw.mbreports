<?php
set_time_limit(0);

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
  
  protected $_noFields = TRUE;
  
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
    $this->_select = 'SELECT a.id AS case_id, d.label AS case_type, a.start_date
      , e.label AS status, a.end_date, b.contact_id_b AS case_manager_id, c.wf_melder';
  }

  function from() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $this->_from = 'FROM civicrm_case a 
      LEFT JOIN civicrm_relationship b ON a.id = b.case_id AND b.relationship_type_id = '
      .$mbreportsConfig->dossierManagerRelationshipTypeId.' 
      LEFT JOIN civicrm_value_wf_data c ON a.id = c.entity_id
      LEFT JOIN civicrm_option_value d ON a.case_type_id = d.value AND d.option_group_id = '
      .$mbreportsConfig->caseTypeOptionGroupId
      .' LEFT JOIN civicrm_option_value e ON a.status_id = e.value AND e.option_group_id = '
      .$mbreportsConfig->caseStatusOptionGroupId;
  }

  function where() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $this->_where = 'WHERE a.is_deleted = 0';
    if (!empty($this->_formValues['case_type_value'])) {
      $this->_where .= ' AND '.$this->setMultipleWhereClause($this->_formValues['case_type_value'], $mbreportsConfig->caseTypes, 'd.label', $this->_formValues['case_type_op']);
    }
    if (!empty($this->_formValues['case_manager_value'])) {
      $this->_where .= ' AND b.contact_id_b '.$this->formatOperator($this->_formValues['case_manager_op']).'('.implode(', ', $this->_formValues['case_manager_value']).')';
    }
    if (!empty($this->_formValues['wf_melder_value'])) {
      $this->_where .= ' AND '.$this->setMultipleWhereClause($this->_formValues['wf_melder_value'], $mbreportsConfig->wfMelderList, 'wf_melder', $this->_formValues['wf_melder_op']);      
    }
    if (!empty($this->_formValues['end_date_relative']) 
      || !empty($this->_formValues['end_date_from']) 
      || !empty($this->_formValues['end_date_to'])) {
      $relative = $this->_formValues['end_date_relative'];
      $from     = $this->_formValues['end_date_from'];
      $to       = $this->_formValues['end_date_to'];
      $this->_where .= ' AND ('.$this->dateClause('a.end_date', $relative, $from, $to, CRM_Utils_Type::T_DATE).' OR a.end_date IS NULL)';
    }
  }
  
  private function setMultipleWhereClause($keys, $list, $field, $operator) {
    $values = array();
    foreach ($keys as $key) {
      $values[] = CRM_Utils_Array::value($key, $list);
    }
    return $field.' '.$this->formatOperator($operator).'("'.implode('", "', $values).'")';
  }
  
  private function setSeparatedWhereClause($keys, $field, $operator) {
    $values = array();
    foreach ($keys as $key) {
      $values[] = CRM_Core_DAO::VALUE_SEPARATOR.$key.CRM_Core_DAO::VALUE_SEPARATOR;
    }
    return $field.' '.$this->formatOperator($operator).'("'.implode('", "', $values).'")';
  }
  
  private function formatOperator($operator) {
    $operator = strtoupper($operator);
    if ($operator == 'NOTIN') {
      $operator = 'NOT IN';
    }
    return $operator;
  }
  
  private function reverseOperator($operator) {
    if ($operator == 'in') {
      $operator = 'NOTIN';
    } else {
      $operator = 'IN';
    }
    return $operator;
  }

  
  function orderBy() {
    $this->_orderBy = "";
  }

  function postProcess() {
    $this->beginPostProcess();
    $this->_formValues = $this->exportValues();
    $this->select();
    $this->from();
    $this->where();
    $sql = $this->_select.' '.$this->_from.' '.$this->_where;
    
    $this->getGroupFields();
    $rows = array();
    $this->buildRows($sql, $rows);
    $this->alterDisplay($rows);

    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
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
        'complex' => array(
          'title'         => 'Complex',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->complexList,
        ),
        'wijk' => array(
          'title'         => 'Wijk',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->wijkList
        ),
        'buurt' => array(
          'title'         => 'Buurt',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->buurtList
        ),
        'case_manager' => array(
          'title'         => 'Dossiermanager',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->dossierManagerList
        ),
        'wf_melder' => array(
          'title'         => 'Woonfraude melder',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $mbreportsConfig->wfMelderList
        ),
        'end_date' => array(
          'title'        => 'Periode',
          'default'      => 'this.month',
          'operatorType' => CRM_Report_Form::OP_DATE,
    ))));        
  }
  
  public function buildRows($sql, &$rows) {
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
     * delete from temporary data based on vge selections
     */
    $this->removeTempTable();
    /*
     * now select records from temp and build row from them
     */
    $query = 'SELECT COUNT(*) AS countCases, '.implode(', ', $this->_groupFields)
      .' FROM data_rows GROUP BY '.implode(', ', $this->_groupFields)
      .' ORDER BY '.implode(', ', $this->_groupFields);
    $dao = CRM_Core_DAO::executeQuery($query);
    $previousLevel = NULL;
    $levelCount = 0;
    $totalCount = 0;
    while ($dao->fetch()) {
      $totalCount = $totalCount + $dao->countCases;
      $rows[] = $this->buildSingleRow($dao->countCases, $previousLevel, $levelCount, $dao);
    }
    $this->buildTotalRows($totalCount, $dao, $rows, $levelCount, $previousLevel);
  }
  /**
   * Function to create temporary data to hold rows that are partially filled
   * from civicrm_case and partially updated when building the rows
   */
  private function createTempTable() {
    $query = 'CREATE TEMPORARY TABLE IF NOT EXISTS data_rows (
      case_id INT(11),
      complex VARCHAR(25),
      wijk VARCHAR(128),
      buurt VARCHAR(128),
      case_manager VARCHAR(255),
      case_type VARCHAR(255),
      wf_melder VARCHAR(255),
      status VARCHAR(25), 
      start_date VARCHAR(25), 
      end_date VARCHAR(25))';
    CRM_Core_DAO::executeQuery($query);
  }
  /**
   * Function to add  a record to temp table
   */
  private function addTempTable($dao) {
    $insert = 'INSERT INTO data_rows (case_id, case_manager, case_type, status, start_date,
      end_date, wf_melder, buurt, wijk, complex)';
    $elementIndex = 1;
    $insValues = array();
    $insParams = array();
    
    $this->setValueLine($dao->case_id, 'String',  $elementIndex, $insParams, $insValues);
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $caseManager = CRM_Utils_Array::value($dao->case_manager_id, $mbreportsConfig->dossierManagerList);
    $this->setValueLine($caseManager, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->case_type, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->status, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->start_date, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->end_date, 'String',  $elementIndex, $insParams, $insValues);
    $this->setValueLine($dao->wf_melder, 'String',  $elementIndex, $insParams, $insValues);
    /*
     * retrieve data for VGE and for wf_uitkomst
     */
    $vgeData = $this->getCaseVgeData($dao->case_id);
    $this->setValueLine($vgeData['block'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($vgeData['city_region'], 'String', $elementIndex, $insParams, $insValues);
    $this->setValueLine($vgeData['complex_id'], 'String', $elementIndex, $insParams, $insValues);

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
    
    if (isset($this->_formValues['wijkGroupBy']) and $this->_formValues['wijkGroupBy'] == TRUE) {
      $this->_groupFields[] = 'wijk';
      $this->_columnHeaders['wijk'] = array('title' => 'Wijk');
    }
    if (isset($this->_formValues['buurtGroupBy']) and $this->_formValues['buurtGroupBy'] == TRUE) {
      $this->_groupFields[] = 'buurt';
      $this->_columnHeaders['buurt'] = array('title' => 'buurt');
    }
    if (isset($this->_formValues['complexGroupBy']) and $this->_formValues['complexGroupBy'] == TRUE) {
      $this->_groupFields[] = 'complex';
      $this->_columnHeaders['complex'] = array('title' => 'Complex');
    }
    if (isset($this->_formValues['caseTypeGroupBy']) and $this->_formValues['caseTypeGroupBy'] == TRUE) {
      $this->_groupFields[] = 'case_type';
      $this->_columnHeaders['case_type'] = array('title' => 'Dossiertype');
    }
    if (isset($this->_formValues['caseManagerGroupBy']) and $this->_formValues['caseManagerGroupBy'] == TRUE) {
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
    if (!isset($dao->levelField) or $dao->levelField != $previousLevel) {
      $row['level_break'] = true;
      $row['total_count'] = $levelCount;
      $row['previous'] = $previousLevel;
      $row['current'] = $dao->$levelField;
      $row['col_span'] = count($this->_groupFields);
      $previousLevel = $dao->$levelField;
      $levelCount = 0;
    } else {
      $row['level_break'] = false;
      $row['total_count'] = 0;
      $row['previous'] = '';
      $row['col_span'] = 0;
    }
    $row['last'] = false;
    foreach ($this->_groupFields as $fieldId => $fieldValue) {
      $row[$fieldValue] = $dao->$fieldValue;
    }
    $row['count'] = $countCases;
    $levelCount = $levelCount + $countCases;
    return $row;
  }
  
  private function buildTotalRows($totalCount, $dao, &$rows, $levelCount, $previousLevel) {
    $row = array();
    $row['level_break'] = true;
    $row['last'] = true;
    $row['total_count'] = $levelCount;
    $row['previous'] = $previousLevel;
    $row['col_span'] = count($this->_groupFields);
    $rows[] = $row;
    $totalRow['col_span'] = count($this->_groupFields);
    $totalRow['total'] = $totalCount;
    $rows[] = $totalRow;
  }
  
  private function removeTempTable() {
    /*
     * set remove where clauses
     */
    $removeWhere = $this->setRemoveWhereClauses();
    if (!empty($removeWhere)) {
      $removeQuery = 'DELETE FROM data_rows WHERE '.$removeWhere;
      CRM_Core_DAO::executeQuery($removeQuery);
    }
  }
  
  private function setRemoveWhereClauses() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $whereClauses = array();
    if (isset($this->_formValues['complex_value']) and !empty($this->_formValues['complex_value'])) {
      $operator = $this->reverseOperator($this->_formValues['complex_op']);
      $whereClauses[] = $this->setMultipleWhereClause($this->_formValues['complex_value'], $mbreportsConfig->complexList, 'complex', $operator);
    }
    if (isset($this->_formValues['wijk_value']) and !empty($this->_formValues['wijk_value'])) {
      $operator = $this->reverseOperator($this->_formValues['wijk_op']);
      $whereClauses[] = $this->setMultipleWhereClause($this->_formValues['wijk_value'], $mbreportsConfig->wijkList, 'wijk', $operator);
    }
    if (isset($this->_formValues['buurt_value']) and !empty($this->_formValues['buurt_value'])) {
      $operator = $this->reverseOperator($this->_formValues['buurt_op']);
      $whereClauses[] = $this->setMultipleWhereClause($this->_formValues['buurt_value'], $mbreportsConfig->buurtList, 'buurt', $operator);
    }
    if (!empty($whereClauses)) {
      $whereString = implode(' OR ', $whereClauses);
    } else {
      $whereString = '';
    }
    return $whereString;
  }  
}