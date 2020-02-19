<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_GroupContact_Get implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if (!empty($apiRequest['params']['smart_group']) && $apiRequest['params']['smart_group'] == 1 ) {
      if (empty($apiRequest['params']['contact_id'])) {
        throw new api_Exception('Field \'contact_id\' is required field.', 'required_field');
      }

      $contact = new CRM_Contact_BAO_Contact();
      $contact->id = $apiRequest['params']['contact_id'];
      $contactExistence = $contact->find(TRUE);
      if (empty($contactExistence)) {
        throw new api_Exception('Contact(id=' . $apiRequest['params']['contact_id'] . ') does not exist.', 'contact_does_not_exist');
      }

      $contactGroups = CRM_Contact_BAO_GroupContactCache::contactGroup($apiRequest['params']['contact_id']);

      return civicrm_api3_create_success(!empty($contactGroups['group']) ? $contactGroups['group'] : []);
    }

    if (!empty($result['values']) && is_array($result['values'])) {
      foreach ($result['values'] as &$value) {
        $value['group_contact_status'] = !empty($apiRequest['params']['status']) ? $apiRequest['params']['status'] : '';
      }
    }

    return $result;
  }

}
