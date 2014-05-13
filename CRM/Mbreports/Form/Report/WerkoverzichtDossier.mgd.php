<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Mbreports_Form_Report_WerkoverzichtDossier',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'WerkoverzichtDossier',
      'description' => 'WerkoverzichtDossier (org.civicoop.dgw.mbreports)',
      'class_name' => 'CRM_Mbreports_Form_Report_WerkoverzichtDossier',
      'report_url' => 'org.civicoop.dgw.mbreports/werkoverzichtdossier',
      'component' => 'CiviCase',
    ),
  ),
);