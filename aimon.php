<?php
require_once 'aimon.civix.php';

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
  require_once $autoload;
}

/**
 * Implementation of hook_civicrm_config
 */
function aimon_civicrm_config(&$config)
{
  _aimon_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_install
 */
function aimon_civicrm_install()
{
  $groupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'sms_provider_name', 'id', 'name');

  civicrm_api3('OptionValue', 'create', [
    'option_group_id' => $groupID,
    'label' => 'Aimon',
    'value' => 'eu.civihost.aimonsms',
    'name'  => 'aimon',
    'is_default' => 1,
    'is_active'  => 1,
  ]);

  return _aimon_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function aimon_civicrm_uninstall()
{
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'aimon', 'id', 'name');

  if ($optionID) {
    CRM_Core_BAO_OptionValue::del($optionID);
  }

  $filter =  ['name'  => 'eu.civihost.aimonsms'];
  $Providers = CRM_SMS_BAO_Provider::getProviders(FALSE, $filter, FALSE);
  if ($Providers) {
    foreach ($Providers as $key => $value) {
      CRM_SMS_BAO_Provider::del($value['id']);
    }
  }
  return;
}

/**
 * Implementation of hook_civicrm_enable
 */
function aimon_civicrm_enable()
{
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'aimon', 'id', 'name');

  if ($optionID) {
    CRM_Core_BAO_OptionValue::setIsActive($optionID, TRUE);
  }

  $filter = ['name' => 'eu.civihost.aimonsms'];
  $Providers = CRM_SMS_BAO_Provider::getProviders(FALSE, $filter, FALSE);
  if ($Providers) {
    foreach ($Providers as $key => $value) {
      CRM_SMS_BAO_Provider::setIsActive($value['id'], TRUE);
    }
  }
  return _aimon_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function aimon_civicrm_disable()
{
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'aimon', 'id', 'name');

  if ($optionID) {
    CRM_Core_BAO_OptionValue::setIsActive($optionID, FALSE);
  }

  $filter = ['name' => 'eu.civihost.aimonsms'];
  $Providers = CRM_SMS_BAO_Provider::getProviders(FALSE, $filter, FALSE);
  if ($Providers) {
    foreach ($Providers as $key => $value) {
      CRM_SMS_BAO_Provider::setIsActive($value['id'], FALSE);
    }
  }
  return;
}
