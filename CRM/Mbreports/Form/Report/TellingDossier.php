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
    $this->setOptionLists();
    $this->setColumns();    
    parent::__construct();
  }

  function preProcess() {
    $this->assign('reportTitle', ts('Totaaltellingen dossier Mens en Buurt'));
    parent::preProcess();
  }

  function select() {

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  function from() {
    $this->_from = NULL;
  }

  function where() {
    $clauses = array();

    if (empty($clauses)) {
      $this->_where = "WHERE ( 1 ) ";
    }
  }

  function groupBy() {
    $this->_groupBy = "";
  }

  function orderBy() {
    $this->_orderBy = "";
  }

  function postProcess() {
    $this->beginPostProcess();
    $this->_columnHeaders = array(
      'complex'       => array('title' => 'Complex'),
      'wijk'          => array('title' => 'Wijk'),
      'buurt'         => array('title' => 'Buurt'),
      'case_manager'  => array('title' => 'Dossiermanager'),
      'case_type'     => array('title' => 'Dossiertype'),
      'typering'      => array('title' => 'Typering'),
      'melder'        => array('title' => 'Melder'),
      'uitkomst'      => array('title' => 'Uitkomst'),
      'total_count'   => array('title' => 'Totaal'),
      'urgent_count'  => array('title' => 'Urgent'),
      'open_count'    => array('title' => 'Open'),
      'wait_count'    => array('title' => 'Wacht'),
      'closed_count'  => array('title' => 'Gesloten'));
    
    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);
    $sql = $this->buildQuery(TRUE);

    $rows = array();
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  function alterDisplay(&$rows) {
    // custom code to alter rows
    $entryFound = FALSE;
    $checkList = array();
    foreach ($rows as $rowNum => $row) {

      if (!empty($this->_noRepeats) && $this->_outputMode != 'csv') {
        // not repeat contact display names if it matches with the one
        // in previous row
        $repeatFound = FALSE;
        foreach ($row as $colName => $colVal) {
          if (CRM_Utils_Array::value($colName, $checkList) &&
            is_array($checkList[$colName]) &&
            in_array($colVal, $checkList[$colName])
          ) {
            $rows[$rowNum][$colName] = "";
            $repeatFound = TRUE;
          }
          if (in_array($colName, $this->_noRepeats)) {
            $checkList[$colName][] = $colVal;
          }
        }
      }

      if (array_key_exists('civicrm_membership_membership_type_id', $row)) {
        if ($value = $row['civicrm_membership_membership_type_id']) {
          $rows[$rowNum]['civicrm_membership_membership_type_id'] = CRM_Member_PseudoConstant::membershipType($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_address_state_province_id', $row)) {
        if ($value = $row['civicrm_address_state_province_id']) {
          $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_address_country_id', $row)) {
        if ($value = $row['civicrm_address_country_id']) {
          $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        $rows[$rowNum]['civicrm_contact_sort_name'] &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url("civicrm/contact/view",
          'reset=1&cid=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Contact Summary for this Contact.");
        $entryFound = TRUE;
      }

      if (!$entryFound) {
        break;
      }
    }
  }
  private function setComplexList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(complex_id) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->complex_id)) {
      $this->_complexList[] = $dao->complex_id;
      }
    }
    asort($this->_complexList);
  }

  private function setBuurtList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(city_region) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->city_region)) {
      $this->_buurtList[] = $dao->city_region;
      }
    }
    asort($this->_buurtList);
  }
  
  private function setOptionLists() {
    $this->setComplexList();
    $this->setWijkList();
    $this->setBuurtList();
  }
  
  private function setWijkList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(block) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->block)) {
      $this->_wijkList[] = $dao->block;
      }
    }
    asort($this->_wijkList);
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
          'options'       => $this->_complexList,
        ),
        'wijk_id' => array(
          'title'         => 'Wijk',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $this->_wijkList
        ),
        'buurt_id' => array(
          'title'         => 'Buurt',
          'type'          => CRM_Utils_Type::T_INT,
          'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
          'options'       => $this->_buurtList
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
}