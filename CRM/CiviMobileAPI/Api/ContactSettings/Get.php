<?php

class CRM_CiviMobileAPI_Api_ContactSettings_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns results to api
   *
   * @return array
   */
  public function getResult() {
    $result = [];
    $fieldsReturn = [];
    $fieldsNames = [];
    $listCustomFieldsID = $this->getCustomFieldsID();

    if (!empty($listCustomFieldsID)) {
      foreach ($listCustomFieldsID as $customFieldName => $customFieldID) {
        $fieldsReturn[] = 'custom_' . $customFieldID;
        $fieldsNames['custom_' . $customFieldID] = $customFieldName;
      }

      $contactSettings = civicrm_api3('Contact', 'get', [
        'return' => $fieldsReturn,
        'id' => $this->validParams['contact_id'],
      ]);

      $outValues = [];
      if ($contactSettings["is_error"] == 0) {
        $outValues['contact_id'] = $this->validParams['contact_id'];

        foreach ($contactSettings['values'][$this->validParams['contact_id']] as $settingName => $setting) {
          if (isset($fieldsNames[$settingName])) {
            $outValues[$fieldsNames[$settingName]] = $setting;
          }
        }
      }
      $result[] = $outValues;
    }
    
    return $result;
  }

  /**
   * Get custom fields names and IDs
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  private function getCustomFieldsID() {
    $out = [];
    $customGroupID = civicrm_api3('CustomGroup', 'get', [
      'return' => "id",
      'name' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS,
      'is_active' => 1,
    ]);

    if (!empty($customGroupID["values"])) {
      $customFeilds = civicrm_api3('CustomField', 'get', [
        'return' => [
          "id",
          "name",
        ],
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS,
        'is_active' => 1,
      ]);
      if (isset($customFeilds["values"])) {
        foreach ($customFeilds["values"] as $field) {
          $out[$field['name']] = $field['id'];
        }
      }
    }

    return $out;
  }
  
  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   */
  protected function getValidParams($params) {
    return $params;
  }

}
