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
  
  protected $fields = array();
  
  function __construct() {
    $config = CRM_Mbreports_Config::singleton();
    
    // case types
    $case_types[''] = ts('- elke - ');
    $case_types = array_merge($case_types, $config->caseTypes);
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
    
    $this->fields = array
    (
      'CA.id' => array(
        'title' => ts('Dossier ID'),
        'name' => 'id',
        'filter_name' => 'CA_id',
        'required' => TRUE,
        'filters' => array(),
        'order_bys' => array(
          'name' => 'id',
          'title' => ts('Dossier ID'),
          'alias' => 'id',
        ),
      ),
      'CA.subject' => array(
        'title' => ts('Dossier onderwerp'),
        'name' => 'subject',
        'filter_name' => 'CA_subject',
        'required' => TRUE,
        'filters' => array(),
        'order_bys' => array(),
      ),
      'CA.case_type_id' => array(
        'title' => ts('Dossier type'),
        'name' => 'case_type_id',
        'filter_name' => 'CA_case_type_id',
        'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier type'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $case_types,
        ),
        'order_bys' => array(),
      ),
      'CA.status_id' => array(
        'title' => ts('Dossier status'),
        'name' => 'status_id',
        'filter_name' => 'CA_case_type_id',
        'required' => TRUE,
        'filters' => array(
          'title' => ts('Dossier status'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $case_statuses,
        ),
        'order_bys' => array(
          'name' => 'status_id',
          'title' => ts('Dossier status'),
          'alias' => 'status_id',
        ),
      ),
      'CA.start_date' => array(
        'title' => ts('Dossier begindatum'),
        'name' => 'start_date',
        'filter_name' => 'CA_case_type_id',
        'filters' => array(
          'title' => ts('Dossier begindatum'),
          'default'      => 'this.month',
          'operatorType' => CRM_Report_Form::OP_DATE,
        ),
        'order_bys' => array(
          'name' => 'start_date',
          'title' => ts('Dossier begindatum'),
          'alias' => 'start_date',
        ),
      ),
      'TYPE.typeringen' => array(
        'title' => ts('Typeringen'),
        'name' => 'typeringen',
        'filter_name' => 'CA_case_type_id',
        'filters' => array(),
        'order_bys' => array(
          'name' => 'typeringen',
          'title' => ts('Typeringen'),
          'alias' => 'typeringen',
        ),
      ),
      'DOSS.dossiermanager' =>  array(
        'title' => ts('Dossiermanager'),
        'name' => 'dossiermanager',
        'filter_name' => 'DOSS_dossiermanager',
        'filters' => array(
          'title' => ts('Dossiermanager'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $dossiermanagers,
        ),
        'order_bys' => array(
          'name' => 'dossiermanager',
          'title' => ts('Dossiermanager'),
          'alias' => 'dossiermanager',
        ),
      ),
      'DEUR.deurwaarder' => array(
        'title' => ts('Deurwaarder'),
        'name' => 'deurwaarder',
        'filter_name' => 'DEUR_deurwaarder',
        'filters' => array(
          'title' => ts('Deurwaarder'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $deurwaarders,
        ),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) ontruimt, ontruim id is 41
      'ONT.ontruiming' => array(
        'title' => ts('Ontruiming'),
        'name' => 'ontruiming',
        'filter_name' => 'ONT_ontruiming',
        'filters' => array(
          'title' => ts('Ontruiming'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => array('' => ts('- elke - '), 'J' => ts('Ja'), 'N' => ts('Nee')),
        ),
        'order_bys' => array(),
      ),
      'ONT.status' => array(
        'title' => ts('Ontruiming status'),
        'name' => 'ontruiming_status',
        'filter_name' => 'ONT_status',
        'filters' => array(
          'title' => ts('Ontruiming status '),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $activity_statuss,
        ),
        'order_bys' => array(),
      ),
      'ONT.activity_date_time' => array(
        'title' => ts('Ontruiming datum'),
        'name' => 'ontruiming_status',
        'filter_name' => 'ONT_activity_date_time',
        'filters' => array(),
        'order_bys' => array(),
      ),
      // J / N (Ja of Nee) vonnis, vonnis id = 40
      'VONN.vonnis' => array(
        'title' => ts('Vonnis'),
        'name' => 'vonnis',
        'filter_name' => 'VONN_vonnis',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'VONN.activity_date_time' => array(
        'title' => ts('Vonnis datum'),
        'name' => 'vonnis_activity_date_time',
        'filter_name' => 'VONN_activity_date_time',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'PROP.vge_id' => array(
        'title' => ts('VGE nummer'),
        'name' => 'PROP_vge_id',
        'filter_name' => 'PROP_vge_id',
        'operatorType' => CRM_Report_Form::OP_SELECT,
        'options' => $complex_ids,
        'filter_name' => 'PROP_vge_id',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'PROP.complex_id' => array(
        'title' => ts('Complex'),
        'name' => 'complex_id',
        'filter_name' => 'PROP_complex_id',
        'filters' => array(
          'title' => ts('Complex'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $complex_ids,
        ),
        'order_bys' => array(
          'name' => 'complex_id',
          'title' => ts('Complex'),
          'alias' => 'complex_id',
        ),
      ),
      'PROP.block' => array(
        'title' => ts('Wijk'),
        'name' => 'block',
        'filter_name' => 'PROP_block',
        'filters' => array(
          'title' => ts('Wijk'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $blocks,
        ),
        'order_bys' => array(
          'name' => 'block',
          'title' => ts('Wijk'),
          'alias' => 'block',
        ),
      ),
      'PROP.city_region' => array(
        'title' => ts('Buurt'),
        'name' => 'city_region',
        'filter_name' => 'PROP_city_region',
        'filters' => array(
          'title' => ts('Buurt'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $city_regions,
        ),
        'order_bys' => array(
          'name' => 'city_region',
          'title' => ts('Buurt'),
          'alias' => 'city_region',
        ),
      ),
      'PROP.vge_type_id' => array(
        'title' => ts('VGE type'),
        'name' => 'vge_type_id',
        'filter_name' => 'PROP_vge_type_id',
        'filters' => array(
          'title' => ts('VGE type'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'options' => $vge_type_ids,
        ),
        'order_bys' => array(
          'name' => 'vge_type_id',
          'title' => ts('VGE type'),
          'alias' => 'vge_type_id',
        ),
      ),
      'HOOFD.hoofdhuurder' => array(
        'title' => ts('Hoofdhuurder naam'),
        'name' => 'hoofdhuurder',
        'required' => TRUE,
        'filter_name' => 'HOOFD_hoofdhuurder',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'HOOFDADD.hoofdhuurder_street_address' => array(
        'title' => ts('Hoofdhuurder adres'),
        'name' => 'hoofdhuurder_street_address',
        'required' => TRUE,
        'filter_name' => 'HOOFD_hoofdhuurder_street_address',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'HOOFDEM.hoofdhuurder_email' => array(
        'title' => ts('Hoofdhuurder e-mail'),
        'name' => 'hoofdhuurder_email',
        'filter_name' => 'HOOFD_hoofdhuurder_email',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'HOOFDPHO.hoofdhuurder_phone' => array(
        'title' => ts('Hoofdhuurder telefoon'),
        'name' => 'hoofdhuurder_phone',
        'filter_name' => 'HOOFD_hoofdhuurder_phone',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'MEDE.medehuurder' => array(
        'title' => ts('Medehuurder naam'),
        'name' => 'medehuurder',
        'filter_name' => 'MEDE_medehuurder',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'MEDEEM.medehuurder_email' => array(
        'title' => ts('Medehuurder e-mail'),
        'name' => 'medehuurder_email',
        'filter_name' => 'MEDE_medehuurder_email',
        'filters' => array(),
        'order_bys' => array(),
      ),
      'MEDEPHO.medehuurder_phone' => array(
        'title' => ts('Medehuurder telefoon'),
        'name' => 'medehuurder_phone',
        'filter_name' => 'MEDE_medehuurder_phone',
        'filters' => array(),
        'order_bys' => array(),
      ),
    );
        
    $this->_columns = array(
      'civicrm_case' =>
      array(
        'dao' => 'CRM_Case_DAO_Case',
        'fields' => array(
          'CA.id' => array(
            'title' => ts('Dossier ID'),
            'name' => 'id',
            'required' => TRUE,
          ),
          'CA.subject' => array(
            'title' => ts('Dossier onderwerp'),
            'name' => 'subject',
            'required' => TRUE,
          ),
          'CA.case_type_id' => array(
            'title' => ts('Dossier type'),
            'name' => 'case_type_id',
            'required' => TRUE,
          ),
          'CA.status_id' => array(
            'title' => ts('Dossier status'),
            'name' => 'status_id',
            'required' => TRUE,
          ),
          'CA.start_date' => array(
            'title' => ts('Dossier begindatum'),
            'name' => 'start_date',
            //'required' => TRUE,
          ),
          'TYPE.typeringen' => array(
            'title' => ts('Typeringen'),
            'name' => 'typeringen',
          ),
          'DOSS.dossiermanager' =>  array(
            'title' => ts('Dossiermanager'),
            'name' => 'dossiermanager',
          ),
          'DEUR.deurwaarder' => array(
            'title' => ts('Deurwaarder'),
            'name' => 'deurwaarder',
          ),
          // J / N (Ja of Nee) ontruimt, ontruim id is 41
          'ONT.ontruiming' => array(
            'title' => ts('Ontruiming'),
            'name' => 'ontruiming',
          ),
          /*'ONT.status' =>
          array('title' => ts('Ontruiming status')
          ),
          'ONT.activity_date_time' => 
          array('title' => ts('Ontruiming datum')
          ),*/
          // J / N (Ja of Nee) vonnis, vonnis id = 40
          'VONN.vonnis' =>
          array(
            'title' => ts('Vonnis'),
            'name' => 'vonnis',
          ),
          /*'VONN.activity_date_time' => 
          array('title' => ts('Vonnis datum')
          ),*/
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
          'ONT.ontruiming_status_id' => array(
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
          'TYPE.typeringen' => 
          array(
            'name' => 'typeringen',
            'title' => ts('Typeringen'),
            'alias' => 'typeringen',
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
            'name' => 'vge_id',
          ),
          'PROP.complex_id' => array(
            'title' => ts('Complex'),
            'name' => 'complex_id',
          ),
          'PROP.block' => array(
            'title' => ts('Wijk'),
            'name' => 'block',
          ),
          'PROP.city_region' => array(
            'title' => ts('Buurt'),
            'name' => 'city_region',
          ),
          'PROP.vge_type_id' => array(
            'title' => ts('VGE type'),
            'name' => 'vge_type_id',
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
            'name' => 'hoofdhuurder',
            'required' => TRUE,
          ),
          'HOOFDADD.hoofdhuurder_street_address' => array(
            'title' => ts('Hoofdhuurder adres'),
            'name' => 'hoofdhuurder_street_address',
            'required' => TRUE,
          ),
          'HOOFDEM.hoofdhuurder_email' => array(
            'title' => ts('Hoofdhuurder e-mail'),
            'name' => 'hoofdhuurder_email',
          ),
          'HOOFDPHO.hoofdhuurder_phone' => array(
            'title' => ts('Hoofdhuurder telefoon'),
            'name' => 'hoofdhuurder_phone',
          ),
        ),
      ),
      
      // medehuurder
      'medehuurder' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'MEDE.medehuurder' => array(
            'title' => ts('Medehuurder naam'),
            'name' => 'medehuurder',
          ),
          'MEDEEM.medehuurder_email' => array(
            'title' => ts('Medehuurder e-mail'),
            'name' => 'medehuurder_email',
          ),
          'MEDEPHO.medehuurder_phone' => array(
            'title' => ts('Medehuurder telefoon'),
            'name' => 'medehuurder_phone',
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
    
    set_time_limit(0);

    $mbreporst_config = CRM_Mbreports_Config::singleton();
    
    $this->beginPostProcess();
    
    // select
    $sql = "SELECT";
    
    $sql .= " CACONT.contact_id AS contact_id, ";
        
    // fields
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){                
        case 'ONT.ontruiming':
          $sql .= " " . $field . ",";
          $sql .= " ONT.ontruiming_status,";
          $sql .= " ONT.ontruiming_activity_date_time,";
          break;
        
        case 'VONN.vonnis':
          $sql .= " " . $field . ",";
          $sql .= " VONN.vonnis_activity_date_time,";
          break;
        
        case 'HOOFD.hoofdhuurder':
        case 'HOOFDADD.hoofdhuurder_street_address':
        case 'HOOFDEM.hoofdhuurder_email':
        case 'HOOFDPHO.hoofdhuurder_phone':
        case 'MEDE.medehuurder':
        case 'MEDEEM.medehuurder_email':
        case 'MEDEPHO.medehuurder_phone':
          
        case 'PROP.vge_id':
        case 'PROP.complex_id':
        case 'PROP.block':
        case 'PROP.city_region':
        case 'PROP.vge_type_id':
          $sql .= "";
          break;
        
        default:
          $sql .= " " . $field . ",";
      }
    }
    
    $sql = substr($sql, 0, -1);
    
    // from
    $sql .= " FROM civicrm_case as CA" . PHP_EOL;
    
    // case_contact
    $sql .= " LEFT JOIN civicrm_case_contact AS CACONT ON CACONT.case_id = CA.id ";
        
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){
        
        case 'TYPE.typeringen':
          $sql .= " LEFT JOIN ( SELECT TYPEPROTY.label as typeringen, TYPEOVD.entity_id FROM civicrm_value_ov_data AS TYPEOVD " . PHP_EOL;
          // civicrm_property_type
          $sql .= " LEFT JOIN civicrm_property_type AS TYPEPROTY ON TYPEPROTY.id = TYPEOVD.ov_type " . PHP_EOL;
          $sql .= " ) AS TYPE ON CA.id = TYPE.entity_id " . PHP_EOL;
          break;
          
        case 'PROP.vge_id':
          /*$sql .= " CASE WHEN HOOFD.hoofdhuurder_id IS NOT NULL THEN " . PHP_EOL;
          // civicrm_property
          $sql .= " LEFT JOIN ( SELECT vge_id AS vge_id, complex_id AS complex_id, block AS block, city_region AS city_region FROM civicrm_property AS PROPPROP " . PHP_EOL;
          // civicrm_value_huurovereenkomst_2
          $sql .= " LEFT JOIN civicrm_value_huurovereenkomst_2 AS PROPVAHUUR ON PROPVAHUUR.cge_nummer_first_6 = PROPPROP.vge_id " . PHP_EOL;
          // civicrm_relationship
          $sql .= " LEFT JOIN civicrm_relationship AS PROPREL ON PROPREL.contact_id_b = PROPVAHUUR.entity_id " . PHP_EOL;
          
          $sql .= " WHERE PROPREL.contact_id_a = HOOFD.hoofdhuurder_id " . PHP_EOL;
          
          $sql .= " ) AS PROP ON CA.id = PROP.case_id " . PHP_EOL;*/
          
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
          $sql .= " LEFT JOIN ( SELECT ONTACT.status_id AS ontruiming_status_id, ONTACT.activity_date_time AS ontruiming_activity_date_time, ONTOPTVA.label AS ontruiming_status, ONTCAACT.case_id, " . PHP_EOL;
          
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
          $sql .= " LEFT JOIN ( SELECT VONNACT.status_id AS vonnis_status_id, VONNACT.activity_date_time AS vonnis_activity_date_time, VONNCAACT.case_id, " . PHP_EOL;
          
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
        
        /*case 'HOOFD.hoofdhuurder':
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
                
        case 'HOOFDADD.hoofdhuurder_street_address':
          $sql .= " LEFT JOIN ( SELECT HOOFDADDADD.street_address AS hoofdhuurder_street_address, HOOFDADDCACON.case_id AS case_id FROM civicrm_contact AS HOOFDADDCON " . PHP_EOL;
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
        
        case 'HOOFDEM.hoofdhuurder_email':
          $sql .= " LEFT JOIN ( SELECT HOOFDEMEM.email AS hoofdhuurder_email, HOOFDEMCACON.case_id AS case_id FROM civicrm_contact AS HOOFDEMCON " . PHP_EOL;
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
        
        case 'HOOFDPHO.hoofdhuurder_phone':
          $sql .= " LEFT JOIN ( SELECT HOOFDPHOPHO.phone AS hoofdhuurder_phone, HOOFDPHOCACON.case_id AS case_id FROM civicrm_contact AS HOOFDPHOCON " . PHP_EOL;
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
        
        case 'MEDEEM.medehuurder_email':
          $sql .= " LEFT JOIN ( SELECT MEDEEMEM.email AS medehuurder_email, MEDEEMCACON.case_id AS case_id FROM civicrm_contact AS MEDEEMCON " . PHP_EOL;
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
        
        case 'MEDEPHO.medehuurder_phone':
          $sql .= " LEFT JOIN ( SELECT MEDEPHOPHO.phone AS medehuurder_phone, MEDEPHOCACON.case_id AS case_id FROM civicrm_contact AS MEDEPHOCON " . PHP_EOL;
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
          break;*/
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
      
      if(array_key_exists($filter_name . '_value', $this->_submitValues) and !empty($this->_submitValues[$filter_name . '_value'])){
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
        
    // columns headers
    foreach($this->_submitValues['fields'] as $field => $true){
      switch($field){                
        case 'ONT.ontruiming':
          $this->_columnHeaders[$this->fields[$field]['name']] = array('title' => $this->fields[$field]['title']);
          $this->_columnHeaders[$this->fields['ONT.status']['name']] = array('title' => $this->fields[$field]['title']);
          $this->_columnHeaders[$this->fields['ONT.activity_date_time']['name']] = array('title' => $this->fields[$field]['title']);
          break;

        case 'VONN.vonnis':
          $this->_columnHeaders[$this->fields[$field]['name']] = array('title' => $this->fields[$field]['title']);
          $this->_columnHeaders[$this->fields['VONN.activity_date_time']['name']] = array('title' => $this->fields[$field]['title']);
          break;

        default:
          $this->_columnHeaders[$this->fields[$field]['name']] = array('title' => $this->fields[$field]['title']);
      }
    }
      
    $rows = array();
    $dao = CRM_Core_DAO::executeQuery($sql);
    
    unset($sql);
    
    while ($dao->fetch()) {
            
      // hoofdhuurder
      if(isset($this->_submitValues['fields']['HOOFD.hoofdhuurder']) or isset($this->_submitValues['fields']['HOOFDADD.hoofdhuurder_street_address']) or isset($this->_submitValues['fields']['HOOFDEM.hoofdhuurder_email']) or isset($this->_submitValues['fields']['HOOFDPHO.hoofdhuurder_phone'])){        
        $hoofdHuurders = CRM_Utils_DgwUtils::getHoofdhuurders($dao->contact_id, true);
        
        if(isset($hoofdHuurders[0]['contact_id'])){
          $sql = "SELECT civicrm_contact.sort_name AS " . $this->fields['HOOFD.hoofdhuurder']['name'] . ", civicrm_address.street_address AS " . $this->fields['HOOFDADD.hoofdhuurder_street_address']['name'] . ", civicrm_email.email AS " . $this->fields['HOOFDEM.hoofdhuurder_email']['name'] . ", civicrm_phone.phone AS " . $this->fields['HOOFDPHO.hoofdhuurder_phone']['name'] . " ";
          $sql .= "FROM civicrm_contact ";
          $sql .= "LEFT JOIN civicrm_address ON civicrm_address.contact_id = civicrm_contact.id ";
          $sql .= "LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id ";
          $sql .= "LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id ";
          $sql .= "WHERE civicrm_contact.id = '" . $hoofdHuurders[0]['contact_id'] . "' ";
          $sql .= "LIMIT 1 ";
          
          //echo('$sql hoofd: ' . $sql);
        
          $dao_hoofdhuurder = CRM_Core_DAO::executeQuery($sql);
          unset($sql);
          while ($dao_hoofdhuurder->fetch()) {
            $dao->hoofdhuurder = $dao_hoofdhuurder->{$this->fields['HOOFD.hoofdhuurder']['name']};
            $dao->hoofdhuurder_street_address = $dao_hoofdhuurder->{$this->fields['HOOFDADD.hoofdhuurder_street_address']['name']};
            $dao->hoofdhuurder_email = $dao_hoofdhuurder->{$this->fields['HOOFDEM.hoofdhuurder_email']['name']};
            $dao->hoofdhuurder_phone = $dao_hoofdhuurder->{$this->fields['HOOFDPHO.hoofdhuurder_phone']['name']};
          }
          
          unset($dao_hoofdhuurder);
        }else {
          $dao->hoofdhuurder = NULL;
          $dao->hoofdhuurder_street_address = NULL;
          $dao->hoofdhuurder_email = NULL;
          $dao->hoofdhuurder_phone = NULL;
        }
        
        unset($hoofdHuurders);
      }
      
      // medehuurder
      if(isset($this->_submitValues['fields']['MEDE.medehuurder']) or isset($this->_submitValues['fields']['MEDEEM.medehuurder_email']) or isset($this->_submitValues['fields']['MEDEPHO.medehuurder_phone'])){
        $medeHuurders = CRM_Utils_DgwUtils::getMedeHuurders($dao->contact_id, true);
        
        if(isset($medeHuurders[0]['contact_id'])){
          $sql = "SELECT civicrm_contact.sort_name AS " . $this->fields['MEDE.medehuurder']['name'] . ", civicrm_email.email AS " . $this->fields['MEDEEM.medehuurder_email']['name'] . ", civicrm_phone.phone AS " . $this->fields['MEDEPHO.medehuurder_phone']['name'] . " ";
          $sql .= "FROM civicrm_contact ";
          $sql .= "LEFT JOIN civicrm_address ON civicrm_email.contact_id = civicrm_contact.id ";
          $sql .= "LEFT JOIN civicrm_phone ON civicrm_phone.contact_id = civicrm_contact.id ";
          $sql .= "WHERE civicrm_contact.id = '" . $medehuurder_id . "' ";
          $sql .= "LIMIT 1 ";

          $dao_medehuurder = CRM_Core_DAO::executeQuery($sql);
          unset($sql);
          while ($dao_medehuurder->fetch()) {
            $dao->medehuurder = $dao_medehuurder->{$this->fields['MEDE.medehuurder']['name']};
            $dao->medehuurder_email = $dao_medehuurder->{$this->fields['MEDEEM.medehuurder_email']['name']};
            $dao->medehuurder_phone = $dao_medehuurder->{$this->fields['MEDEPHO.medehuurder_phone']['name']};
          }
          
          unset($dao_medehuurder);
        }else {
          $dao->medehuurder = NULL;
          $dao->medehuurder_email = NULL;
          $dao->medehuurder_phone = NULL;
        }
        unset($medeHuurders);
      }
      
      // prop
      if(isset($this->_submitValues['fields']['PROP.vge_id']) or isset($this->_submitValues['fields']['PROP.complex_id']) or isset($this->_submitValues['fields']['PROP.block']) or isset($this->_submitValues['fields']['PROP.city_region']) or isset($this->_submitValues['fields']['PROP.vge_type_id'])){
        $vgeData = CRM_Utils_MbreportsUtils::getCaseVgeData($dao->id);
        
        if(!empty($vgeData)){
          $sql = "SELECT vge_id AS " . $this->fields['PROP.vge_id']['name'] . ", complex_id AS " . $this->fields['PROP.complex_id']['name'] . ", block AS " . $this->fields['PROP.block']['name'] . ", city_region AS " . $this->fields['PROP.city_region']['name'] . ", vge_type_id AS " . $this->fields['PROP.vge_type_id']['name'] . " ";
          $sql .= "FROM civicrm_property ";
          $sql .= "WHERE civicrm_property.vge_id = '" . $vgeData['id'] . "' ";
          $sql .= "LIMIT 1 ";
          
          $dao_vge = CRM_Core_DAO::executeQuery($sql);
          unset($sql);
          while ($dao_vge->fetch()) {
            $dao->vge_id = $dao_medehuurder->{$this->fields['PROP.vge_id']['name']};
            $dao->complex_id = $dao_medehuurder->{$this->fields['PROP.complex_id']['name']};
            $dao->block = $dao_medehuurder->{$this->fields['PROP.block']['name']};
            $dao->city_region = $dao_medehuurder->{$this->fields['PROP.city_region']['name']};
            $dao->vge_type_id = $dao_medehuurder->{$this->fields['PROP.vge_type_id']['name']};
          }
          unset($dao_vge);
        }else {
          $dao->vge_id = NULL;
          $dao->complex_id = NULL;
          $dao->block = NULL;
          $dao->city_region = NULL;
          $dao->vge_type_id = NULL;
        }
        unset($vgeData);
      }
      
      foreach($this->_columnHeaders as $key => $title){
        $row[$key] = $dao->$key;
      }
      
      $rows[] = $row;
      
      unset($row);
    }

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }
}
