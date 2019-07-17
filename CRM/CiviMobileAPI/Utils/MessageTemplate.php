<?php

/**
 * Class provide MessageTemplate helper methods
 */
class CRM_CiviMobileAPI_Utils_MessageTemplate {

  /**
   * Get message template info by workflow id
   *
   * @param $workflowId
   *
   * @return array|bool
   */
  public static function getByWorkflowId($workflowId) {
    try {
      $messageTemplate = civicrm_api3('MessageTemplate', 'getsingle', [
        'sequential' => 1,
        'workflow_id' => $workflowId,
        'is_default' => 1,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return !empty($messageTemplate) ? $messageTemplate : false;
  }

}
