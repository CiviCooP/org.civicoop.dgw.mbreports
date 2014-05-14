<?php
/**
 * Class configuration singleton
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */
class CRM_Mbreports_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  
  public $caseTypeOptionGroupId = NULL;
  public $caseTypes = array();
  public $caseStatusOptionGroupId= NULL;
  public $caseStatus = array();
  public $actTypeOptionGroupId = NULL;
  
  public $hoofdhuurderRelationshipTypeId = NULL;
  public $dossierManagerRelationshipTypeId = NULL;
  public $dossierManagerList = array();
  public $complexList = array();
  public $buurtList = array();
  public $wijkList = array();
  /*
   * custom group for case type Woonfraude
   */
  public $wfMelderCustomGroupName = NULL;
  public $wfMelderCustomGroupId = NULL;
  public $wfMelderCustomTableName = NULL;
  
  public $wfUitkomstCustomGroupName = NULL;
  public $wfUitkomstCustomGroupId = NULL;
  public $wfUitkomstCustomTableName = NULL;
  
  public $wfTypeCustomFieldName = NULL;
  public $wfMelderCustomFieldName = NULL;
  public $wfUitkomstCustomFieldName = NULL;
  
  public $wfTypeColumnName = NULL;
  public $wfTypeList = array();
  public $wfMelderColumnName = NULL;
  public $wfUitkomstColumnName = NULL;
  public $woonfraudeCaseTypeId = NULL;
  
  public $changeCaseStatusActTypeId = NULL;
  /*
   * custom group for case type Overlast
   */
  public $ovCustomGroupName = NULL;
  public $ovCustomGroupId = NULL;
  public $ovCustomTableName = NULL;
  public $ovTypeCustomFieldName = NULL;
  public $ovTypeColumnName = NULL;
  public $ovTypeList = array();
  public $overlastCaseTypeId = NULL;
  
  /*
   * array with case types that are available for M&B reporting
   */
  public $validCaseTypes = array();
  /**
   * Constructor function
   */
  function __construct() {
    $this->setCaseTypeOptionGroupId();
    $this->setActTypeOptionGroupId();
    $this->setCaseStatusOptionGroupId();

    $this->setCaseTypeId('woonfraude');
    $this->setCaseTypeId('overlast');
    $this->setActTypeId('change Case Status');
    $this->setWfUitkomstCustomGroupName('wf_uitkomst');
    $this->setWfMelderCustomGroupName('wf_data');
    $this->setWfMelderCustomFieldName('wf_melder');
    $this->setWfTypeCustomFieldName('wf_type');
    $this->setWfUitkomstCustomFieldName('wf_uitkomst');
    $this->setWoonfraude();
    $this->setWfTypeList();
    
    $this->setOvCustomGroupName('ov_data');
    $this->setOvTypeCustomFieldName('ov_type');
    $this->setOverlast();
    $this->setOvTypeList();
    $this->setActTypeId('Change Case Status');
    $this->setValidCaseTypes();
    
    $this->setHoofdhuurderRelationshipTypeId();
    $this->setDossierManagerRelationshipTypeId();
    $this->setDossierManagerList();
    $this->setCaseTypes();
    $this->setBuurtList();
    $this->setComplexList();
    $this->setWijkList();
    $this->setCaseStatus();
  }
  
  private function setCaseTypeId($caseTypeName) {
    $propertyName = strtolower(trim($caseTypeName)).'CaseTypeId';
    $optionValueParams = array(
      'option_group_id' => $this->caseTypeOptionGroupId,
      'name'            => ucfirst($caseTypeName),
      'return'          => 'value');
    try {
      $this->$propertyName = civicrm_api3('OptionValue', 'Getvalue', $optionValueParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a valid case type '.ucfirst($caseTypeName.
        ', error from API OptionValue Getvalue : '.$ex->getMessage()));
    }
  }
  
  private function setActTypeId($actTypeName) {
    $gluedActTypeName = $this->glueStringParts($actTypeName);
    $propertyName = strtolower($gluedActTypeName).'ActTypeId';
    $optionValueParams = array(
      'option_group_id' => $this->actTypeOptionGroupId,
      'name'            => ucwords($actTypeName),
      'return'          => 'value');
    try {
      $this->$propertyName = civicrm_api3('OptionValue', 'Getvalue', $optionValueParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a valid activity type '.ucwords($actTypeName.
        ', error from API OptionValue Getvalue : '.$ex->getMessage()));
    }    
  }

  private function glueStringParts($string) {
    $result = $string;
    $parts = explode(' ', $string);
    if (count($parts) > 1) {
      $result = implode($parts);
    }
    return $result;
  }
  
  public function setWfMelderCustomGroupName($wfMelderCustomGroupName) {
    $this->wfMelderCustomGroupName = $wfMelderCustomGroupName;
  }
  
  public function setWfUitkomstCustomGroupName($wfUitkomstCustomGroupName) {
    $this->wfUitkomstCustomGroupName = $wfUitkomstCustomGroupName;
  }
  
  public function setWfMelderCustomGroupId($wfMelderCustomGroupId) {
    $this->wfMelderCustomGroupId = $wfMelderCustomGroupId;
  }
  
  public function setWfUitkomstCustomGroupId($wfUitkomstCustomGroupId) {
    $this->wfUitkomstCustomGroupId = $wfUitkomstCustomGroupId;
  }
  
  public function setWfUitkomstCustomTableName($wfUitkomstCustomTableName) {
    $this->wfUitkomstCustomTableName = $wfUitkomstCustomTableName;
  }
  
  public function setWfMelderCustomTableName($wfMelderCustomTableName) {
    $this->wfMelderCustomTableName = $wfMelderCustomTableName;
  }
  
  public function setWfTypeCustomFieldName($wfTypeCustomFieldName) {
    $this->wfTypeCustomFieldName = $wfTypeCustomFieldName;
  }
  
  public function setWfTypeColumnName($wfTypeColumnName) {
    $this->wfTypeColumnName = $wfTypeColumnName;
  }
  
  public function setWfMelderCustomFieldName($wfMelderCustomFieldName) {
    $this->wfMelderCustomFieldName = $wfMelderCustomFieldName;
  }
  
  public function setWfMelderColumnName($wfMelderColumnName) {
    $this->wfMelderColumnName = $wfMelderColumnName;
  }
  
  public function setWfUitkomstCustomFieldName($wfUitkomstCustomFieldName) {
    $this->wfUitkomstCustomFieldName = $wfUitkomstCustomFieldName;
  }
  
  public function setWfUitkomstColumnName($wfUitkomstColumnName) {
    $this->wfUitkomstColumnName = $wfUitkomstColumnName;
  }
  
  public function setOvCustomGroupName($ovCustomGroupName) {
    $this->ovCustomGroupName = $ovCustomGroupName;
  }
  
  public function setOvCustomGroupId($ovCustomGroupId) {
    $this->ovCustomGroupId = $ovCustomGroupId;
  }
  
  public function setOvCustomTableName($ovCustomTableName) {
    $this->ovCustomTableName = $ovCustomTableName;
  }
  
  public function setOvTypeCustomFieldName($ovTypeCustomFieldName) {
    $this->ovTypeCustomFieldName = $ovTypeCustomFieldName;
  }
  
  public function setOvTypeColumnName($ovTypeColumnName) {
    $this->ovTypeColumnName = $ovTypeColumnName;
  }
  
  private function setWoonfraude() {
    try {
      $customGroupMelder = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->wfMelderCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfMelderCustomGroupId(0);
      $this->setWfMelderCustomTableName('');
      $this->setWfMelderColumnName('');
      throw new Exception('Could not find a group with name '.$this->wfMelderCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setWfMelderCustomGroupId($customGroupMelder['id']);
    $this->setWfMelderCustomTableName($customGroupMelder['table_name']);

    try {
      $customGroupUitkomst = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->wfUitkomstCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfUitkomstCustomGroupId(0);
      $this->setWfUitkomstCustomTableName('');
      $this->setWfTypeColumnName('');
      $this->setWfUitkomstColumnName('');
      throw new Exception('Could not find a group with name '.$this->wfUitkomstCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setWfUitkomstCustomGroupId($customGroupUitkomst['id']);
    $this->setWfUitkomstCustomTableName($customGroupUitkomst['table_name']);
    $this->wfCustomFields();
  }
  
  private function wfCustomFields() {
    $customFieldParams['custom_group_id'] = $this->wfMelderCustomGroupId;
    $customFieldParams['return'] = 'column_name';
    $customFieldParams['name'] = $this->wfMelderColumnName;
    try {
      $this->setWfMelderColumnName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfMelderColumnName('');
      throw new Exception('Could not find custom field with name '.$this->wfMelderCustomFieldName
        .' in custom group '.$this->wfMelderCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    $customFieldParams['custom_group_id'] = $this->wfUitkomstCustomGroupId;
    $customFieldParams['name'] = $this->wfTypeCustomFieldName;
    try {
      $this->setWfTypeColumnName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfTypeColumnName('');
      throw new Exception('Could not find custom field with name '.$this->wfTypeCustomFieldName
        .' in custom group '.$this->wfUitkomstCustomGroupId.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    $customFieldParams['name'] = $this->wfUitkomstCustomFieldName;
    try {
      $this->setWfUitkomstColumnName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfUitkomstColumnName('');
      throw new Exception('Could not find custom field with name '.$this->wfUitkomstCustomFieldName
        .' in custom group '.$this->wfUitkomstCustomGroupId.', error from API CustomField Getvalue :'.$ex->getMessage());
    }    
  }
  
  private function setOverlast() {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->ovCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setOvCustomGroupId(0);
      $this->setOvCustomTableName('');
      $this->setOvTypeColumnName('');
      throw new Exception('Could not find a group with name '.$this->ovCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setOvCustomGroupId($customGroup['id']);
    $this->setOvCustomTableName($customGroup['table_name']);
    $this->ovCustomFields();
  }
  
  private function ovCustomFields() {
    $customFieldParams['custom_group_id'] = $this->ovCustomGroupId;
    $customFieldParams['return'] = 'column_name';
    $customFieldParams['name'] = $this->ovTypeCustomFieldName;
    try {
      $this->setOvTypeColumnName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setOvTypeColumnName('');
      throw new Exception('Could not find custom field with name '.$this->ovTypeCustomFieldName
        .' in custom group '.$this->ovCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
  }
  
  private function setDossierManagerRelationshipTypeId() {
    $params = array(
      'name_a_b'  =>  'Dossiermanager',
      'return'    =>  'id');
    try {
      $this->dossierManagerRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->dossierManagerRelationshipTypeId = 0;
    }
  }
  
  private function setHoofdhuurderRelationshiptypeId() {
    $params = array(
      'name_a_b'  =>  'Hoofdhuurder',
      'return'    =>  'id');
    try {
      $this->hoofdhuurderRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->hoofdhuurderRelationshipTypeId = 0;
    }
  }
  
  private function setValidCaseTypes() {
    $this->validCaseTypes = array('ActienaVonnis', 'Buitenkanstraject', 'Overlast', 
      'Volgcontact', 'Woonfraude', 'Laatstekans', 'Regeling');
    asort($this->validCaseTypes);
  }
  
  private function setCaseTypeOptionGroupId() {
    $params = array('name' => 'case_type', 'return' => 'id');
    try {
      $this->caseTypeOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->caseTypeOptionGroupId = 0;
    }
  }

  private function setCaseStatusOptionGroupId() {
    $params = array('name' => 'case_status', 'return' => 'id');
    try {
      $this->caseStatusOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->caseStatusOptionGroupId = 0;
    }
  }

  private function setActTypeOptionGroupId() {
    $params = array('name' => 'activity_type', 'return' => 'id');
    try {
      $this->actTypeOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->actTypeOptionGroupId = 0;
    }
  }
  
  private function setCaseTypes() {
    $params = array('option_group_id' => $this->caseTypeOptionGroupId);
    try {
      $apiCaseTypes = civicrm_api3('OptionValue', 'Get', $params);
      foreach ($apiCaseTypes['values'] as $apiCaseType) {
        if (in_array($apiCaseType['label'], $this->validCaseTypes)) {
          $this->caseTypes[$apiCaseType['value']] = $apiCaseType['label'];
        }
      }
    } catch (CiviCRM_API3_Exception $ex) {
      $this->caseTypes = array();
    }
    asort($this->caseTypes);
  }
  
  private function setCaseStatus() {
    $params = array('option_group_id' => $this->caseStatusOptionGroupId);
    try {
      $apiCaseStatus = civicrm_api3('OptionValue', 'Get', $params);
      foreach ($apiCaseStatus['values'] as $caseStatus) {
        $this->caseStatus[$caseStatus['value']] = $caseStatus['label'];
      }
    } catch (CiviCRM_API3_Exception $ex) {
      $this->caseStatus = array();
    }
    asort($this->caseStatus);
  }
  
  private function setDossierManagerList() {
    /*
     * retrieve all relationships dossiermanager
     */
    $query = 'SELECT DISTINCT(contact_id_b) as manager_id, display_name FROM civicrm_relationship '
      . 'JOIN civicrm_contact cc ON contact_id_b = cc.id WHERE relationship_type_id = %1 AND case_id IS NOT NULL';
    $params = array(1 => array($this->dossierManagerRelationshipTypeId, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while($dao->fetch()) {
      $this->dossierManagerList[$dao->manager_id] = $dao->display_name;
    }
    asort($this->dossierManagerList);
  }
  
  private function setOvTypeList() {
    $params = array(
      'name'            =>  $this->ovTypeCustomFieldName,
      'custom_group_id' =>  $this->ovCustomGroupId,
      'return'          =>  'option_group_id');
    $ovOptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $ovOptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->ovTypeList[$optionId] = $optionValue['label'];
    }
    asort($this->ovTypeList);
  }

  private function setWfTypeList() {
    $params = array(
      'name'            =>  $this->wfTypeCustomFieldName,
      'custom_group_id' =>  $this->wfUitkomstCustomGroupId,
      'return'          =>  'option_group_id');
    $wfOptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $wfOptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->wfTypeList[$optionId] = $optionValue['label'];
    }
    asort($this->wfTypeList);
  }
  private function setComplexList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(complex_id) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->complex_id)) {
      $this->complexList[] = $dao->complex_id;
      }
    }
    $this->complexList[] = 'Onbekend';
    asort($this->complexList);
  }

  private function setBuurtList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(city_region) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->city_region)) {
      $this->buurtList[] = $dao->city_region;
      }
    }
    $this->buurtList[] = 'Onbekend';
    asort($this->buurtList);
  }
  
  private function setWijkList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(block) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->block)) {
      $this->wijkList[] = $dao->block;
      }
    }
    $this->wijkList[] = 'Onbekend';
    asort($this->wijkList);
  }
  
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Mbreports_Config();
    }
    return self::$_singleton;
  }
}
