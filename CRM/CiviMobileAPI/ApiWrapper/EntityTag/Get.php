<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_EntityTag_Get implements API_Wrapper {

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
    foreach ($result['values'] as &$value) {
      if (empty($value['tag_id'])) {
       continue;
      }

      try {
        $tagInfo = civicrm_api3('Tag', 'getsingle', [
          'id' => $value['tag_id'],
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with getting info for tag: " . $e->getMessage()));
      }

      $value['name'] = !empty($tagInfo['name']) ? $tagInfo['name'] : '';
      if ($tagInfo['is_selectable'] == '1' && empty($tagInfo['parent_id'])) {
        $value['tag_section'] = 'Tree';
      }

      if (!empty($tagInfo['parent_id'])) {
        try {
          $parentTagInfo = civicrm_api3('Tag', 'getsingle', [
            'sequential' => 1,
            'return' => ["name", "is_tagset"],
            'id' => $tagInfo['parent_id'],
          ]);
        } catch (CiviCRM_API3_Exception $e) {
          throw new \API_Exception(ts("Something wrong with getting info for parent tag: " . $e->getMessage()));
        }

        if ($parentTagInfo['is_tagset'] == '1') {
          $value['tag_section'] = 'Set';
          $value['name_set'] = !empty($parentTagInfo['name']) ? $parentTagInfo['name'] : '';
        }
      }
    }

    return $result;
  }

}
