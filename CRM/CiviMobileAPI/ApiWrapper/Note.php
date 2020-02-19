<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Note implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    if (!empty($apiRequest['params']['entity_table']) && $apiRequest['params']['entity_table'] == 'civicrm_note') {
      unset($apiRequest['fields']['entity_table']['pseudoconstant']);
    }

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
    if ($apiRequest['action'] == 'get') {
      $newValues = [];

      foreach ($result['values'] as $key => $note) {
        if (!CRM_Core_BAO_Note::getNotePrivacyHidden($note['id'])) {
          $newValues[] = $result['values'][$key];
        }
      }

      $result['values'] = $newValues;
      $result['count'] = count($result['values']);
    }

    return $result;
  }

}
