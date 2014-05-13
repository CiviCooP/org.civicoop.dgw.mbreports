<?php

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
    $query .= "LEFT JOIN civicrm_relationship ON civicrm_contact.id = civicrm_relationship.contact_id_a ";
    $query .= "WHERE civicrm_relationship.case_id != 'NULL' ";
    $query .= "AND civicrm_relationship.relationship_type_id = '" . $result['id'] . "' ";
    $query .= "GROUP BY civicrm_contact.id ORDER BY civicrm_contact.sort_name ASC";
    $dao = CRM_Core_DAO::executeQuery($query); 
    
    $dossiermanagers = array();
    while($dao->fetch()){
      $dossiermanagers[$dao->id] = $dao->sort_name;
    }
    
    // relationship_contact_id_a_deurwaarder
    // name_a_b = Deurwaarder
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'name_a_b' => 'Deurwaarder',
    );
    $result = civicrm_api('RelationshipType', 'getsingle', $params);
    
    $query = "SELECT civicrm_contact.id, civicrm_contact.sort_name FROM civicrm_contact ";
    $query .= "LEFT JOIN civicrm_relationship ON civicrm_contact.id = civicrm_relationship.contact_id_a ";
    $query .= "WHERE civicrm_relationship.case_id != 'NULL' ";
    $query .= "AND civicrm_relationship.relationship_type_id = '" . $result['id'] . "' ";
    $query .= "GROUP BY civicrm_contact.id ORDER BY civicrm_contact.sort_name ASC";
    $dao = CRM_Core_DAO::executeQuery($query); 
    
    $deurwaarders = array();
    while($dao->fetch()){
      $deurwaarders[$dao->id] = $dao->sort_name;
    }
    
    // complex
    $query = "SELECT complex_id FROM civicrm_property GROUP BY complex_id ORDER BY complex_id ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $complex_ids = array();
    $complex_ids[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->complex_id)){
        $complex_ids[$dao->complex_id] = $dao->complex_id;
      }
    }
    
    // city_region
    $query = "SELECT city_region FROM civicrm_property GROUP BY city_region ORDER BY city_region ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $city_regions = array();
    $city_regions[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->city_region)){
        $city_regions[$dao->city_region] = $dao->city_region;
      }
    }
    
    // block
    $query = "SELECT block FROM civicrm_property GROUP BY block ORDER BY block ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $blocks = array();
    $blocks[''] = ts('- elke - ');
    while($dao->fetch()){
      if(!empty($dao->block)){
        $blocks[$dao->block] = $dao->block;
      }
    }
    
    // vge_type_id
    $query = "SELECT id, label FROM civicrm_property_type ORDER BY label ASC";
    $dao = CRM_Core_DAO::executeQuery($query);  

    $vge_type_ids = array();
    $vge_type_ids[''] = ts('- elke - ');
    while($dao->fetch()){
      $vge_type_ids[$dao->id] = $dao->label;
    }
    
    // activity_ontruiming_status_id
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
          'id' =>
          array('title' => ts('Dossier ID'),
            'required' => TRUE,
          ),
          'subject' =>
          array('title' => ts('Dossier onderwerp'),
            'required' => TRUE,
          ),
          'case_type_id' =>
          array('title' => ts('Dossier type'),
            'required' => TRUE,
          ),
          'status_id' =>
          array('title' => ts('Dossier status'),
            'required' => TRUE,
          ),
          'start_date' =>
          array('title' => ts('Dossier begindatum'),
            //'required' => TRUE,
          ),
          'typeringen' =>
          array('title' => ts('Typeringen')
          ),
          'relationship_contact_id_a_dossiermanager' => 
          array('title' => ts('Dossiermanager')
          ),
          'relationship_contact_id_a_deurwaarder' => 
          array('title' => ts('Deurwaarder')
          ),
          // J / N (Ja of Nee) ontruimt, ontruim id is 41
          'activity_ontruiming_41' =>
          array('title' => ts('Ontruiming')
          ),
          'activity_ontruiming_status_id' =>
          array('title' => ts('Ontruiming status')
          ),
          'activity_ontruiming_date_time' => 
          array('title' => ts('Ontruiming datum')
          ),
          // J / N (Ja of Nee) vonnis, vonnis id = 40
          'activity_vonnis_40' =>
          array('title' => ts('Vonnis')
          ),
          'activity_vonnis_date_time' => 
          array('title' => ts('Vonnis datum')
          ),
        ),
        'filters' => array(
          'case_type_id' => array(
            'title' => ts('Dossier type'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $case_types,
          ),
          'status_id' => array(
            'title' => ts('Dossier status'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $case_statuses,
          ),
          'start_date' => array(
            'title' => ts('Dossier begindatum'),
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
          'relationship_contact_id_a_dossiermanager' => array(
            'title' => ts('Dossiermanager'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $dossiermanagers,
          ),
          'relationship_contact_id_a_deurwaarder' => array(
            'title' => ts('Deurwaarder'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $deurwaarders,
          ),
          'activity_ontruiming_41' => array(
            'title' => ts('Ontruiming'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => array('' => ts('- elke - '), 'J' => ts('Ja'), 'N' => ts('Nee')),
          ),
          'activity_ontruiming_status_id' => array(
            'title' => ts('Ontruiming status '),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $activity_statuss,
          ),
        ),
        'order_bys' =>
        array(
          'id' =>
          array(
            'name' => 'id',
            'title' => ts('Dossier ID'),
            'alias' => 'id',
          ),
          'typeringen' => 
          array(
            'name' => 'typeringen',
            'title' => ts('Typeringen'),
            'alias' => 'typeringen',
          ),
          'status_id' => 
          array(
            'name' => 'status_id',
            'title' => ts('Dossier status'),
            'alias' => 'status_id',
          ),
          'start_date' => 
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
          'vge_id' => array(
            'title' => ts('VGE nummer'),
          ),
          'complex_id' => array(
            'title' => ts('Complex'),
          ),
          'block' => array(
            'title' => ts('Wijk'),
          ),
          'city_region' => array(
            'title' => ts('Buurt'),
          ),
          'vge_type_id' => array(
            'title' => ts('VGE type'),
          ),
        ),
        'filters' => array(
          'complex_id' => array(
            'title' => ts('Complex'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $complex_ids,
          ),
          'block' => array(
            'title' => ts('Wijk'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $blocks,
          ),
          'city_region' => array(
            'title' => ts('Buurt'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $city_regions,
          ),
          'vge_type_id' => array(
            'title' => ts('VGE type'),
            'operatorType' => CRM_Report_Form::OP_SELECT,
            'options' => $vge_type_ids,
          ),
        ),
        'order_bys' =>
        array(
          'complex_id' =>
          array(
            'name' => 'complex_id',
            'title' => ts('Complex'),
            'alias' => 'complex_id',
          ),
          'block' => 
          array(
            'name' => 'block',
            'title' => ts('Wijk'),
            'alias' => 'block',
          ),
          'city_region' => 
          array(
            'name' => 'city_region',
            'title' => ts('Buurt'),
            'alias' => 'city_region',
          ),
          'vge_type_id' => 
          array(
            'name' => 'vge_type_id',
            'title' => ts('VGE type'),
            'alias' => 'vge_type_id',
          ),
        ),
      ),
      
      // hoofdhuurder
      '11_b_a' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          '11_b_a_sort_name' => array(
            'title' => ts('Hoofdhuurder naam'),
              'required' => TRUE,
          ),
          '11_b_a_street_address' => array(
            'title' => ts('Hoofdhuurder adres'),
              'required' => TRUE,
          ),
          '11_b_a_email' => array(
            'title' => ts('Hoofdhuurder e-mail'),
          ),
          '11_b_a_phone' => array(
            'title' => ts('Hoofdhuurder telefoon'),
          ),
        ),
      ),
            
      // medehuurder
      '13_b_a' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          '13_b_a_sort_name' => array(
            'title' => ts('Medehuurder naam'),
          ),
          '13_b_a_email' => array(
            'title' => ts('Medehuurder e-mail'),
          ),
          '13_b_a_phone' => array(
            'title' => ts('Medehuurder telefoon'),
          ),
        ),
      ),
    );
    
    parent::__construct();
  }

  function preProcess() {
    $this->assign('reportTitle', ts('Membership Detail Report'));
    parent::preProcess();
  }

  function select() {
    $select = $this->_columnHeaders = array();

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            if ($tableName == 'civicrm_address') {
              $this->_addressField = TRUE;
            }
            elseif ($tableName == 'civicrm_email') {
              $this->_emailField = TRUE;
            }
            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  function from() {
    $this->_from = NULL;

    $this->_from = "
         FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}
               INNER JOIN civicrm_membership {$this->_aliases['civicrm_membership']}
                          ON {$this->_aliases['civicrm_contact']}.id =
                             {$this->_aliases['civicrm_membership']}.contact_id AND {$this->_aliases['civicrm_membership']}.is_test = 0
               LEFT  JOIN civicrm_membership_status {$this->_aliases['civicrm_membership_status']}
                          ON {$this->_aliases['civicrm_membership_status']}.id =
                             {$this->_aliases['civicrm_membership']}.status_id ";


    //used when address field is selected
    if ($this->_addressField) {
      $this->_from .= "
             LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
                       ON {$this->_aliases['civicrm_contact']}.id =
                          {$this->_aliases['civicrm_address']}.contact_id AND
                          {$this->_aliases['civicrm_address']}.is_primary = 1\n";
    }
    //used when email field is selected
    if ($this->_emailField) {
      $this->_from .= "
              LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']}
                        ON {$this->_aliases['civicrm_contact']}.id =
                           {$this->_aliases['civicrm_email']}.contact_id AND
                           {$this->_aliases['civicrm_email']}.is_primary = 1\n";
    }
  }

  function where() {
    $clauses = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if (CRM_Utils_Array::value('operatorType', $field) & CRM_Utils_Type::T_DATE) {
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
            if ($op) {
              $clause = $this->whereClause($field,
                $op,
                CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
              );
            }
          }

          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }

    if (empty($clauses)) {
      $this->_where = "WHERE ( 1 ) ";
    }
    else {
      $this->_where = "WHERE " . implode(' AND ', $clauses);
    }

    if ($this->_aclWhere) {
      $this->_where .= " AND {$this->_aclWhere} ";
    }
  }

  function groupBy() {
    $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_membership']}.membership_type_id";
  }

  function orderBy() {
    $this->_orderBy = " ORDER BY {$this->_aliases['civicrm_contact']}.sort_name, {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_membership']}.membership_type_id";
  }

  function postProcess() {

    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);
    $sql = $this->buildQuery(TRUE);

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
