<?php
/**
/**
 * Util functions for mbreports
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Jan-Derek Vos (CiviCooP) <helpdesk@civicoop.org>
 * @date 12 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */

class CRM_Mbreports_Form_Report_WerkoverzichtDossier extends CRM_Report_Form {
  
  function __construct() {
    //$config = CRM_Mbreports_Config::singleton();
    
    // case types
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name' => 'case_types',
    );
    $result = civicrm_api('OptionGroup', 'getsingle', $params);

    $params = array(
      'version' => 3,
      'sequential' => 1,
      'option_group_id' => $result['id'],
    );
    $result = civicrm_api('OptionValue', 'get', $params);
    
    $case_types = array();
    $case_types[''] = ts('- elke - ');
    foreach($result['values'] as $key => $case_type){
      $case_types[$case_type['id']] = $case_type['label'];
    }
    
    // case status
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name' => 'case_status',
    );
    $result = civicrm_api('OptionGroup', 'getsingle', $params);
    
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'option_group_id' => $result['id'],
    );
    $result = civicrm_api('OptionValue', 'get', $params);
    
    $case_statuses = array();
    $case_statuses[''] = ts('- elke - ');
    foreach($result['values'] as $key => $case_status){
      $case_statuses[$case_status['id']] = $case_status['label'];
    }
    
    // relationship_contact_id_a_dossiermanager
    // name_a_b = Dossiermanager
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name_a_b' => 'Dossiermanager',
    );
    $result = civicrm_api('RelationshipType', 'getsingle', $params);
        
    $query = "SELECT civicrm_contact.id, civicrm_contact.sort_name FROM civicrm_contact ";
    $query .= "LEFT JOIN civicrm_relationship ON civicrm_contact.id = civicrm_relationship.contact_id_b ";
    $query .= "WHERE civicrm_relationship.case_id != 'NULL' ";
    $query .= "AND civicrm_relationship.relationship_type_id = '" . $result['id'] . "' ";
    $query .= "GROUP BY civicrm_contact.id ORDER BY civicrm_contact.sort_name ASC";
    $dao = CRM_Core_DAO::executeQuery($query); 
    
    $dossiermanagers = array();
    while($dao->fetch()){
      $dossiermanagers[$dao->id] = $dao->sort_name;
    }
    
    // deurwaarder
    // name_a_b = Deurwaarder
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name_a_b' => 'Deurwaarder',
    );
    $result = civicrm_api('RelationshipType', 'getsingle', $params);
    
    $query = "SELECT civicrm_contact.id, civicrm_contact.sort_name FROM civicrm_contact ";
    $query .= "LEFT JOIN civicrm_relationship ON civicrm_contact.id = civicrm_relationship.contact_id_b ";
    $query .= "WHERE civicrm_relationship.case_id != 'NULL' ";
    $query .= "AND civicrm_relationship.relationship_type_id = '" . $result['id'] . "' ";
    $query .= "GROUP BY civicrm_contact.id ORDER BY civicrm_contact.sort_name ASC";
    $dao = CRM_Core_DAO::executeQuery($query); 
    
    $deurwaarders = array();
    while($dao->fetch()){
      $deurwaarders[$dao->id] = $dao->sort_name;
    }
    
    // property.complex
    $query = "SELECT complex_id FROM civicrm_property GROUP BY complex_id ORDER BY complex_id ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $complex_ids = array();
    $complex_ids[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->complex_id)){
        $complex_ids[$dao->complex_id] = $dao->complex_id;
      }
    }
    
    // property.city_region
    $query = "SELECT city_region FROM civicrm_property GROUP BY city_region ORDER BY city_region ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $city_regions = array();
    $city_regions[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->city_region)){
        $city_regions[$dao->city_region] = $dao->city_region;
      }
    }
    
    // property.block
    $query = "SELECT block FROM civicrm_property GROUP BY block ORDER BY block ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $blocks = array();
    $blocks[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->block)){
        $blocks[$dao->block] = $dao->block;
      }
    }
    
    // property.vge_type_id
    $query = "SELECT id, label FROM civicrm_property_type ORDER BY label ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $vge_type_ids = array();
    $vge_type_ids[''] = ts('- elke - ');
    while($dao->fetch()){
      $vge_type_ids[$dao->id] = $dao->label;
    }
    
    // ontruiming.status_id
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name' => 'activity_status',
    );
    $result = civicrm_api('OptionGroup', 'getsingle', $params);

    $params = array(
      'version' => 3,
      'sequential' => 1,
      'option_group_id' => $result['id'],
    );
    $result = civicrm_api('OptionValue', 'get', $params);
    
    $activity_statuss = array();
    $activity_statuss[''] = ts('- elke - ');
    foreach($result['values'] as $key => $activity_status){
      $activity_statuss[$activity_status['id']] = $activity_status['label'];
    }
    
    $this->_columns = array(
      'civicrm_case' =>
      array(
        'dao' => 'CRM_Case_DAO_Case',
        'fields' => array(
          'CA.id' =>
          array('title' => ts('Dossier ID'),
            'required' => TRUE,
          ),
          'CA.subject' =>
          array('title' => ts('Dossier onderwerp'),
            'required' => TRUE,
          ),
          'CA.case_type_id' =>
          array('title' => ts('Dossier type'),
            'required' => TRUE,
          ),
          'CA.status_id' =>
          array('title' => ts('Dossier status'),
            'required' => TRUE,
          ),
          'CA.start_date' =>
          array('title' => ts('Dossier begindatum'),
            //'required' => TRUE,
          ),
          'TYPE.typeringen' =>
          array('title' => ts('Typeringen')
          ),
          'dossiermanager' => 
          array('title' => ts('Dossiermanager')
          ),
          'deurwaarder' => 
          array('title' => ts('Deurwaarder')
          ),
          // J / N (Ja of Nee) ontruimt, ontruim id is 41
          'ontruiming' =>
          array('title' => ts('Ontruiming')
          ),
          'ontruiming_status_id' =>
          array('title' => ts('Ontruiming status')
          ),
          'ontruiming_activity_date_time' => 
          array('title' => ts('Ontruiming datum')
          ),
          // J / N (Ja of Nee) vonnis, vonnis id = 40
          'vonnis' =>
          array('title' => ts('Vonnis')
          ),
          'vonnis_activity_date_time' => 
          array('title' => ts('Vonnis datum')
          ),
        ),
        'filters' => array(
          'CA.case_type_id' => array(
            'title' => ts('Dossier type'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $case_types,
          ),
          'CA.status_id' => array(
            'title' => ts('Dossier status'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $case_statuses,
          ),
          'CA.start_date' => array(
            'title' => ts('Dossier begindatum'),
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
          'dossiermanager' => array(
            'title' => ts('Dossiermanager'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $dossiermanagers,
          ),
          'deurwaarder' => array(
            'title' => ts('Deurwaarder'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $deurwaarders,
          ),
          'ontruiming' => array(
            'title' => ts('Ontruiming'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => array('' => ts('- elke - '), 'J' => ts('Ja'), 'N' => ts('Nee')),
          ),
          'ontruiming_status_id' => array(
            'title' => ts('Ontruiming status '),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $activity_statuss,
          ),
        ),
        'order_bys' =>
        array(
          'CA.id' =>
          array(
            'name' => 'id',
            'title' => ts('Dossier ID'),
            'alias' => 'id',
          ),
          'TYPE.typeringen' => 
          array(
            'name' => 'typeringen',
            'title' => ts('Typeringen'),
            'alias' => 'typeringen',
          ),
          'CA.status_id' => 
          array(
            'name' => 'status_id',
            'title' => ts('Dossier status'),
            'alias' => 'status_id',
          ),
          'CA.start_date' => 
          array(
            'name' => 'start_date',
            'title' => ts('Dossier begindatum'),
            'alias' => 'start_date',
          ),
          'dossiermanager' => 
          array(
            'name' => 'dossiermanager',
            'title' => ts('Dossiermanager'),
            'alias' => 'dossiermanager',
          ),
        ),
      ),
          
      // property
      'civicrm_property' => array(
        'dao' => 'CRM_Core_DAO_CustomField',
        'fields' => array(
          'PROP.vge_id' => array(
            'title' => ts('VGE nummer'),
          ),
          'PROP.complex_id' => array(
            'title' => ts('Complex'),
          ),
          'PROP.block' => array(
            'title' => ts('Wijk'),
          ),
          'PROP.city_region' => array(
            'title' => ts('Buurt'),
          ),
          'PROP.vge_type_id' => array(
            'title' => ts('VGE type'),
          ),
        ),
        'filters' => array(
          'PROP.complex_id' => array(
            'title' => ts('Complex'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $complex_ids,
          ),
          'PROP.block' => array(
            'title' => ts('Wijk'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $blocks,
          ),
          'PROP.city_region' => array(
            'title' => ts('Buurt'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $city_regions,
          ),
          'PROP.vge_type_id' => array(
            'title' => ts('VGE type'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $vge_type_ids,
          ),
        ),
        'order_bys' =>
        array(
          'PROP.complex_id' =>
          array(
            'name' => 'complex_id',
            'title' => ts('Complex'),
            'alias' => 'complex_id',
          ),
          'PROP.block' => 
          array(
            'name' => 'block',
            'title' => ts('Wijk'),
            'alias' => 'block',
          ),
          'PROP.city_region' => 
          array(
            'name' => 'city_region',
            'title' => ts('Buurt'),
            'alias' => 'city_region',
          ),
          'PROP.vge_type_id' => 
          array(
            'name' => 'vge_type_id',
            'title' => ts('VGE type'),
            'alias' => 'vge_type_id',
          ),
        ),
      ),
      
      // hoofdhuurder
      'hoofdhuurder' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'hoofdhuurder' => array(
            'title' => ts('Hoofdhuurder naam'),
              'required' => TRUE,
          ),
          'hoofdhuurder_street_address' => array(
            'title' => ts('Hoofdhuurder adres'),
              'required' => TRUE,
          ),
          'hoofdhuurder_email' => array(
            'title' => ts('Hoofdhuurder e-mail'),
          ),
          'hoofdhuurder_phone' => array(
            'title' => ts('Hoofdhuurder telefoon'),
          ),
        ),
      ),
            
      // medehuurder
      'medehuurder' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'medehuurder' => array(
            'title' => ts('Medehuurder naam'),
          ),
          'medehuurder_email' => array(
            'title' => ts('Medehuurder e-mail'),
          ),
          'medehuurder_phone' => array(
            'title' => ts('Medehuurder telefoon'),
          ),
        ),
      ),
    );
    
    parent::__construct();
  }

  function preProcess() {
    $this->assign('reportTitle', ts('Werkoverzicht dossier'));
    parent::preProcess();
  }
  
  function postProcess() {

    $this->beginPostProcess();

    echo('<pre>');
    print_r($this->_submitValues);
    echo('</pre>');
    
    // select
    $sql = "SELECT";
        
    // fields
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){
        case 'dossiermanager':
          $sql .= " (SELECT DOSMCON.sort_name FROM civicrm_contact AS DOSMCON";
          $sql .= " LEFT JOIN civicrm_relationship AS DOSMRE ON DOSMCON.id = DOSMRE.contact_id_b ";
          $sql .= " LEFT JOIN civicrm_relationship_type AS DOSMRETY ON DOSMRE.relationship_type_id = DOSMRETY.id ";
          $sql .= " WHERE DOSMRETY.name_a_b = 'Dossiermanager'";
          $sql .= " AND DOSMRE.case_id = CA.id";
          $sql .= " ) AS dossiermanager,";
          break;
        
        case 'deurwaarder':
          $sql .= " (SELECT DEURCON.sort_name FROM civicrm_contact AS DEURCON";
          $sql .= " LEFT JOIN civicrm_relationship AS DEURRE ON DEURCON.id = DEURRE.contact_id_b ";
          $sql .= " LEFT JOIN civicrm_relationship_type AS DEURRETY ON DEURRE.relationship_type_id = DEURRETY.id ";
          $sql .= " WHERE DEURRETY.name_a_b = 'Deurwaarder'";
          $sql .= " AND DEURRE.case_id = CA.id";
          $sql .= " ) AS deurwaarder,";
          break;
        
        // option group activity_type id = 2
        // option_value Ontruiming id = 620, value = 41, option_value status gepland value = 3
        /*case 'ontruiming':
          $sql .= " (CASE WHEN 3 = (SELECT ONT.status_id FROM civicrm_activity AS ONT";
          $sql .= " LEFT JOIN civicrm_case_activity AS ONTCAACT ON ONT.id = ONTCAACT.activity_id";
          $sql .= " WHERE ONT.activity_type_id = '41'";
          $sql .= " AND ONTCAACT.case_id = CA.id";
          $sql .= " ORDER BY ONT.activity_date_time DESC LIMIT 1";
          $sql .= " ) THEN 'J' ELSE 'N' END) AS ontruiming,";
          break;
        
        
        SELECT ONT.status_id, ONT.activity_date_time FROM civicrm_activity AS ONT
        LEFT JOIN civicrm_case_activity AS ONTCAACT ON ONT.id = ONTCAACT.activity_id
        WHERE ONT.activity_type_id = '41'
        AND ONTCAACT.case_id = '282'
        ORDER BY ONT.activity_date_time DESC
        
        
        // option_group activity_status = 25
        case 'ontruiming_status_id':
          $sql .= " (SELECT ONTSTOPVA.name FROM civicrm_activity AS ONTST";
          $sql .= " LEFT JOIN civicrm_case_activity AS ONTSTCAACT ON ONTST.id = ONTSTCAACT.activity_id";
          $sql .= " LEFT JOIN civicrm_option_value AS ONTSTOPVA ON ONTST.status_id = ONTSTOPVA.value";
          $sql .= " WHERE ONTST.activity_type_id = '41'";
          $sql .= " AND ONTSTCAACT.case_id = CA.id";
          $sql .= " AND ONTSTOPVA.option_group_id = '25'";
          $sql .= " ORDER BY ONTST.activity_date_time DESC LIMIT 1";
          $sql .= " ) AS ontruiming_status,";
          break;
        
SELECT ONTST.status_id, ONTSTOPVA.name, ONTST.activity_date_time FROM civicrm_activity AS ONTST
LEFT JOIN civicrm_case_activity AS ONTSTCAACT ON ONTST.id = ONTSTCAACT.activity_id
LEFT JOIN civicrm_option_value AS ONTSTOPVA ON ONTST.status_id = ONTSTOPVA.value
WHERE ONTST.activity_type_id = '41'
AND ONTSTCAACT.case_id = '282'
AND ONTSTOPVA.option_group_id = '25'
ORDER BY ONTST.activity_date_time DESC LIMIT 1
        
        case 'ontruiming_activity_date_time':
          $sql .= " (SELECT ONTDATE.activity_date_time FROM civicrm_activity AS ONTDATE";
          $sql .= " LEFT JOIN civicrm_case_activity AS ONTDATECAACT ON ONTDATE.id = ONTDATECAACT.activity_id";
          $sql .= " WHERE ONTDATE.activity_type_id = '41'";
          $sql .= " AND ONTDATECAACT.case_id = CA.id";
          $sql .= " ORDER BY ONTDATE.activity_date_time DESC LIMIT 1";
          $sql .= " ) AS ontruiming_activity_date_time,";
          break;
        
        // option_value Vonnis id = 619, value = 40  
        case 'vonnis':
          $sql .= " (CASE WHEN EXISTS (SELECT VON.id FROM civicrm_activity AS VON";
          $sql .= " LEFT JOIN civicrm_case_activity AS VONCAACT ON ONT.id = VONCAACT.activity_id";
          $sql .= " WHERE VON.activity_type_id = '40'";
          $sql .= " AND VONCAACT.case_id = CA.id";
          $sql .= " ORDER BY VON.activity_date_time DESC LIMIT 1";
          $sql .= " ) THEN 'J' ELSE 'N' END) AS vonnis,";
          break;
        
        case 'vonnis_activity_date_time':
          $sql .= " (SELECT VONDATE.activity_date_time FROM civicrm_activity AS VONDATE";
          $sql .= " LEFT JOIN civicrm_case_activity AS VONDATECAACT ON ONT.id = VONDATECAACT.activity_id";
          $sql .= " WHERE VONDATE.activity_type_id = '41'";
          $sql .= " AND VONDATECAACT.case_id = CA.id";
          $sql .= " ORDER BY VONDATE.activity_date_time DESC LIMIT 1";
          $sql .= " ) AS vonnis_activity_date_time,";
          break;*/
        
        // Hoofdhuurder civicrm_relationship_type id = 11
        
        // hoofdhuurder contact.id -> relationship.contact_id_b,
        // relationship.contact_id_b -> relationship.contact_id_a, 
        // relationship.contact_id_a -> case_contact.contact_id,
        // case_contact.contact_id -> case_contact.case_id
        case 'hoofdhuurder':
          $sql .= " (SELECT HOOFD.sort_name FROM civicrm_contact AS HOOFD";
          $sql .= " LEFT JOIN civicrm_relationship AS HOOFDRE ON HOOFD.id = HOOFDRE.contact_id_b";
          
          $sql .= " LEFT JOIN civicrm_case_contact AS HOOFDCACON ON HOOFDRE.contact_id_a = HOOFDCACON.case_id";
          
          $sql .= " WHERE HOOFDRE.relationship_type_id = '11'";
          $sql .= " AND HOOFDCACON.case_id = CA.id";
          $sql .= " ) AS hoofdhuurder,";
          break;
        
        /*case 'hoofdhuurder_street_address':
          $sql .= " (SELECT HOOFDSTRE.street_address FROM civicrm_address AS HOOFDSTRE";
          $sql .= " LEFT JOIN civicrm_relationship AS HOOFDSTRERE ON HOOFDSTRE.contact_id = HOOFDSTRERE.contact_id_b ";
          $sql .= " LEFT JOIN civicrm_relationship_type AS HOOFDSTRERETY ON HOOFDSTRERE.relationship_type_id = HOOFDSTRERETY.id ";
          $sql .= " WHERE HOOFDSTRERETY.name_a_b = 'Hoofdhuurder'";
          $sql .= " AND HOOFDSTRERE.case_id = CA.id";
          $sql .= " ) AS hoofdhuurder_street_address,";
          break;*/
        
        case 'CA.id':
          $sql .= " " . $field . ",";
          break;
        
        default:
          
      }
    }
    
    $sql = substr($sql, 0, -1);
    
    // from
    $sql .= " FROM civicrm_case as CA";
    
    // join
    /*if(isset($this->_submitValues['fields']['typeringen.typeringen'])){
      $sql .= " LEFT JOIN civicrm_value_typeringen_7"
    }*/
    
    /*if(isset($this->_submitValues['fields']['dossiermanager.sort_name'])){
      $sql .= " LEFT JOIN civicrm_contact AS dossiermanager ON "
    }
    
    if(isset($this->_submitValues['fields']['dossiermanager.sort_name'])){
      $sql .= " LEFT JOIN civicrm_contact AS dossiermanager ON "
    }*/
    
    echo('$sql: ' . $sql);
    exit();
    
    // get the acl clauses built before we assemble the query
    //$this->buildACLClause($this->_aliases['civicrm_contact']);
    //$sql = $this->buildQuery(TRUE);

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
}
