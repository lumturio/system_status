<?php
/**
 * @file
 * Logic for system_status output
 */
require_once('system_status_encryption.inc');

/**
 * Return JSON formatted module information.
 */
function system_status_status_page() {
  $drupal_modules = module_list();
//var_dump($system_modules);

$system_modules = array();

foreach($drupal_modules as $name) {
  $path = drupal_get_path('module', $name) . '/' . $name . '.info';
  $info = drupal_parse_info_file($path);
  $system_modules[$info['project']] = $info;
}

// we want to do our best to guess the correct drupal version
if(isset($drupal_modules['system'])) {
  $path = drupal_get_path('module', 'system') . '/system.info';
  $info = drupal_parse_info_file($path);
  $system_modules[$info['project']] = $info;
}

  // Needless initialisation, but hey.
  $res = array(
    "core" => array(),
    "contrib" => array(),
    "custom" => "disabled",
  );

  foreach ($system_modules as $module) {
    if(isset($module['project'])) {
      if($module['project'] == "drupal") {
        $res['core']['drupal'] = array("version" => $module['version']);
      } else {
        $res['contrib'][$module['project']] = array("version" => $module['version']);
      }
    }
  }

  if (variable_get('system_status_service_allow_drupalstatus', 1) == 1 && extension_loaded('mcrypt')) {
    $res = SystemStatusEncryption::encrypt(json_encode(array("system_status" => $res)));
    header('Content-Type: application/json');
    echo json_encode(array("system_status" => "encrypted", "data" => $res, "drupal_version" => "6"));
  }
  else {
    header('Content-Type: application/json');
    echo json_encode(array("system_status" => $res, "drupal_version" => "6"));
  }
}
