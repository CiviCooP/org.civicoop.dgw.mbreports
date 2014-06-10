<?php
/**
/**
 * Util functions for mbreports
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 14 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */

class CRM_Utils_MbreportsUtils {
  /**
   * Function to retrieve the VGE id for a case
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 May 2014
   * @param int $caseId
   * @return result $vgeData
   * @access public
   * @static
   */
  static public function getCaseVgeData($caseId) {
    if (empty($caseId) || !is_numeric(($caseId))) {
      return array();
    }
    $caseClients = CRM_Case_BAO_Case::getCaseClients($caseId);
    /*
     * assume first one is the one we need, De Goede Woning do not assign more
     * customers to a case
     */
    if (!empty($caseClients)) {
      $clientId = $caseClients[0];
    }
    if (self::checkHuishouden($clientId) == FALSE) {
      $huishoudenId = self::getHuishouden($clientId);
    } else {
      $huishoudenId = $clientId;
    }
    $vgeData = self::getHuishoudenVgeData($huishoudenId);
    return $vgeData;
  }
  
  /**
   * Function to get vge_data from huurovereenkomst for huishouden
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 May 2014
   * @param int $huishoudenId
   * @return array $vgeData
   * @access public
   * @static
   * 
   * @todo Decide on and include some processing if no active vge found?
   */
  public static function getHuishoudenVgeData($huishoudenId) {
    $huurOvereenkomsten = CRM_Utils_DgwUtils::getVgeHuurovereenkomst($huishoudenId);
    return array_shift($huurOvereenkomsten);
  }
  /**
   * Public static function to retrieve huishoudenID that belongs to a contact
   * First check if contact has relationship Hoofdhuurder and if so, retrieve
   * huishouden.
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 May 2014
   * @param int $contactId
   * @return int $huishoudenId
   * @access public
   * @static
   */
  public static function getHuishouden($contactId) {
    $huishoudenId = NULL;
    if (CRM_Utils_DgwUtils::checkContactHoofdhuurder($contactId) == TRUE) {
      $huishoudenId = self::getHuishoudenId($contactId, 'hoofdhuurder');
      return $huishoudenId;
    }
    if (CRM_Utils_DgwUtils::checkContactMedehuurder($contactId) == TRUE) {
      $huishoudenId = self::getHuishoudenId($contactId, 'medehuurder');
      return $huishoudenId;
    }
    return $huishoudenId;
  }
  /**
   * Function to check if incoming contact_id is household
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 May 2014
   * @param int $contactId
   * @return boolean
   * @access public
   * @static
   */
  static public function checkHuishouden($contactId) {
    $params = array('id' => $contactId, 'return' => 'contact_type');
    try {
      $contactType = civicrm_api3('Contact', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
    if ($contactType == 'Household') {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  static private function getHuishoudenId($contactId, $type) {
    $mbreportsConfig = CRM_Mbreports_Config::singleton();
    $query = 'SELECT contact_id_b FROM civicrm_relationship WHERE relationship_type_id = %1 AND contact_id_a = %2 ORDER BY end_date DESC';
    if ($type == 'hoofdhuurder') {
    $params = array(
      1 => array($mbreportsConfig->hoofdhuurderRelationshipTypeId, 'Integer'),
      2 => array($contactId, 'Integer')
      );
    }
    if ($type == 'medehuurder') {
    $params = array(
      1 => array($mbreportsConfig->medehuurderRelationshipTypeId, 'Integer'),
      2 => array($contactId, 'Integer')
      );
    }
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {
      return $dao->contact_id_b;
    } else {
      return 0;
    }
  }
  
  public static function getCaseHoofdhuurder($caseId){
    if (empty($caseId) || !is_numeric(($caseId))) {
      return array();
    }
    $caseClients = CRM_Case_BAO_Case::getCaseClients($caseId);
    
    /*
     * assume first one is the one we need, De Goede Woning do not assign more
     * customers to a case
     */
    if (!empty($caseClients)) {
      $clientId = $caseClients[0];
    }
    
    if (self::checkHuishouden($clientId) == FALSE) {
      $huishoudenId = self::getHuishoudenId($clientId, 'hoofdhuurder');
    } else {
      $huishoudenId = $clientId;
    }
    return CRM_Utils_DgwUtils::getHoofdhuurders($huishoudenId, false);
  }
  
  public static function getCaseMedehuurder($caseId){
    if (empty($caseId) || !is_numeric(($caseId))) {
      return array();
    }
    $caseClients = CRM_Case_BAO_Case::getCaseClients($caseId);
    /*
     * assume first one is the one we need, De Goede Woning do not assign more
     * customers to a case
     */
    if (!empty($caseClients)) {
      $clientId = $caseClients[0];
    }
    if (self::checkHuishouden($clientId) == FALSE) {
      $huishoudenId = self::getHuishoudenId($clientId, 'medehuurder');
    } else {
      $huishoudenId = $clientId;
    }
    
    return CRM_Utils_DgwUtils::getMedeHuurders($huishoudenId, false);
  }
}