<?php

/**
 * @file
 * Contains \Drupal\pants\Form\PantsSettingsForm.
 */

namespace Drupal\system_status\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure pants settings for this site.
 */
class SystemStatusSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'system_status_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('system_status.settings');

    $form['system_status_service_allow_drupalstatus'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow reporting to DrupalStatus.org'),
      '#description' => t('Allow reports to be generated by DrupalStatus. The reports will be stored and send using encryption and are only accessible using your account credentials on DrupalStatus.org'),
      '#default_value' => $config->get('system_status_service_allow_drupalstatus'),
    );

    $form['add_site'] = array(
      '#type' => 'submit',
      '#value' => t('Add this site to your DrupalStatus.org overview'),
      '#submit' => array('system_status_add_site'),
    );

/*
  $form['system_status_need_protect_token'] = array(
    '#type' => 'checkbox',
    '#title' => t('Protect all calls using a unique token'),
    '#description' => t('Require that a code is passed to the url to view the JSON ouput. You need the following private key to open the page: @token',
      array("@token" => variable_get('system_status_token', 'Error-no-token'))),
    '#default_value' => variable_get('system_status_need_protect_token', 0),
    '#states' => array(
      'disabled' => array(
        ':input[name="system_status_service_allow_drupalstatus"]' => array('checked' => TRUE),
      ),
      'checked' => array(
        array(':input[name="system_status_need_protect_token"]' => array('checked' => TRUE)),
        array(':input[name="system_status_service_allow_drupalstatus"]' => array('checked' => TRUE)),
      ),
    ),
  );

  $form['system_status_need_encryption'] = array(
    '#type' => 'checkbox',
    '#title' => t('Protect all calls using encryption'),
    '#description' => t('Selecting this option will encrypt all output, rendering the data useless for hackers during a man-in-the-middle attack. You need the following private key to decode the message: @encrypt_token ',
      array("@encrypt_token" => variable_get("system_status_encrypt_token", 'Error-no-token'))),
    '#default_value' => variable_get('system_status_need_encryption', 0),
    '#states' => array(
      'disabled' => array(
        ':input[name="system_status_service_allow_drupalstatus"]' => array('checked' => TRUE),
      ),
      'checked' => array(
        array(':input[name="system_status_need_encryption"]' => array('checked' => TRUE)),
        array(':input[name="system_status_service_allow_drupalstatus"]' => array('checked' => TRUE)),
      ),
    ),
    );
  */
    $form['system_status_do_match_core'] = array(
      '#type' => 'checkbox',
      '#title' => t('Report core modules'),
      '#description' => t('Include core modules and their versions in your report. This option is required for reporting of your website\'s Drupal version and available updates.'),
      '#default_value' => $config->get('system_status_do_match_core'),
    );

    $form['system_status_do_match_contrib'] = array(
      '#type' => 'checkbox',
      '#title' => t('Report contrib modules'),
      '#description' => t('Include contrib modules and their versions in your report. This option is required for reporting of all your website\'s modules versions and available updates.'),
      '#default_value' => $config->get('system_status_do_match_contrib'),
    );

    $form['system_status_match_contrib_mode'] = array(
      '#type' => 'radios',
      '#title' => t('Where are your contrib modules stored ?'),
      '#description' => t('When unsure, leave this option as set.'),
      '#default_value' => $config->get('system_status_match_contrib_mode'),
      '#options' => array(
        0 => 'modules/',
        1 => 'modules/contrib/',
        2 => 'Other'),
      '#states' => array(
        'visible' => array(
          ':input[name="system_status_do_match_contrib"]' => array('checked' => TRUE),
        ),
      ),
    );

    $form['system_status_preg_match_contrib'] = array(
      '#type' => 'textfield',
      '#title' => t('Regular expression to match contrib modules'),
      '#default_value' => $config->get('system_status_preg_match_contrib'),
      '#states' => array(
        'visible' => array(
          ':input[name="system_status_match_contrib_mode"]' => array('value' => 2),
        ),
      ),
    );

    $form['system_status_do_match_custom'] = array(
      '#description' => t('Scan for custom modules using a regular expression.'),
      '#type' => 'checkbox',
      '#title' => t('Report custom modules'),
      '#default_value' => $config->get('system_status_do_match_custom'),
      '#states' => array(
        'visible' => array(
          ':input[name="system_status_match_contrib_mode"]' => array('value' => 2),
          ':input[name="system_status_match_contrib_mode"]' => array('value' => 1),
        ),
      ),
    );

    $form['system_status_preg_match_custom'] = array(
      '#type' => 'textfield',
      '#title' => t('Regular expression to match custom modules'),
      '#default_value' => $config->get('system_status_preg_match_custom'),
      '#states' => array(
        'visible' => array(
          ':input[name="system_status_do_match_custom"]' => array('visible' => TRUE),
        ),
      ),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->configFactory->get('system_status.settings');
    $config->set('system_status_service_allow_drupalstatus', $form_state['values']['system_status_service_allow_drupalstatus']);
    $config->set('system_status_do_match_core', $form_state['values']['system_status_do_match_core']);
    $config->set('system_status_do_match_contrib', $form_state['values']['system_status_do_match_contrib']);
    $config->set('system_status_match_contrib_mode', $form_state['values']['system_status_match_contrib_mode']);
    $config->set('system_status_preg_match_contrib', $form_state['values']['system_status_preg_match_contrib']);
    $config->set('system_status_do_match_custom', $form_state['values']['system_status_do_match_custom']);
    $config->set('system_status_preg_match_custom', $form_state['values']['system_status_preg_match_custom']);
    $config->save();
    parent::submitForm($form, $form_state);
  }



}
