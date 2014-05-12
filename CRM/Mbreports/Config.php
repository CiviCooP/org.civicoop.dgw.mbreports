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
  /*
   * custom group for case type Overlast
   */
  public $ovCustomGroupName = NULL;
  public $ovCustomGroupId = NULL;
  public $ovCustomTableName = NULL;
  public $ovTypeCustomFieldName = NULL;
  public $ovTypeColumnName = NULL;
  /**
   * Constructor function
   */
  function __construct() {
    $this->setWfUitkomstCustomGroupName('wf_uitkomst');
    $this->setWfMelderCustomGroupName('wf_melder');
    $this->setWfMelderCustomFieldName('wf_melder');
    $this->setWoonfraude();
    $this->setOvCustomGroupName('Overlastdata');
    $this->setOverlast();
  }
  
  private function setWfMelderCustomGroupName($wfMelderCustomGroupName) {
    $this->wfMelderCustomGroupName = $wfMelderCustomGroupName;
  }
  
  private function setWfUitkomstCustomGroupName($wfUitkomstCustomGroupName) {
    $this->wfUitkomstCustomGroupName = $wfUitkomstCustomGroupName;
  }
  
  private function setWfMelderCustomGroupId($wfMelderCustomGroupId) {
    $this->wfMelderCustomGroupId = $wfMelderCustomGroupId;
  }
  
  private function setWfUitkomstCustomGroupId($wfUitkomstCustomGroupId) {
    $this->wfUitkomstCustomGroupId = $wfUitkomstCustomGroupId;
  }
  
  private function setWfUitkomstCustomTableName($wfUitkomstCustomTableName) {
    $this->wfUitkomstCustomTableName = $wfUitkomstCustomTableName;
  }
  
  private function setWfMelderCustomTableName($wfMelderCustomTableName) {
    $this->wfMelderCustomTableName = $wfMelderCustomTableName;
  }
  
  private function setWfTypeCustomFieldName($wfTypeCustomFieldName) {
    $this->wfTypeCustomFieldName = $wfTypeCustomFieldName;
  }
  
  private function setWfTypeColumnName($wfTypeColumnName) {
    $this->wfTypeColumnName = $wfTypeColumnName;
  }
  
  private function setWfMelderCustomFieldName($wfMelderCustomFieldName) {
    $this->wfMelderCustomFieldName = $wfMelderCustomFieldName;
  }
  
  private function setWfMelderColumnName($wfMelderColumnName) {
    $this->wfMelderColumnName = $wfMelderColumnName;
  }
  
  private function setWfUitkomstCustomFieldName($wfUitkomstCustomFieldName) {
    $this->wfUitkomstCustomFieldName = $wfUitkomstCustomFieldName;
  }
  
  private function setWfUitkomstColumnName($wfUitkomstColumnName) {
    $this->wfUitkomstColumnName = $wfUitkomstColumnName;
  }
  
  private function setOvCustomGroupName($ovCustomGroupName) {
    $this->ovCustomGroupName = $ovCustomGroupName;
  }
  
  private function setOvCustomGroupId($ovCustomGroupId) {
    $this->ovCustomGroupId = $ovCustomGroupId;
  }
  
  private function setOvCustomTableName($ovCustomTableName) {
    $this->ovCustomTableName = $ovCustomTableName;
  }
  
  private function setOvTypeCustomFieldName($ovTypeCustomFieldName) {
    $this->ovTypeCustomFieldName = $ovTypeCustomFieldName;
  }
  
  private function setOvTypeColumnName($ovTypeColumnName) {
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
      self::$_singleton = new CRM_Threepeas_Config();
    }
    return self::$_singleton;
  }
}
