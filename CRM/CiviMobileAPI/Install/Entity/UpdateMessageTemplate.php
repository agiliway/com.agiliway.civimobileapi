<?php

class CRM_CiviMobileAPI_Install_Entity_UpdateMessageTemplate implements CRM_CiviMobileAPI_Install_Entity_InstallInterface {

  public function install() {
    $this->updateOnlineTemplate();
    $this->updateOfflineTemplate();
  }

  /**
   * Updates message template event_online_receipt
   *
   * @throws API_Exception
   */
  public function updateOnlineTemplate() {
    $workflowIdOnline = CRM_CiviMobileAPI_Utils_OptionValue::getId('msg_tpl_workflow_event', 'event_online_receipt');
    $messageTemplateOnline = CRM_CiviMobileAPI_Utils_MessageTemplate::getByWorkflowId($workflowIdOnline);

    if (empty($messageTemplateOnline)) {
      throw new \API_Exception(ts("Can not find template with these parameters: valueName 'event_online_receipt' and groupName 'msg_tpl_workflow_event'"));
    }

    $newHtmlOnline = '
               <center>
                 {if $file_name}
                    <table width="700" border="1" cellpadding="0" cellspacing="0" id="crm-event_receipt" 
                          style="font-family: Arial, Verdana, sans-serif; text-align: center;">
                     <tr>
                       <td colspan="2">
                         <img src="{$file_name}" alt="QR code"/>
                       </td>
                     </tr>
                   </table>
                 {/if}
               </center>
               </body>';

    $pos = strpos($messageTemplateOnline['msg_html'], '$file_name');
    if ($pos === false) {
      $newHtmlTemplateOnline = str_replace('</body>', $newHtmlOnline, $messageTemplateOnline['msg_html']);
      try {
        civicrm_api3('MessageTemplate', 'create', [
          'id' => $messageTemplateOnline['id'],
          'msg_title' => $messageTemplateOnline['msg_title'],
          'msg_html' => $newHtmlTemplateOnline,
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with updating message template " . $messageTemplateOnline['msg_title'] . ": " . $e->getMessage()));
      }
    }

  }

  /**
   * Updates message template event_offline_receipt
   *
   * @throws API_Exception
   */
  public function updateOfflineTemplate() {
    $workflowIdOffline = CRM_CiviMobileAPI_Utils_OptionValue::getId('msg_tpl_workflow_event', 'event_offline_receipt');
    $messageTemplateOffline = CRM_CiviMobileAPI_Utils_MessageTemplate::getByWorkflowId($workflowIdOffline);

    if (empty($messageTemplateOffline)) {
      throw new \API_Exception(ts("Can not find template with these parameters: valueName 'event_offline_receipt' and groupName 'msg_tpl_workflow_event'"));
    }

    $newHtmlOffline = '
               <center>
                 {if $file_name}
                    <table width="620" border="1" cellpadding="0" cellspacing="0" id="crm-event_receipt" 
                          style="font-family: Arial, Verdana, sans-serif; text-align: center;">
                     <tr>
                       <td colspan="2">
                         <img src="{$file_name}" alt="QR code"/>
                       </td>
                     </tr>
                   </table>
                 {/if}
               </center>
               </body>';

    $pos = strpos($messageTemplateOffline['msg_html'], '$file_name');
    if ($pos === false) {
      $newHtmlTemplateOffline = str_replace('</body>', $newHtmlOffline, $messageTemplateOffline['msg_html']);
      try {
        civicrm_api3('MessageTemplate', 'create', [
          'id' => $messageTemplateOffline['id'],
          'msg_title' => $messageTemplateOffline['msg_title'],
          'msg_html' => $newHtmlTemplateOffline,
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with updating message template " . $messageTemplateOffline['msg_title'] . ": " . $e->getMessage()));
      }
    }

  }

}
