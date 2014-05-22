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
    $config = CRM_Mbreports_Config::singleton();
    
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
          'DOSS.dossiermanager' => 
          array('title' => ts('Dossiermanager')
          ),
          'DEUR.deurwaarder' => 
          array('title' => ts('Deurwaarder')
          ),
          // J / N (Ja of Nee) ontruimt, ontruim id is 41
          'ONT.ontruiming' =>
          array('title' => ts('Ontruiming')
          ),
          'ONT.status' =>
          array('title' => ts('Ontruiming status')
          ),
          'ONT.activity_date_time' => 
          array('title' => ts('Ontruiming datum')
          ),
          // J / N (Ja of Nee) vonnis, vonnis id = 40
          'VONN.vonnis' =>
          array('title' => ts('Vonnis')
          ),
          'VONN.activity_date_time' => 
          array('title' => ts('Vonnis datum')
          ),
        ),
        'filters' => array(
          'CA.id' => array(
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
            'default'      => 'this.month',
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
          'DOSS.dossiermanager_id' => array(
            'title' => ts('Dossiermanager'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $dossiermanagers,
          ),
          'DEUR.deurwaarder_id' => array(
            'title' => ts('Deurwaarder'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $deurwaarders,
          ),
          'ONT.ontruiming' => array(
            'title' => ts('Ontruiming'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => array('' => ts('- elke - '), 'J' => ts('Ja'), 'N' => ts('Nee')),
          ),
          'ONT.status_id' => array(
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
          'DOSS.dossiermanager' => 
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
          'HOOFD.hoofdhuurder' => array(
            'title' => ts('Hoofdhuurder naam'),
              'required' => TRUE,
          ),
          'HOOFDADD.street_address' => array(
            'title' => ts('Hoofdhuurder adres'),
              'required' => TRUE,
          ),
          'HOOFDEM.email' => array(
            'title' => ts('Hoofdhuurder e-mail'),
          ),
          'HOOFDPHO.phone' => array(
            'title' => ts('Hoofdhuurder telefoon'),
          ),
        ),
      ),
      
      // medehuurder
      'medehuurder' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'MEDE.medehuurder' => array(
            'title' => ts('Medehuurder naam'),
          ),
          'MEDEEM.email' => array(
            'title' => ts('Medehuurder e-mail'),
          ),
          'MEDEPHO.phone' => array(
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

    $config = CRM_Mbreports_Config::singleton();
    
    echo('<pre>');
    print_r($this->_submitValues);
    echo('</pre>');
    
    // select
    $sql = "SELECT";
        
    // fields
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){        
        case 'CA.id':
          $sql .= " " . $field . ",";
          break;
        
        case 'TYPE.typeringen':
          $sql .= " " . $field . ",";
          break;
        
        case 'DOSS.dossiermanager':
          $sql .= " " . $field . ",";
          break;
        
        case 'DEUR.deurwaarder':
          $sql .= " " . $field . ",";
          break;
        
        case 'ONT.ontruiming':
          $sql .= " " . $field . ",";
          $sql .= " ONT.status,";
          $sql .= " ONT.activity_date_time,";
          break;
        
        case 'VONN.vonnis':
          $sql .= " " . $field . ",";
          $sql .= " VONN.activity_date_time,";
          break;
        
        case 'PROP.vge_id':
          $sql .= " " . $field . ",";
          $sql .= " PROP.complex_id,";
          $sql .= " PROP.block,";
          $sql .= " PROP.city_region,";
          $sql .= " PROP.vge_type_id,";
          break;
        
        case 'HOOFD.hoofdhuurder':
          $sql .= " " . $field . ",";
          break;
        case 'HOOFDADD.street_address':
          $sql .= " " . $field . ",";
          break;
        case 'HOOFDEM.email':
          $sql .= " " . $field . ",";
          break;
        case 'HOOFDPHO.phone':
          $sql .= " " . $field . ",";
          break;
        
        case 'MEDE.medehuurder':
          $sql .= " " . $field . ",";
          break;
        case 'MEDEEM.email':
          $sql .= " " . $field . ",";
          break;
        case 'MEDEPHO.phone':
          $sql .= " " . $field . ",";
          break;
          
          break;
        

        default:
          
      }
    }
    
    $sql = substr($sql, 0, -1);
    
    // from
    $sql .= " FROM civicrm_case as CA" . PHP_EOL;
    
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){
        
        case 'TYPE.typeringen':
          $sql .= " LEFT JOIN ( SELECT TYPEPROTY.label as typeringen, TYPEOVD.entity_id FROM civicrm_value_ov_data AS TYPEOVD " . PHP_EOL;
          // civicrm_property_type
          $sql .= " LEFT JOIN civicrm_property_type AS TYPEPROTY ON TYPEPROTY.id = TYPEOVD.ov_type " . PHP_EOL;
          $sql .= " ) AS TYPE ON CA.id = TYPE.entity_id " . PHP_EOL;
          break;
          
        case 'PROP.vge_id':
          $sql .= " CASE WHEN HOOFD.hoofdhuurder_id IS NOT NULL THEN " . PHP_EOL;
          // civicrm_property
          $sql .= " LEFT JOIN ( SELECT vge_id AS vge_id, complex_id AS complex_id, block AS block, city_region AS city_region FROM civicrm_property AS PROPPROP " . PHP_EOL;
          // civicrm_value_huurovereenkomst_2
          $sql .= " LEFT JOIN civicrm_value_huurovereenkomst_2 AS PROPVAHUUR ON PROPVAHUUR.cge_nummer_first_6 = PROPPROP.vge_id " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS PROPREL ON PROPREL.contact_id_b = PROPVAHUUR.entity_id " . PHP_EOL;
          
          $sql .= " WHERE PROPREL.contact_id_a = HOOFD.hoofdhuurder_id " . PHP_EOL;
          
          $sql .= " ) AS PROP ON CA.id = PROP.case_id " . PHP_EOL;
          
          /*$sql .= " ELSE CASE WHEN MEDE.medehuurder_id IS NOT NULL THEN " . PHP_EOL;
          // civicrm_property
          $sql .= " LEFT JOIN ( SELECT vge_id AS vge_id, complex_id AS complex_id, block AS block, city_region AS city_region FROM civicrm_property AS PROPPROP " . PHP_EOL;
          // civicrm_value_huurovereenkomst_2
          $sql .= " LEFT JOIN civicrm_value_huurovereenkomst_2 AS PROPVAHUUR ON PROPVAHUUR.cge_nummer_first_6 = PROPPROP.vge_id " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS PROPREL ON PROPREL.contact_id_b = PROPVAHUUR.entity_id " . PHP_EOL;
          
          $sql .= " WHERE PROPREL.contact_id_a = HOOFD.hoofdhuurder_id " . PHP_EOL;
          
          $sql .= " ) AS PROP ON CA.id = PROP.case_id " . PHP_EOL;*/
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'DOSS.dossiermanager':
          $sql .= " LEFT JOIN ( SELECT DOSSCON.id AS dossiermanager_id, DOSSCON.sort_name AS dossiermanager, DOSSREL.case_id FROM civicrm_contact AS DOSSCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS DOSSREL ON DOSSREL.contact_id_b = DOSSCON.id " . PHP_EOL;
          $sql .= " WHERE DOSSREL.relationship_type_id = '42' " . PHP_EOL;
          $sql .= " ) AS DOSS ON CA.id = DOSS.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'DEUR.deurwaarder':
          $sql .= " LEFT JOIN ( SELECT DEURCON.id AS deurwaarder_id, DEURCON.sort_name AS deurwaarder, DEURREL.case_id FROM civicrm_contact AS DEURCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS DEURREL ON DEURREL.contact_id_b = DEURCON.id " . PHP_EOL;
          $sql .= " WHERE DEURREL.relationship_type_id = '15' " . PHP_EOL;
          $sql .= " ) AS DEUR ON CA.id = DEUR.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
                
        case 'ONT.ontruiming':
          $sql .= " LEFT JOIN ( SELECT ONTACT.status_id AS status_id, ONTACT.activity_date_time, ONTOPTVA.label AS status, ONTCAACT.case_id, " . PHP_EOL;
          
          // J or N 
          $sql .= " (CASE WHEN 3 = status_id THEN 'J' ELSE 'N' END) AS ontruiming " . PHP_EOL;

          $sql .= " FROM civicrm_activity AS ONTACT " . PHP_EOL;
          
          // civicrm_case_activity
          $sql .= " LEFT JOIN civicrm_case_activity AS ONTCAACT ON ONTCAACT.activity_id = ONTACT.id " . PHP_EOL;
          // option_value, status_id
          $sql .= " LEFT JOIN civicrm_option_value AS ONTOPTVA ON ONTOPTVA.value = ONTACT.status_id " . PHP_EOL;
          
          $sql .= " WHERE ONTACT.activity_type_id = '41' " . PHP_EOL;
          $sql .= " AND ONTOPTVA.option_group_id = '25' " . PHP_EOL;
          
          $sql .= " ORDER BY ONTACT.activity_date_time" . PHP_EOL;
          
          $sql .= " ) AS ONT ON CA.id = ONT.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'VONN.vonnis':
          $sql .= " LEFT JOIN ( SELECT status_id, activity_date_time, VONNCAACT.case_id, " . PHP_EOL;
          
          // J or N 
          $sql .= " (CASE WHEN (status_id IS NULL) THEN 'N' ELSE 'J' END) AS vonnis " . PHP_EOL;

          $sql .= " FROM civicrm_activity AS VONNACT " . PHP_EOL;
          
          // civicrm_case_activity
          $sql .= " LEFT JOIN civicrm_case_activity AS VONNCAACT ON VONNCAACT.activity_id = VONNACT.id " . PHP_EOL;

          $sql .= " WHERE VONNACT.activity_type_id = '40' " . PHP_EOL;
          
          $sql .= " ORDER BY VONNACT.activity_date_time" . PHP_EOL;
          
          $sql .= " ) AS VONN ON CA.id = VONN.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        /*case 'PROP.vge_id':
          $sql .= " LEFT JOIN ( SELECT ";
          break;*/
        
        case 'HOOFD.hoofdhuurder':
          $sql .= " LEFT JOIN ( SELECT HOOFDCON.id AS hoofdhuurder_id, HOOFDCON.sort_name AS hoofdhuurder, HOOFDCACON.case_id AS case_id FROM civicrm_contact AS HOOFDCON " . PHP_EOL;
          
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS HOODFREL ON HOODFREL.contact_id_a = HOOFDCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS HOOFDCONA ON HOOFDCONA.id = HOODFREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as HOOFDCACON ON HOOFDCACON.contact_id = HOOFDCONA.id " . PHP_EOL;
                    
          $sql .= " WHERE HOODFREL.relationship_type_id = '11' AND HOODFREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY HOODFREL.start_date DESC";
          
          $sql .= " ) AS HOOFD ON CA.id = HOOFD.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
                
        case 'HOOFDADD.street_address':
          $sql .= " LEFT JOIN ( SELECT HOOFDADDADD.street_address AS street_address, HOOFDADDCACON.case_id AS case_id FROM civicrm_contact AS HOOFDADDCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS HOODFREL ON HOODFREL.contact_id_a = HOOFDADDCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS HOOFDADDCONA ON HOOFDADDCONA.id = HOODFREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as HOOFDADDCACON ON HOOFDADDCACON.contact_id = HOOFDADDCONA.id " . PHP_EOL;
          
          // civicrm_address
          $sql .= " LEFT JOIN civicrm_address as HOOFDADDADD ON HOOFDADDADD.contact_id = HOOFDADDCON.id " . PHP_EOL;
          
          $sql .= " WHERE HOODFREL.relationship_type_id = '11' AND HOOFDADDADD.is_primary = '1' AND HOODFREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY HOODFREL.start_date DESC ";
          
          $sql .= " ) AS HOOFDADD ON CA.id = HOOFDADD.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'HOOFDEM.email':
          $sql .= " LEFT JOIN ( SELECT HOOFDEMEM.email, HOOFDEMCACON.case_id AS case_id FROM civicrm_contact AS HOOFDEMCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS HOODFREL ON HOODFREL.contact_id_a = HOOFDEMCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS HOOFDEMCONA ON HOOFDEMCONA.id = HOODFREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as HOOFDEMCACON ON HOOFDEMCACON.contact_id = HOOFDEMCONA.id " . PHP_EOL;
          
          // civicrm_email
          $sql .= " LEFT JOIN civicrm_email as HOOFDEMEM ON HOOFDEMEM.contact_id = HOOFDEMCON.id " . PHP_EOL;
          
          $sql .= " WHERE HOODFREL.relationship_type_id = '11' AND HOOFDEMEM.is_primary = '1' AND HOODFREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY HOODFREL.start_date DESC ";
          
          $sql .= " ) AS HOOFDEM ON CA.id = HOOFDEM.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'HOOFDPHO.phone':
          $sql .= " LEFT JOIN ( SELECT HOOFDPHOPHO.phone AS phone, HOOFDPHOCACON.case_id AS case_id FROM civicrm_contact AS HOOFDPHOCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS HOODFREL ON HOODFREL.contact_id_a = HOOFDPHOCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS HOOFDPHOCONA ON HOOFDPHOCONA.id = HOODFREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as HOOFDPHOCACON ON HOOFDPHOCACON.contact_id = HOOFDPHOCONA.id " . PHP_EOL;
                   
          // civicrm_phone
          $sql .= " LEFT JOIN civicrm_phone as HOOFDPHOPHO ON HOOFDPHOPHO.contact_id = HOOFDPHOCON.id " . PHP_EOL;
          
          $sql .= " WHERE HOODFREL.relationship_type_id = '11' AND HOOFDPHOPHO.is_primary = '1' AND HOODFREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY HOODFREL.start_date DESC ";
          
          $sql .= " ) AS HOOFDPHO ON CA.id = HOOFDPHO.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'MEDE.medehuurder':
          $sql .= " LEFT JOIN ( SELECT MEDECON.id AS medehuurder_id, MEDECON.sort_name AS medehuurder, MEDECACON.case_id AS case_id FROM civicrm_contact AS MEDECON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS MEDEREL ON MEDEREL.contact_id_a = MEDECON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS MEDECONA ON MEDECONA.id = MEDEREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as MEDECACON ON MEDECACON.contact_id = MEDECONA.id " . PHP_EOL;
                    
          $sql .= " WHERE MEDEREL.relationship_type_id = '13' AND MEDEREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY MEDEREL.start_date DESC ";
          
          $sql .= " ) AS MEDE ON CA.id = MEDE.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'MEDEEM.email':
          $sql .= " LEFT JOIN ( SELECT MEDEEMEM.email AS email, MEDEEMCACON.case_id AS case_id FROM civicrm_contact AS MEDEEMCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS MEDEEMREL ON MEDEEMREL.contact_id_a = MEDEEMCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS MEDEEMCONA ON MEDEEMCONA.id = MEDEEMREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as MEDEEMCACON ON MEDEEMCACON.contact_id = MEDEEMCONA.id " . PHP_EOL;
                    
          // civicrm_email
          $sql .= " LEFT JOIN civicrm_email as MEDEEMEM ON MEDEEMEM.contact_id = MEDEEMCON.id " . PHP_EOL;
          
          $sql .= " WHERE MEDEEMREL.relationship_type_id = '13' AND MEDEEMEM.is_primary = '1' AND MEDEEMREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY MEDEEMREL.start_date DESC ";
          
          $sql .= " ) AS MEDEEM ON CA.id = MEDEEM.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
        
        case 'MEDEPHO.phone':
          $sql .= " LEFT JOIN ( SELECT MEDEPHOPHO.phone AS phone, MEDEPHOCACON.case_id AS case_id FROM civicrm_contact AS MEDEPHOCON " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS MEDEPHOREL ON MEDEPHOREL.contact_id_a = MEDEPHOCON.id " . PHP_EOL;
          
          // civicrm_contact
          $sql .= " LEFT JOIN civicrm_contact AS MEDEPHOCONA ON MEDEPHOCONA.id = MEDEPHOREL.contact_id_b " . PHP_EOL;
          
          // civicrm_case_contact
          $sql .= " LEFT JOIN civicrm_case_contact as MEDEPHOCACON ON MEDEPHOCACON.contact_id = MEDEPHOCONA.id " . PHP_EOL;
          
          // civicrm_phone
          $sql .= " LEFT JOIN civicrm_phone as MEDEPHOPHO ON MEDEPHOPHO.contact_id = MEDEPHOCON.id " . PHP_EOL;
          
          $sql .= " WHERE MEDEPHOREL.relationship_type_id = '13' AND MEDEPHOPHO.is_primary = '1' AND MEDEPHOREL.is_active = '1' " . PHP_EOL;
          
          $sql .= " ORDER BY MEDEPHOREL.start_date DESC ";
          
          $sql .= " ) AS MEDEPHO ON CA.id = MEDEPHO.case_id " . PHP_EOL;
          $sql .= PHP_EOL . PHP_EOL;
          break;
      }
    }
    
    // where
    $where = '';
    foreach($this->_submitValues['fields'] as $field => $true){
      
      if('DOSS.dossiermanager' == $field){
        $field = 'DOSS.dossiermanager_id';
      }
      
      if('DEUR.deurwaarder' == $field){
        $field = 'DEUR.deurwaarder_id';
      }
      
      $filter_name = str_replace('.', '_', $field);
      
      //echo('$filter_name: ' . $filter_name .  '<br />') . PHP_EOL;
      
      if(array_key_exists($filter_name . '_value', $this->_submitValues) and !empty($this->_submitValues[$filter_name . '_value'])){
        //echo('$filter_value : ' . $this->_submitValues[$filter_name . '_value'] .  '<br />') . PHP_EOL;
        //echo('$filter_op : ' . $this->_submitValues[$filter_name . '_value'] .  '<br />') . PHP_EOL;
        
        switch ($this->_submitValues[$filter_name . '_op']){
          case 'eq':
            $where .= " " . $field . " = '" . $this->_submitValues[$filter_name . '_value'] . "' AND "; 
            break;
          
          case 'in':
            $where .= " ( ";
            foreach($this->_submitValues[$filter_name . '_value'] as $key => $value){
              $where .= " " . $field . " = '" . $value . "' OR "; 
            }
            $where = substr($where, 0, -3);
            $where .= " ) AND ";
            break;
          
          case 'notin':
            $where .= " ( ";
            foreach($this->_submitValues[$filter_name . '_value'] as $key => $value){
              $where .= " " . $field . " != '" . $value . "' AND "; 
            }
            $where = substr($where, 0, -3);
            $where .= " ) AND ";
            break;
        }
      }
    }
    
    $sql .= " WHERE CA.is_deleted = 0 ";
    
    if(!empty($where)){
      //echo('$where: ' . $where) . PHP_EOL;
      
      $sql .= " AND " . substr($where, 0, -4);
    }
    
    
    // group by
    $sql .= " GROUP BY CA.id " . PHP_EOL;
        
    // order by
    if('-' == $this->_submitValues['order_bys'][1]['column']){
      $sql .= " ORDER BY CA.id ASC ". PHP_EOL;
    }else {
      $sql .= " ORDER BY " . $this->_submitValues['order_bys'][1]['column'] . " " . $this->_submitValues['order_bys'][1]['order'] . " ". PHP_EOL;
    }
        
    echo($sql);
    //exit();
    
    // get the acl clauses built before we assemble the query
    //$this->buildACLClause($this->_aliases['civicrm_contact']);
    //$sql = $this->buildQuery(TRUE);

    $rows = array();
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
    
   
  }

  /*function alterDisplay(&$rows) {
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
  }*/
}
