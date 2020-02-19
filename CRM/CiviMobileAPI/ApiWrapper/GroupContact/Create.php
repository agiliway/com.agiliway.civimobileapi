<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_GroupContact_Create implements API_Wrapper {

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
    $status = $apiRequest['params']['status'];
    if (!empty($status) && !is_array($status) && ($status == "Added" || $status == "Removed")) {
      $groupId = $apiRequest['params']['group_id'];

      try {
        $groupInfo = civicrm_api3('Group', 'getsingle', [
          'return' => ["title"],
          'id' => $groupId,
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with getting info for group: " . $e->getMessage()));
      }

      $result['title'] = !empty($groupInfo['title']) ? $groupInfo['title'] : '';
      $result['date_added'] = CRM_Utils_Date::getToday(NULL, 'F jS, Y g:i A');
      $result['group_contact_status'] = $status;
      if ($status == "Removed") {
        $result['out_date'] = CRM_Utils_Date::getToday(NULL, 'F jS, Y g:i A');
      }
    }

    return $result;
  }

}
