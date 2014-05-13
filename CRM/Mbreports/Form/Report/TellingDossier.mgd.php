<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Mbreports_Form_Report_TellingDossier',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'TellingDossier',
      'description' => 'Dossiertellingen',
      'class_name' => 'CRM_Mbreports_Form_Report_TellingDossier',
      'report_url' => 'civirm/tellingdossier',
      'component' => 'CiviCase',
    ),
  ),
);