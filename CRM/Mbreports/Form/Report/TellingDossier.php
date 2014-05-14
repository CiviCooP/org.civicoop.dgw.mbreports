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
  protected $_complexList = array();
  protected $_buurtList = array();
  protected $_wijkList = array();
  protected $_dossierManagerList = array();

  function __construct() {
    $this->setColumns();    
    parent::__construct();
  }

  function preProcess() {
    $this->assign('reportTitle', ts('Totaaltellingen dossier Mens en Buurt'));
    parent::preProcess();
  }

  function select() {

    $this->_select = 'SELECT a.id AS case_id, a.case_type_id, f.label AS case_type, a.start_date, 
      a.status_id, a.end_date, b.contact_id_b AS manager_id, c.sort_name AS manager_name
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
      .$mbreportsConfig->caseTypeOptionGroupId;
  }

  function where() {
    $inArray = array();
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    foreach ($mbreportsConfig->caseTypes as $caseType) {
      $inArray[] = $caseType;
    }
    $this->_where = 'WHERE a.is_deleted = 0 AND f.label IN("'
      .implode('", "', $inArray).'")';
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
    
    //$values = $this->exportValues();
    
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
    $this->_columnHeaders = array(
      'complex'       => array('title' => 'Complex'),
      //'wijk'          => array('title' => 'Wijk'),
      //'buurt'         => array('title' => 'Buurt'),
      //'case_manager'  => array('title' => 'Dossiermanager'),
      'case_type'     => array('title' => 'Dossiertype'),
      //'typering'      => array('title' => 'Typering'),
      //'melder'        => array('title' => 'Melder'),
      //'uitkomst'      => array('title' => 'Uitkomst'),
      'total_count'   => array('title' => 'Totaal'),
      'urgent_count'  => array('title' => 'Urgent'),
      'open_count'    => array('title' => 'Open'),
      'wait_count'    => array('title' => 'Wacht'),
      'closed_count'  => array('title' => 'Gesloten'));
    
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
    $rows = $this->buildDisplayRows();
  }
  /**
   * Function to create temporary data to hold rows that are partially filled
   * from civicrm_case and partially updated when building the rows
   */
  private function createTempTable() {
    $query = 'CREATE TABLE IF NOT EXISTS data_rows (
      complex VARCHAR(25),
      wijk VARCHAR(128),
      buurt VARCHAR(128),
      case_manager VARCHAR(255),
      case_type VARCHAR(255),
      ov_type VARCHAR(255),
      wf_type VARCHAR(255),
      wf_melder VARCHAR(255),
      wf_uitkomst VARCHAR(255),
      status_id INT(11), 
      start_date VARCHAR(25), 
      end_date VARCHAR(25))';
    CRM_Core_DAO::executeQuery($query);
  }
  /**
   * Function to add  a record to temp table
   */
  private function addTempTable($dao) {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $insert = 'INSERT INTO data_rows (case_manager, case_type, status_id, start_date,
      end_date, wf_melder, ov_type, complex, wijk, buurt, wf_type, wf_uitkomst)';
    $insValues = array();
    $elementIndex = 1;
    if (!empty($dao->manager_name)) {
      $insParams[$elementIndex] = array($dao->manager_name, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
      
    if (!empty($dao->case_type)) {
      $insParams[$elementIndex] = array($dao->case_type, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
      
    if (!empty($dao->status_id)) {
      $insParams[$elementIndex] = array($dao->status_id, 'Integer');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
      
    if (!empty($dao->start_date)) {
      $insParams[$elementIndex] = array($dao->start_date, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
      
    if (!empty($dao->end_date)) {
      $insParams[$elementIndex] = array($dao->end_date, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
      
    if (!empty($dao->wf_melder)) {
      $insParams[$elementIndex] = array($dao->wf_melder, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
    
    if (!empty($dao->ov_type)) {
      $ovType = CRM_Utils_Array::value($dao->ov_type, $mbreportsConfig->ovTypeList);
      $insParams[$elementIndex] = array($ovType, 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
    /*
     * retrieve data for VGE and for wf_uitkomst
     */
    $vgeData = $this->getCaseVgeData($dao->case_id);
    if (isset($vgeData['complex_id'])) {
      $insParams[$elementIndex] = array($vgeData['complex_id'], 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
    
    if (isset($vgeData['city_region'])) {
      $insParams[$elementIndex] = array($vgeData['city_region'], 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
    
    if (isset($vgeData['block'])) {
      $insParams[$elementIndex] = array($vgeData['block'], 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }

    $wfUitkomstData = $this->getWfUitkomstData($dao->case_id);
    if (isset($wfUitkomstData['wf_type'])) {
      $insParams[$elementIndex] = array($wfUitkomstData['wf_type'], 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
    
    if (isset($wfUitkomstData['wf_uitkomst'])) {
      $insParams[$elementIndex] = array($wfUitkomstData['wf_uitkomst'], 'String');
      $insValues[] = '%'.$elementIndex;
      $elementIndex++;
    } else {
      $insValues[] = 'NULL';
    }
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
    return $vgeData;
  }
  /**
   * Function get get wf_uitkomst data for case (linked to activity change case status)
   */
  private function getWfUitkomstData($caseId) {
    return array();
  } 
  
  private function buildDisplayRows() {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $result = array();
    $row = array();
    foreach ($mbreportsConfig->complexList as $complex) {
      $row['complex'] = '<strong>'.$complex. '</strong>';
      foreach ($mbreportsConfig->caseTypes as $caseType) {
        $row['case_type'] = $caseType;
        foreach ($mbreportsConfig->caseStatus as $statusId => $statusLabel) {
          $query = 'SELECT count(*) AS countCases FROM data_rows WHERE 
            complex = %1 AND case_type = %2 and status_id = %3';
          $params = array(
            1 => array($complex, 'String'),
            2 => array($caseType, 'String'),
            3 => array($statusId, 'Integer'));
          $daoCount = CRM_Core_DAO::executeQuery($query, $params);
          if ($daoCount->fetch()) {
            $totalCount = $totalCount + $daoCount->countCases;           
            switch($statusLabel) {
              case 'Gesloten':
                if ($row['closed_count'] != 0) {
                  $row['closed_count'] = $daoCount->countCases;
                } else {
                  $row['closed_count'] = '-';
                }
                break;
              case 'Open':
                if ($row['open_count'] != 0) {
                  $row['open_count'] = $daoCount->countCases;
                } else {
                  $row['open_count'] = '-';
                }
                break;
              case 'Urgent':
                if ($row['urgent_count'] != 0) {
                  $row['urgent_count'] = $daoCount->countCases;
                } else {
                  $row['urgent_count'] = '-';
                }
                break;
              case 'Wacht':
                if ($row['wait_count'] != 0) {
                  $row['wait_count'] = $daoCount->countCases;
                } else {
                  $row['wait_count'] = '-';
                }
                break;
            }
          }
        }
        if ($totalCount != 0) {
          $row['total_count'] = $totalCount;
          $rows[] = $row;
        }
        $totalCount = 0;
        $row = array();
      }
    }
    return $rows;
  }
}