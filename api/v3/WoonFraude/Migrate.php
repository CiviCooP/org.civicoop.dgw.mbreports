<?php
/**
 * CaseTyperingen.Migrate API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_woon_fraude_migrate($params) {
  $oldTable = 'civicrm_value_typeringen_7';
  define('NEWTYPETABLE', 'civicrm_value_wf_uitkomst');
  define('NEWTYPEFIELD', 'wf_type');
  define('OLDTYPEFIELD', 'type_woonfraude_46');
  $oldMelderField = 'type_melder_woonfraude_47';
  
  $typeOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'wf_type', 'return' => 'id'));
  define('TYPEOPTIONGROUPID', $typeOptionGroupId);
  
  $melderOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'wf_melder', 'return' => 'id'));
  define('MELDEROPTIONGROUPID', $melderOptionGroupId);

  $qryOld = 'SELECT a.entity_id, a.'.OLDTYPEFIELD.', a. '.$oldMelderField
    .', b.case_id, c.contact_id FROM '.$oldTable.' a LEFT JOIN civicrm_case_activity 
    b ON a.entity_id = b.activity_id LEFT JOIN civicrm_case_contact c ON b.case_id = c.case_id 
    LEFT JOIN civicrm_activity d ON b.activity_id = d.id
    WHERE ('.OLDTYPEFIELD.' != "" OR '.$oldMelderField.' != "") AND is_current_revision = 1';
  $daoOld = CRM_Core_DAO::executeQuery($qryOld);
  while ($daoOld->fetch()) {
    $newMelder = _get_new_melder($daoOld->$oldMelderField);
    $insert = "REPLACE INTO civicrm_value_wf_data SET entity_id = %1, wf_melder = %2";
    $params = array(1 => array($daoOld->case_id, 'Positive'), array($newMelder, 'String'));
    CRM_Core_DAO::executeQuery($insert, $params);
    $oldTypeField = OLDTYPEFIELD;
    if (!empty($daoOld->$oldTypeField)) {
      _create_new_types($daoOld);
    }
  }
  $returnValues = array('is_error' => 0, 'Woonfraude typeringen en melders overgezet.');
  return civicrm_api3_create_success($returnValues, $params, 'WoonFraude', 'Migrate');
}
/**
 * Function to create activity for new types
 */
function _create_new_types($dao) {
  $oldTypeField = OLDTYPEFIELD;
  $oldTyperingen = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->$oldTypeField);
  $newTyperingen = array();
  foreach ($oldTyperingen as $oldTypering) {
    if (!empty($oldTypering)) {
      $newTyperingen[] = _get_new_type($oldTypering); 
    }
  }
  if (!empty($newTyperingen)) {
    $newTypering = CRM_Core_DAO::VALUE_SEPARATOR.implode(CRM_Core_DAO::VALUE_SEPARATOR, $newTyperingen).CRM_Core_DAO::VALUE_SEPARATOR;
    /*
     * create activity
     */
    $params = array(
      'source_contact_id' => 1,
      'activity_type_id' => 16,
      'subject' => 'Migratie woonfraude types '.date('d-m-Y').' (status is NIET veranderd)',
      'status_id' => 2,
      'priority_id' => 2,
      'medium_id' => 2,
      'is_current_revision' => 1,
      'is_deleted' => 0,
      'target_contact_id' => $dao->contact_id
    );
    $createdActivity = civicrm_api3('Activity', 'Create', $params);
    /*
     * create link in civicrm_case_activity
     */
    $caseActInsert = 'INSERT INTO civicrm_case_activity SET case_id = %1, activity_id = %2';
    $caseActParams = array(1 => array($dao->case_id, 'Positive'), 2 => array($createdActivity['id'], 'Positive'));
    CRM_Core_DAO::executeQuery($caseActInsert, $caseActParams);
    /*
     * create wf_type data
     */
    $wfTypeInsert = 'INSERT INTO '.NEWTYPETABLE.' SET entity_id = %1, '.NEWTYPEFIELD.' = %2';
    $wfTypeParams = array(1 => array($createdActivity['id'], 'Positive'), 2 => array($newTypering, 'String'));
    CRM_Core_DAO::executeQuery($wfTypeInsert, $wfTypeParams);
  }
}
/**
 * Function to get new melder
 */
function _get_new_melder($inMelder) {
  $params = array(
    'option_group_id' =>  MELDEROPTIONGROUPID,
    'label' => $inMelder,
    'return' => 'value' 
  );
  try {
    $outMelder = civicrm_api3('OptionValue', 'Getvalue', $params);
  } catch (CiviCRM_API3_Exception $ex) {
    $outMelder = '';
  }
  return $outMelder;
}
/**
 * Function to get new type
 */
function _get_new_type($inType) {
  $params = array(
    'option_group_id' =>  TYPEOPTIONGROUPID,
    'label' => $inType,
    'return' => 'value' 
  );
  try {
    $outType = civicrm_api3('OptionValue', 'Getvalue', $params);
  } catch (CiviCRM_API3_Exception $ex) {
    $outType = '';
  }
  return $outType;
}
