<?php

class CRM_CiviMobileAPI_Form_CalendarSettings extends CRM_Core_Form {

  /**
   * Build the form object.
   *
   * @return void
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
    $settings = $this->getFormSettings();
    foreach ($settings as $name => $setting) {
      if (isset($setting['html_type'])) {
        switch ($setting['html_type']) {
          case 'Text':
            $this->addElement('text', $name, ts($setting['description']), $setting['html_attributes'], []);
            break;

          case 'Checkbox':
            $this->addElement('checkbox', $name, ts($setting['description']), '', '');
            break;

          case 'Select':
            $options = [];
            if (isset($setting['option_values'])) {
              $options = $setting['option_values'];
            }
            elseif (isset($setting['pseudoconstant'])) {
              $options = civicrm_api3('Setting', 'getoptions', [
                'field' => CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getPrefix() . $name,
              ]);
              $options = $options['values'];
            }
            $select = $this->addElement('select', $name, ts($setting['description']), $options, $setting['html_attributes']);
            if (isset($setting['multiple'])) {
              $select->setMultiple($setting['multiple']);
            }
            break;
        }
      }
    }

    $this->assign('elementNames', $this->getRenderableElementNames());
    if (!CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarCompatible()) {
      $this->assign('synchronizationNotice', ts('The CiviCRM has a CiviCalendar installed, but its version is not enough to work with CiviMobileAPI. We recommend updating your calendar to the 3.4.x version or latest.'));
    } elseif (CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarEnable() && !CRM_CiviMobileAPI_Utils_Calendar::isActivateCiviCalendarSettings()) {
      $this->assign('synchronizationNotice', ts('CiviCalendar and CiviMobile calendar are not synchronized! This may cause different info is shown on the calendar in CiviMobile app. It is recommended to set “Synchronize with CiviCalendar” flag to keep both calendars synchronized.'));
    } elseif (CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarEnable() && CRM_CiviMobileAPI_Utils_Calendar::isActivateCiviCalendarSettings()) {
      $this->assign('synchronizationNotice', ts("CiviCalendar and CiviMobile calendar are  synchronized! You can change settings in <a href= " . CRM_Utils_System::url('civicrm/admin/calendar') . ">CiviCalendar Settings</a>"));
    }

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);
  }

  /**
   * @throws \CiviCRM_API3_Exception
   */
  public function postProcess() {
    parent::postProcess();
    $changed = $this->_submitValues;//TODO: use $this->exportValues();
    $settings = $this->getFormSettings(TRUE);

    foreach ($settings as &$setting) {
      if ($setting['html_type'] == 'Checkbox') {
        $setting = FALSE;
      }
      else {
        $setting = NULL;
      }
    }

    $settingsToSave = array_merge($settings, array_intersect_key($changed, $settings));
    $this->saveSetting($settingsToSave);
    CRM_Core_Session::singleton()->setStatus(ts('Configuration Updated'), ts('CiviMobile Calendar Settings'), 'success');
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/civimobile/calendar/settings'));
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    $elementNames = [];

    foreach ($this->_elements as $element) {
      $label = $element->getLabel();

      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }

    return $elementNames;
  }

  /**
   * Get the settings we are going to allow to be set on this form.
   *
   * @param bool $metadata
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  function getFormSettings($metadata = TRUE) {
    $nonPrefixedSettings = [];
    $settings = civicrm_api3('setting', 'getfields', ['filters' => CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getFilter()]);

    if (!empty($settings['values'])) {
      foreach ($settings['values'] as $name => $values) {
        if ($metadata) {
          $nonPrefixedSettings[CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getName($name, FALSE)] = $values;
        }
        else {
          $nonPrefixedSettings[CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getName($name, FALSE)] = NULL;
        }
      }
    }
    if (!CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarEnable() || !CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarCompatible()) {
      unset($nonPrefixedSettings['synchronize_with_civicalendar']);
    }

    $components = civicrm_api3('Setting', 'getvalue', [
      'name' => "enable_components",
    ]);
    if (!in_array('CiviCase', $components)) {
      unset($nonPrefixedSettings['case_types']);
    }
    if (!in_array('CiviEvent', $components)) {
      unset($nonPrefixedSettings['event_types']);
    }

    return $nonPrefixedSettings;
  }

  function setDefaultValues() {
    $settings = $this->getFormSettings(FALSE);
    $defaults = [];

    $existing = CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::get(array_keys($settings));

    if ($existing) {
      foreach ($existing as $name => $value) {
        $defaults[$name] = $value;
      }
    }

    return $defaults;
  }

  /**
   * Save settings
   *
   * @param $settings
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function saveSetting($settings) {
    $prefixedSettings = [];

    foreach ($settings as $name => $value) {
      $prefixedSettings[CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getName($name, TRUE)] = $value;
    }

    if (!empty($prefixedSettings)) {
      civicrm_api3('setting', 'create', $prefixedSettings);
    }
  }

}
