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
  public $activityStatusTypeOptionGroupId = NULL;
  public $activityStatus = array();
  public $ontruimingActTypeId = NULL;
  public $vonnisActTypeId = NULL;
  
  public $activityTypeOntruimingValue = NULL;
  
  public $hoofdhuurderRelationshipTypeId = NULL;
  public $medehuurderRelationshipTypeId = NULL;
  public $dossierManagerRelationshipTypeId = NULL;
  public $deurwaarderList = array();
  public $deurwaarderRelationshipTypeId = NULL;
  public $dossierManagerList = array();
  public $complexList = array();
  public $buurtList = array();
  public $wijkList = array();
  public $VgeTypeList = array();
  
  // huurovereekomst household
  public $hovHouseholdGroupName = NULL;
  public $hovHouseholdGroupId = NULL;
  public $hovHouseholdTableName = NULL;
  public $hovHouseholdCustomFields = array();
  
  // aanvullende persoonsgegevens
  private $perGegevensCustomGroupName = 'Aanvullende_persoonsgegevens';
  public $perGegevensCustomGroupId = 0;
  public $perGegevensCustomTableName = '';
  public $perGegevensCustomFields = [];
  
  /*
   * custom group for case type Woonfraude
   */
  public $wfMelderCustomGroupName = NULL;
  public $wfMelderCustomGroupId = NULL;
  public $wfMelderCustomTableName = NULL;
  public $wfMelderList = array();
  
  public $wfUitkomstCustomGroupName = NULL;
  public $wfUitkomstCustomGroupId = NULL;
  public $wfUitkomstCustomTableName = NULL;
  
  public $wfTypeCustomFieldName = NULL;
  public $wfMelderCustomFieldName = NULL;
  public $wfUitkomstCustomFieldName = NULL;
  public $wfUitkomstActieNaVonnisCustomFieldName = NULL;
  
  public $wfTypeColumnName = NULL;
  public $wfTypeList = array();
  public $wfMelderColumnName = NULL;
  public $wfUitkomstColumnName = NULL;
  public $wfUitkomstList = array();
  public $woonfraudeCaseTypeId = NULL;
  
  public $changeCaseStatusActTypeId = NULL;
  /*
   * custom group for case type Overlast
   */
  public $ovCustomGroupName = NULL;
  public $ovCustomGroupId = NULL;
  public $ovCustomTableName = NULL;
  public $ovTypeCustomFieldName = NULL;
  
  public $ovUitkomstCustomGroupName = NULL;
  public $ovUitkomstCustomGroupId = NULL;
  public $ovUitkomstCustomTableName = NULL;
  public $ovUitkomstCustomFieldName = NULL;
  public $ovUitkomstColumnName = NULL;
  public $ovUitkomstList = array();
    
  public $ovTypeColumnName = NULL;
  public $ovTypeList = array();
  public $overlastCaseTypeId = NULL;
  public $ovTypeOptionGroupId = NULL;
  
  /*
   * custom group for activity type Vonnis gegevens
   */
  public $vongegeCustomGroupName = NULL;
  public $vongegeCustomGroupId = NULL;
  public $vongegeCustomTableName = NULL;
  public $vongegeDeurCustomFieldName = NULL;
  
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
    $this->setActTypeId('ontruiming');
    $this->setActTypeId('vonnis');
    $this->setWfUitkomstCustomGroupName('wf_uitkomst');
    $this->setWfMelderCustomGroupName('wf_data');
    $this->setWfMelderCustomFieldName('wf_melder');
    $this->setWfTypeCustomFieldName('wf_type');
    $this->setWfUitkomstCustomFieldName('wf_uitkomst');
    $this->setWfUitkomstActieNaVonnisCustomFieldName('anv_uitkomst');
    $this->setWoonfraude();
    $this->setWfTypeList();
    $this->setWfMelderList();
    $this->setWfUitkomstList();
    
    // huurovereenkomst household
    $this->setHovHouseholdCustomGroupName('Huurovereenkomst (huishouden)');
    $this->setHovHousehold();
    
    // aanvulldende persoonsgegevens
    $this->setPerGegevensCustomGroup();
    
    $this->setOvCustomGroupName('ov_data');
    $this->setOvTypeCustomFieldName('ov_type');
    $this->setOvUitkomstCustomGroupName('wf_uitkomst');
    $this->setOvUitkomstCustomFieldName('Overlast_uitkomst');
    $this->setOverlast();
    $this->setOvTypeList();
    $this->setActTypeId('Change Case Status');
    $this->setValidCaseTypes();
    $this->setOvUitkomstList();
    
    $this->setVongegeCustomGroupName('vonnis_gegevens');
    $this->setVongegeDeurCustomFieldName('deurwaarder_nummer');
    $this->setVonnisGegevens();
    
    $this->setActivityStatusTypeOptionGroupId();
    $this->setActivityStatus();
    
    $this->setHoofdhuurderRelationshipTypeId();
    $this->setMedehuurderRelationshipTypeId();
    $this->setDossierManagerRelationshipTypeId();
    $this->setDossierManagerList();
    $this->setDeurwaarderRelationshipTypeId();
    $this->setDeurwaarderList();
    $this->setCaseTypes();
    $this->setBuurtList();
    $this->setComplexList();
    $this->setWijkList();
    $this->setVgeTypeList();
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
  
  // Huurovereenkomst household
  public function setHovHouseholdCustomGroupName($hovHouseholdCustomGroupName) {
    $this->hovHouseholdCustomGroupName = $hovHouseholdCustomGroupName;
  }
  
  public function setHovHouseholdCustomGroupId($hovHouseholdCustomGroupId) {
    $this->hovHouseholdCustomGroupId = $hovHouseholdCustomGroupId;
  }
  
  public function setHovHouseholdCustomTableName($hovHouseholdCustomTableName) {
    $this->hovHouseholdCustomTableName = $hovHouseholdCustomTableName;
  }
  
  // Woonfraude
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
  
  public function setWfUitkomstActieNaVonnisCustomFieldName($wfUitkomstActieNaVonnisCustomFieldName) {
    $this->wfUitkomstActieNaVonnisCustomFieldName = $wfUitkomstActieNaVonnisCustomFieldName;
  }
  
  public function setWfUitkomstColumnName($wfUitkomstColumnName) {
    $this->wfUitkomstColumnName = $wfUitkomstColumnName;
  }
  
  // Overlast
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
  
  // Overlast Uitkomst
  public function setOvUitkomstCustomGroupName($ovUitkomstCustomGroupName) {
    $this->ovUitkomstCustomGroupName = $ovUitkomstCustomGroupName;
  }
  
  public function setOvUitkomstCustomGroupId($ovUitkomstCustomGroupId) {
    $this->ovUitkomstCustomGroupId = $ovUitkomstCustomGroupId;
  }
  
  public function setOvUitkomstCustomTableName($ovUitkomstCustomTableName) {
    $this->ovUitkomstCustomTableName = $ovUitkomstCustomTableName;
  }
  
  public function setOvUitkomstCustomFieldName($ovUitkomstCustomFieldName) {
    $this->ovUitkomstCustomFieldName = $ovUitkomstCustomFieldName;
  }
  
  public function setOvUitkomstColumnName($ovUitkomstColumnName) {
    $this->ovUitkomstColumnName = $ovUitkomstColumnName;
  }
  
  // Overlast Type
  public function setOvTypeColumnName($ovTypeColumnName) {
    $this->ovTypeColumnName = $ovTypeColumnName;
  }
  
  public function setVongegeCustomGroupName($vongegeCustomGroupName) {
    $this->vongegeCustomGroupName = $vongegeCustomGroupName;
  }
  
  public function setVongegeCustomGroupId($vongegeCustomGroupId) {
    $this->vongegeCustomGroupId = $vongegeCustomGroupId;
  }
  
  public function setVongegeCustomTableName($vongegeCustomTableName) {
    $this->vongegeCustomTableName = $vongegeCustomTableName;
  }
  
  public function setVongegeDeurCustomFieldName($vongegeDeurCustomFieldName) {
    $this->vongegeDeurCustomFieldName = $vongegeDeurCustomFieldName;
  }
  
  // Huurovereenkomst Household
  private function setHovHousehold(){
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->hovHouseholdCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setHovHouseholdCustomGroupId(0);
      $this->setHovHouseholdCustomTableName('');
      throw new Exception('Could not find a group with name '.$this->hovHouseholdCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setHovHouseholdCustomGroupId($customGroup['id']);
    $this->setHovHouseholdCustomTableName($customGroup['table_name']);
    $this->setHovHouseholdCustomFields();
  }
  
  private function setHovHouseholdCustomFields(){    
    try {
      $customFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $this->hovHouseholdCustomGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find custom fields with group id '.$this->hovHouseholdCustomGroupId
        .' in custom group '.$this->hovHouseholdCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    
    foreach ($customFields['values'] as $custom_field){
      $this->hovHouseholdCustomFields[$custom_field['name']] = $custom_field;
    }
  }
  
  // Aanvullende persoonsgegevens
  private function setPerGegevensCustomGroup(){
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->perGegevensCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a group with name '.$this->perGegevensCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->perGegevensCustomGroupId = $customGroup['id'];
    $this->perGegevensCustomTableName = $customGroup['table_name'];
    $this->setPerGegevensCustomFields();
  }
  
  private function setPerGegevensCustomFields(){    
    try {
      $customFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $this->perGegevensCustomGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find custom fields with group id '.$this->perGegevensCustomGroupId
        .' in custom group '.$this->perGegevensCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    
    foreach ($customFields['values'] as $custom_field){
      $this->perGegevensCustomFields[$custom_field['name']] = $custom_field;
    }
  }
  
  public function getPerNummerFirst($contact_id){
    if(empty($contact_id)){
      return false;
    }
    
    try { 
      $query = "SELECT per.entity_id,
        per." . $this->perGegevensCustomFields['Persoonsnummer_First']['column_name'] . " as `Persoonsnummer_First`,
        per." . $this->perGegevensCustomFields['BSN']['column_name'] . " as `BSN`,
        per." . $this->perGegevensCustomFields['Burgerlijke_staat']['column_name'] . " as `Burgerlijke_staat`,
        per." . $this->perGegevensCustomFields['Totaal_debiteur']['column_name'] . " as `Totaal_debiteur`
        FROM " . $this->perGegevensCustomTableName . " as per
        WHERE per.entity_id = '%1'
      ";
      $params = array( 
          1 => array($contact_id, 'Integer'),
      );

      if(!$dao = CRM_Core_DAO::executeQuery($query, $params)){
        $return['is_error'] = true;
        $return['error_message'] = sprintf('Failed execute query (%s) !', $query);
        if($debug){
          echo $return['error_message'] . '<br/>' . PHP_EOL;
        }
        return $return;
      }

      $dao->fetch();

      return $dao;
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find per with contact id '.$contact_id
        .' in getPerNummerFirst, error from CRM_Core_DAO executeQuery error :'.$ex->getMessage() . ', $query: ' . $query . ' $params: ' . $params);
    }
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
    
    $customFieldParams['custom_group_id'] = $this->wfUitkomstCustomGroupId;
    $customFieldParams['return'] = 'column_name';
    $customFieldParams['name'] = $this->wfUitkomstActieNaVonnisCustomFieldName;
    try {
      $this->setWfUitkomstActieNaVonnisCustomFieldName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setWfUitkomstActieNaVonnisCustomFieldName('');
      throw new Exception('Could not find custom field with name '.$this->wfUitkomstActieNaVonnisCustomFieldName
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
    
    // Overlast Uitkomst
    try {
      $customGroupUitkomst = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->ovUitkomstCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setOvUitkomstCustomGroupId(0);
      $this->setOvUitkomstCustomTableName('');
      $this->setOvUitkomstCustomFieldName('');
      $this->setOvUitkomstColumnName('');
      throw new Exception('Could not find a group with name '.$this->ovUitkomstCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setOvUitkomstCustomGroupId($customGroupUitkomst['id']);
    $this->setOvUitkomstCustomTableName($customGroupUitkomst['table_name']);    
    
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
    
    // Overlast uitkomst
    $customFieldParams['custom_group_id'] = $this->ovUitkomstCustomGroupId;
    $customFieldParams['name'] = $this->ovUitkomstCustomFieldName;
    try {
      $this->setOvUitkomstColumnName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setOvUitkomstColumnName('');
      throw new Exception('Could not find custom field with name '.$this->ovTypeCustomFieldName
        .' in custom group '.$this->ovUitkomstCustomGroupId.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
  }
  
  private function setVonnisGegevens() {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->vongegeCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setVongegeCustomGroupId(0);
      $this->setVongegeCustomTableName('');
      $this->setVongegeDeurCustomFieldName('');
      throw new Exception('Could not find a group with name '.$this->vongegeCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->setVongegeCustomGroupId($customGroup['id']);
    $this->setVongegeCustomTableName($customGroup['table_name']);
    $this->vongegeCustomFields();
  }
  
  private function vongegeCustomFields() {
    $customFieldParams['custom_group_id'] = $this->vongegeCustomGroupId;
    $customFieldParams['return'] = 'column_name';
    $customFieldParams['name'] = $this->vongegeDeurCustomFieldName;
    try {
      $this->setVongegeDeurCustomFieldName(civicrm_api3('CustomField', 'Getvalue', $customFieldParams));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setVongegeDeurCustomFieldName('');
      throw new Exception('Could not find custom field with name '.$this->vongegeDeurCustomFieldName
        .' in custom group '.$this->vongegeCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
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
  
  private function setDeurwaarderRelationshipTypeId() {
    $params = array(
      'name_a_b'  =>  'Deurwaarder',
      'return'    =>  'id');
    try {
      $this->deurwaarderRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->deurwaarderRelationshipTypeId = 0;
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
  
  private function setMedehuurderRelationshiptypeId() {
    $params = array(
      'name_a_b'  =>  'Medehuurder',
      'return'    =>  'id');
    try {
      $this->medehuurderRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->medehuurderRelationshipTypeId = 0;
    }
  }
  
  private function setValidCaseTypes() {
    $this->validCaseTypes = array('ActienaVonnis', 'Buitenkanstraject', 'Huurbemiddeling', 'Overlast', 
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
  
  private function setActivityStatusTypeOptionGroupId() {
    $params = array('name' => 'activity_status', 'return' => 'id');
    try {
      $this->activityStatusTypeOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->activityStatusTypeOptionGroupId = 0;
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
  
  private function setActivityStatus() {
    $params = array('option_group_id' => $this->activityStatusTypeOptionGroupId);
    try {      
      $apiActivityStatuses = civicrm_api3('OptionValue', 'Get', $params);
      foreach ($apiActivityStatuses['values'] as $apiActivityStatus) {
        $this->activityStatus[$apiActivityStatus['value']] = $apiActivityStatus['label'];
      }
    } catch (CiviCRM_API3_Exception $ex) {
      $this->activityStatus = array();
    }
    asort($this->activityStatus);
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
  
  private function setDeurwaarderList() {
    /*
     * retrieve all relationships dossiermanager
     */
    $query = 'SELECT DISTINCT(contact_id_b) as manager_id, display_name FROM civicrm_relationship '
      . 'JOIN civicrm_contact cc ON contact_id_b = cc.id WHERE relationship_type_id = %1 AND case_id IS NOT NULL';
    $params = array(1 => array($this->deurwaarderRelationshipTypeId, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while($dao->fetch()) {
      $this->deurwaarderList[$dao->manager_id] = $dao->display_name;
    }
    asort($this->deurwaarderList);
  }
  
  private function setOvTypeList() {
    $params = array(
      'name'            =>  $this->ovTypeCustomFieldName,
      'custom_group_id' =>  $this->ovCustomGroupId,
      'return'          =>  'option_group_id');
    $this->ovTypeOptionGroupId = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $this->ovTypeOptionGroupId));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->ovTypeList[$optionValue['value']] = $optionValue['label'];
    }
    asort($this->ovTypeList);
  }
  
  private function setOvUitkomstList() {
    $params = array(
      'name'            =>  $this->ovUitkomstCustomFieldName,
      'custom_group_id' =>  $this->ovUitkomstCustomGroupId,
      'return'          =>  'option_group_id');
    $OptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $OptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->ovUitkomstList[$optionValue['value']] = $optionValue['label'];
    }
    asort($this->ovUitkomstList);
  }

  private function setWfMelderList() {
    $params = array(
      'name'            =>  $this->wfMelderCustomFieldName,
      'custom_group_id' =>  $this->wfMelderCustomGroupId,
      'return'          =>  'option_group_id');
    $wfOptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $wfOptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->wfMelderList[$optionValue['value']] = $optionValue['label'];
    }
    asort($this->wfMelderList);
  }
  
  private function setWfTypeList() {
    $params = array(
      'name'            =>  $this->wfTypeCustomFieldName,
      'custom_group_id' =>  $this->wfUitkomstCustomGroupId,
      'return'          =>  'option_group_id');
    $wfOptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $wfOptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->wfTypeList[$optionValue['value']] = $optionValue['label'];
    }
    asort($this->wfTypeList);
  }
  
  private function setWfUitkomstList() {
    $params = array(
      'name'            =>  $this->wfUitkomstCustomFieldName,
      'custom_group_id' =>  $this->wfUitkomstCustomGroupId,
      'return'          =>  'option_group_id');
    $wfOptionGroup = civicrm_api3('CustomField', 'Getvalue', $params);
    $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $wfOptionGroup));
    foreach ($optionValues['values'] as $optionId => $optionValue) {
      $this->wfUitkomstList[$optionValue['value']] = $optionValue['label'];
    }
    asort($this->wfUitkomstList);
  }
  
  private function setComplexList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(complex_id) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->complex_id)) {
      $this->complexList[$dao->complex_id] = $dao->complex_id;
      }
    }
    $this->complexList[] = 'Onbekend';
    asort($this->complexList);
  }

  private function setBuurtList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(block) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->block)) {
      $this->buurtList[$dao->block] = $dao->block;
      }
    }
    $this->buurtList[] = 'Onbekend';
    asort($this->buurtList);
  }
  
  private function setWijkList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT DISTINCT(city_region) FROM civicrm_property');
    while ($dao->fetch()) {
      if (!empty($dao->city_region)) {
      $this->wijkList[$dao->city_region] = $dao->city_region;
      }
    }
    $this->wijkList[] = 'Onbekend';
    asort($this->wijkList);
  }
  
  private function setVgeTypeList() {
    $dao = CRM_Core_DAO::executeQuery('SELECT id, label FROM civicrm_property_type ORDER BY label ASC');
    while ($dao->fetch()) {
      $this->VgeTypeList[$dao->id] = $dao->label;
    }
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
