<?php


class CRM_CiviMobileAPI_ApiWrapper_Activity_Notification implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   *   modified $apiRequest
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   *   modified $result
   */
  public function toApiOutput($apiRequest, $result) {
    if ($apiRequest['action'] == "create") {
      $isActionEdit = isset($apiRequest["params"]['id']);
      if ($isActionEdit) {
        $apiRequest['action'] = "edit";
      }

      $isActivityBelongsToCase = isset($apiRequest['params']['case_id']);
      if ($isActivityBelongsToCase) {
        $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification("Case", $apiRequest['action'], $apiRequest['params']['case_id']);
        $notificationManager->sendNotification();
      } else {
        $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ActivityPushNotification($apiRequest['entity'], $apiRequest['action'], key($result['values']));
        $notificationManager->sendNotification();
      }
    } elseif ($apiRequest['action'] == "delete") {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification($apiRequest['entity'], $apiRequest['action'], key($result['values']));
      $notificationManager->sendNotification();
    }

    return $result;
  }

}
