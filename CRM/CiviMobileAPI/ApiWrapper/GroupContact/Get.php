<?php

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
   * @throws API_Exception
   */
  public function toApiOutput($apiRequest, $result) {

    if (!empty($apiRequest['params']['smart_group']) && $apiRequest['params']['smart_group'] == 1 ) {
      try {
        $smartGroupResult = civicrm_api3('Group', 'get', [
          'sequential' => 1,
          "return" => ["title", "description"],
          'saved_search_id' => ['IS NOT NULL' => 1],
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Can not get group info."));
      }

      $contactCacheObject = new CRM_Contact_DAO_GroupContactCache();
      $contactCacheObject->contact_id = $apiRequest['params']['contact_id'];
      $contactCacheObject->find();
      while ($contactCacheObject->fetch()) {
        $groups[] = $contactCacheObject->group_id;
      }

      $validatedSmartGroup = [];
      foreach ($smartGroupResult['values'] as $value) {
        if (in_array($value['id'], $groups)) {
          $validatedSmartGroup[] = $value;
        }

        $smartGroupResult['count'] = count($validatedSmartGroup);
        $smartGroupResult['values'] = $validatedSmartGroup;
        $value['contact_id'] = $apiRequest['params']['contact_id'];
      }

      return $smartGroupResult;
    }
    else {
      foreach ($result['values'] as &$value) {
        $value['group_contact_status'] = !empty($apiRequest['params']['status']) ? $apiRequest['params']['status'] : '';
      }
      return $result;
    }

  }

}
