<?php
/**
 * @file
 * Logic for system_status output
 */

require_once ('system_status_encryption.inc');

/**
 * Return JSON formatted module information.
 */
function system_status_status_page() {
  $drupal_modules = module_list();
  $system_themes = list_themes(TRUE);

  $system_modules = array();
  foreach ($drupal_modules as $name) {
    $path = drupal_get_path('module', $name) . '/' . $name . '.info';
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
    if (isset($module['project'])) {
      if ($module['project'] == "drupal") {
        $res['core']['drupal'] = array("version" => $module['version']);
      } else {
        $res['contrib'][$module['project']] = array("version" => $module['version']);
      }
    }
  }

foreach ($system_themes as $theme) {
  if (isset($theme->info['project']) && $theme->info['project'] == "drupal")
    continue;

  if (isset($theme->info['version']) && isset($theme->info['project']))
    $res['contrib'][$theme->info['project']] = array("version" => $theme->info['version']);
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
