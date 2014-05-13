<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 May 2014
 */
class CRM_Mbreports_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  
  public $caseTypeOptionGroupId = NULL;
  public $actTypeOptionGroupId = NULL;
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
  public $overlastCaseTypeId = NULL;
  /**
   * Constructor function
   */
  function __construct() {
    $this->setCaseTypeId('woonfraude');
    $this->setCaseTypeId('overlast');
    $this->setActTypeId('change Case Status');
    $this->setWfUitkomstCustomGroupName('wf_uitkomst');
    $this->setWfMelderCustomGroupName('wf_melder');
    $this->setWfMelderCustomFieldName('wf_melder');
    $this->setWfTypeCustomFieldName('wf_type');
    $this->setWfUitkomstCustomFieldName('wf_uitkomst');
    $this->setWoonfraude();
    
    $this->setOvCustomGroupName('ov_type');
    $this->setOvTypeCustomFieldName('ov_type');
    $this->setOverlast();
    $this->setCaseTypeId('Woonfraude');
    $this->setCaseTypeId('Overlast');
    $this->setActTypeId('Change Case Status');
  }
  
  private function setCaseTypeId($caseTypeName) {
    $propertyName = strtolower(trim($caseTypeName)).'CaseTypeId';
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'case_type', 'return' => 'id'));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find option group for case_type, error from API OptionGroup Getvalue : '
        .$ex->getMessage());
    }
    $optionValueParams = array(
      'option_group_id' => $optionGroupId,
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
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'activity_type', 'return' => 'id'));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find option group for activity_type, error from API OptionGroup Getvalue : '
        .$ex->getMessage());
    }
    $optionValueParams = array(
      'option_group_id' => $optionGroupId,
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
