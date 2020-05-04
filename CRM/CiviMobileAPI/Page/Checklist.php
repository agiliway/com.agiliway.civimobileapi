<?php

class CRM_CiviMobileAPI_Page_Checklist extends CRM_Core_Page {

  /**
   * @return mixed
   */
  public function run() {
    $checklist = new CRM_CiviMobileAPI_Utils_Checklist();
    $checklist->checkAllAvailableItems();

    $currentContact = CRM_Contact_BAO_Contact::findById(CRM_Core_Session::singleton()->getLoggedInContactID());
    $apiKey = $currentContact->api_key ? $currentContact->api_key : CRM_CiviMobileAPI_Authentication_Login::setApiKey($currentContact->id);
    $paramsToRest = 'entity=CiviMobileSystem&action=get&api_key=' . $apiKey . '&key=' . CIVICRM_SITE_KEY . '&json={"sequential":1}';

    $authUrl = CRM_Utils_System::url('civicrm/civimobile/auth', NULL, TRUE);
    $restPathUrl = self::concatenateUrl(
      str_replace("/administrator/", "", CIVICRM_UF_BASEURL) . substr((new CRM_CiviMobileAPI_Utils_RestPath())->get(),1),
      $paramsToRest);
    $restUrl = self::concatenateUrl((new CRM_CiviMobileAPI_Utils_RestPath())->getAbsoluteUrl(), $paramsToRest);

    $this->assign([
      'authUrl' => $authUrl,
      'restPathUrl' => $restPathUrl,
      'restUrl' => $restUrl,
      'checklist_params' => $checklist->getCheckedItemsResult(),
      'system_info' => $checklist->getSystemInfoReport()
    ]);

    CRM_Core_Resources::singleton()->addStyleFile('com.agiliway.civimobileapi', 'css/civimobileapiChecklist.css', 200, 'html-header');
    return parent::run();
  }

  /**
   * @param $url
   * @param $params
   * @return string
   */
  private static function concatenateUrl($url, $params) {
    if (strpos($url,'?')) {
      $url .= '&';
    } else {
      $url .= '?';
    }

    return $url . $params;
  }

}
