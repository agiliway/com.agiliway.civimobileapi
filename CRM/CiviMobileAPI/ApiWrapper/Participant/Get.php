<?php

class CRM_CiviMobileAPI_ApiWrapper_Participant_Get implements API_Wrapper {

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
    if (empty($result['values'])) {
      return $result;
    }

    foreach ($result['values'] as &$value) {
      try {
        $imageUrl = civicrm_api3('Contact', 'getvalue', [
          'return' => "image_URL",
          'id' => $value['contact_id'],
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with getting img url of contact: " . $e->getMessage()));
      }

      $value['image_URL'] = !empty($imageUrl) ? $imageUrl : '';
    }

    return $result;
  }

}
