<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Case implements API_Wrapper {

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
    $editAllCase = CRM_Core_Permission::check('access all cases and activities');
    $editMyCase = CRM_Core_Permission::check('access my cases and activities');

    $result['your_roles'] = [];

    if (isset($result['details'])) {
      $result['details'] = preg_replace('/&nbsp;/', ' ', $result['details']);

      if (!isset($result['short_description']) && isset($result['details'])) {
        $result['short_description'] = $result['details'] ? mb_substr (strip_tags(preg_replace('/\s\s+/', ' ', $result['details'])), 0, 200) : '';
      } else {
        $result['short_description'] = '';
      }
    }

    if (isset($result['contacts'])) {
      foreach ($result['contacts'] as $key => $contact) {
        if (!isset($contact['image_URL'])) {
          try {
            $imageUrl = civicrm_api3('Contact', 'getvalue', [
              'return' => 'image_URL',
              'id' => $contact['contact_id'],
            ]);
          } catch (Exception $e) {
            $imageUrl = '';
          }

          $result['contacts'][$key]['image_URL'] = $imageUrl;
        }

        if ($contact['contact_id'] == CRM_Core_Session::singleton()->get('userID')) {
          $result['your_roles'][] = $contact['role'];
        }
      }
    }

    $result['can_create_activity'] = $editMyCase || $editAllCase ? 1 : 0;
    $result['can_add_all_roles'] = $editAllCase ? 1 : 0;
    $result['can_add_ordinary_roles'] = $editAllCase ? 1 : 0;

    return $result;
  }
}
