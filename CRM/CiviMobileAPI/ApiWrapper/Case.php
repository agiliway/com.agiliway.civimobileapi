<?php

class CRM_CiviMobileAPI_ApiWrapper_Case implements API_Wrapper {

  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Adds next fields:
   * - short_description
   * - image_URL for each contact
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if(isset($result['details'])) {
      $result['details'] = preg_replace('/&nbsp;/', ' ', $result['details']);
      if(!isset($result['short_description']) && isset($result['details'])) {
          $result['short_description'] = $result['details'] ? mb_substr (strip_tags(preg_replace('/\s\s+/', ' ', $result['details'])), 0, 200) : '';
      } else {
        $result['short_description'] = '';
      }
    }
    
    if(isset($result['contacts'])) {
      foreach ($result['contacts'] as $key => $contact) {
        if(!isset($contact['image_URL'])) {
          try {
            $imageUrl = civicrm_api3('Contact', 'getvalue', [
              'return' => "image_URL",
              'id' => $contact['contact_id'],
            ]);
          } catch (Exception $e) {
            $imageUrl = '';
          }

          $result['contacts'][$key]['image_URL'] = $imageUrl;
        }
      }
    }
    return $result;
  }
}
