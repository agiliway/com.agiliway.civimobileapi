<?php

/**
 * Class handles CiviMobileCustomFields api
 */
class CRM_CiviMobileAPI_Api_CiviMobileAvailableContactGroup_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns results to api
   *
   * @return array
   */
  public function getResult() {
    $availableGroups = [];
    $groups = $this->getGroups($this->validParams['is_hidden']);
    $contactGroupIds = $this->getContactGroups($this->validParams['contact_id']);

    foreach ($groups as $group) {
      if (!in_array($group['id'],$contactGroupIds)) {
        $availableGroups[] = $group;
      }
    }

    return $availableGroups;
  }

  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   * @throws \api_Exception
   */
  protected function getValidParams($params) {
    $contact = new CRM_Contact_BAO_Contact();
    $contact->id = $params['contact_id'];
    $contactExistence = $contact->find(TRUE);
    if (empty($contactExistence)) {
      throw new api_Exception('Contact(id=' . $params['contact_id'] . ') does not exist.', 'contact_does_not_exist');
    }

    if (!isset($params['is_hidden'])) {
      $params['is_hidden'] = NULL;
    }

    return [
      'contact_id' => $params['contact_id'],
      'is_hidden' => $params['is_hidden']
    ];
  }

  /**
   * Gets active simple groups
   */
  private function getGroups($isHidden) {
    $groupsParams = [
      'sequential' => 1,
      'is_active' => 1,
      'saved_search_id' => ['IS NULL' => 1],
      'options' => ['limit' => 0],
      'return' => ['name', 'title', 'id'],
    ];

    if (!is_null($isHidden)) {
      $groupsParams['is_hidden'] = $isHidden;
    }

    $groups = [];
    try {
      $groupsData = civicrm_api3('Group', 'get', $groupsParams);
    } catch (CiviCRM_API3_Exception $e) {
      return $groups;
    }

    if (!empty($groupsData['values'])) {
      $groups = $groupsData['values'];
    }

    return $groups;
  }

  /**
   * Gets list of group id with contact
   *
   * @param $contactId
   *
   * @return array
   */
  private function getContactGroups($contactId) {
    $groupIds = [];
    try {
      $groupContacts = civicrm_api3('GroupContact', 'get', [
        'sequential' => 1,
        'contact_id' => $contactId,
        'options' => ['limit' => 0],
        'return' => ["group_id"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return $groupIds;
    }

    if (!empty($groupContacts['values'])) {
      foreach ($groupContacts['values'] as $groupContact) {
        $groupIds[] = (int) $groupContact['group_id'];
      }
    }

    return $groupIds;
  }

}
