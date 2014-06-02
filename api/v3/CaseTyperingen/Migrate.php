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
function civicrm_api3_case_typeringen_migrate($params) {
  $oldTable = 'civicrm_value_typeringen_7';
  $oldOverlastField = 'type_overlast_45';
  $migratedTyperingen = 0;
  
  $typeringOptionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'ov_type', 'return' => 'id'));
  define('TYPERINGOPTIONGROUPID', $typeringOptionGroupId);
  
  $qryOld = 'SELECT a.entity_id, a.'.$oldOverlastField.', b.case_id FROM '
    .$oldTable.' a LEFT JOIN civicrm_case_activity b ON a.entity_id = b.activity_id
    WHERE '.$oldOverlastField.' != ""';
  CRM_Core_Error::debug('qryOld', $qryOld);
  exit();
  $daoOld = CRM_Core_DAO::executeQuery($qryOld);
  while ($daoOld->fetch()) {
    $newTyperingen = array();
    $oldTyperingen = explode(CRM_Core_DAO::VALUE_SEPARATOR, $daoOld->$oldOverlastField);
    foreach ($oldTyperingen as $oldTypering) {
      if (!empty($oldTypering)) {
        $newTyperingen[] = _get_new_typering($oldTypering);
      }
    }
    if (!empty($newTyperingen)) {
      $newTypering = CRM_Core_DAO::VALUE_SEPARATOR.implode(CRM_Core_DAO::VALUE_SEPARATOR, $newTyperingen).CRM_Core_DAO::VALUE_SEPARATOR;
      $insert = "REPLACE INTO civicrm_value_ov_data SET entity_id = %1, ov_type = %2";
      $params = array(1 => array($daoOld->case_id, 'Positive'), array($newTypering, 'String'));
      CRM_Core_DAO::executeQuery($insert, $params);
      $migratedTyperingen++;
    }
  }
  $returnValues = array('is_error' => 0, $migratedTyperingen.' overgezet.');
  return civicrm_api3_create_success($returnValues, $params, 'CaseTyperingen', 'Migrate');
}
/**
 * Function to get new typering
 */
function _get_new_typering($inTypering) {
  $params = array(
    'option_group_id' =>  TYPERINGOPTIONGROUPID,
    'label' => $inTypering,
    'return' => 'value' 
  );
  try {
    $outTypering = civicrm_api3('OptionValue', 'Getvalue', $params);
  } catch (CiviCRM_API3_Exception $ex) {
    $outTypering = '';
  }
  return $outTypering;
}

