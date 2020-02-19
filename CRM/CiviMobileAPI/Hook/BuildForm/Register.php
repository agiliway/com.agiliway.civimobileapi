<?php

class CRM_CiviMobileAPI_Hook_BuildForm_Register {

  /**
   * @param $formName
   * @param $form
   * @throws CRM_Core_Exception
   * @throws api_Exception
   */
  public function run($formName, &$form) {
    // remove $cmbHash if we are not using call from mobile application
    if (($formName != 'CRM_Event_Form_Registration_Confirm' && $formName != 'CRM_Event_Form_Registration_Register' && $formName != 'CRM_Financial_Form_Payment')
      || $formName == 'CRM_Event_Form_Registration_ThankYou') {
      $session = CRM_Core_Session::singleton();
      $cmbHash = $session->get('cmbHash');

      // check if set $cmbHash (if we are using call from mobile application)
      if ($cmbHash) {
        CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::deleteByHash($cmbHash);
      }
    }

    if ($formName == 'CRM_Event_Form_Registration_Register') {
      $session = CRM_Core_Session::singleton();
      $reqHash = CRM_Utils_Request::retrieve('cmbHash', 'String');
      $cmbHash = ($session->get('cmbHash')) ? $session->get('cmbHash') : $reqHash;

      if ($reqHash && $reqHash != $cmbHash) {
        $cmbHash = CRM_Utils_Request::retrieve('cmbHash', 'String');
        $session->set('cmbHash', $cmbHash);
      }

      // check if set $cmbHash (if we are using call from mobile application)
      if ($cmbHash) {
        if ($tmpData = CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::getByHash($cmbHash)) {
          $priceSet = json_decode($tmpData['price_set'], true);
          $personalFields = $this->findPersonalFields($tmpData);

          if ($form->elementExists('first_name')) {
            $element = $form->getElement('first_name');
            $element->setValue($personalFields['first_name']);
          }

          if ($form->elementExists('last_name')) {
            $element = $form->getElement('last_name');
            $element->setValue($personalFields['last_name']);
          }

          if ($form->elementExists('email-Primary')) {
            $element = $form->getElement('email-Primary');
            $element->setValue('');
            if (!empty($personalFields['email'])) {
              $element->setValue($personalFields['email']);
            }
          }

          $this->setValuesToPriceSet($priceSet, $form);
          $session->set('cmbHash', $cmbHash);
        } else {
          $session->set('cmbHash', NULL);
        }
      }
    }

    $customizeForms = [
      'CRM_Event_Form_Registration_Confirm',
      'CRM_Event_Form_Registration_Register',
      'CRM_Financial_Form_Payment',
      'CRM_Event_Form_Registration_ThankYou',
    ];

    if (in_array($formName, $customizeForms)) {
      $this->customizeEventRegistration();
    }
  }

  /**
   * Include scripts and styles to Event Registrations
   *
   * @throws CRM_Core_Exception
   */
  private function customizeEventRegistration() {
    $session = CRM_Core_Session::singleton();
    $cmbHash = ($session->get('cmbHash')) ? $session->get('cmbHash') : CRM_Utils_Request::retrieve('cmbHash', 'String');

    // check if set $cmbHash (if we are using call from mobile application)
    if ($cmbHash) {
      $template = CRM_Core_Smarty::singleton();
      $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
      $relURL = Civi::paths()->getUrl('[civicrm.root]/');
      $absURL = CRM_Utils_System::absoluteURL($relURL);

      $template->assign('absURL', $absURL);
      $template->assign('isDrupal7', $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7);
      $template->assign('isDrupal6', $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6);
      $template->assign('isWordpress', $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS);
      $template->assign('isJoomla', $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA);

      CRM_Core_Region::instance('page-body')->add([
        'template' => CRM_CiviMobileAPI_ExtensionUtil::path() . '/templates/CRM/CiviMobileAPI/CustomizeEventRegistration.tpl',
      ]);
    }
  }

  /**
   * @param $priceSet
   * @param $form
   */
  private function setValuesToPriceSet($priceSet, &$form) {
    foreach ($priceSet as $psID => $psFieldIds) {
      foreach ($psFieldIds[0] as $psFieldId => $items) {
        foreach ($items as $item => $psFieldValueId) {
          $priceFieldName = 'price_' . $psFieldId;
          if ($form->elementExists($priceFieldName)) {
            $element = $form->getElement($priceFieldName);
            if ($element->getType() == 'select') {
              $element->setValue(key($psFieldValueId));
            } else if ($element->getAttribute('type') == 'text' && !empty($psFieldValueId[key($psFieldValueId)])) {
              $element = $form->getElement($priceFieldName);
              $element->setValue($psFieldValueId[key($psFieldValueId)]);
            } else {
              $elements = $element->getElements();
              foreach ($elements as $el) {
                if ($el->getAttribute('type') == 'checkbox' && $el->getAttribute('name') == key($psFieldValueId)) {
                  $el->setAttribute('checked', 'checked');
                } else if ($el->getAttribute('type') == 'radio' && $el->getAttribute('value') == key($psFieldValueId)) {
                  $el->setAttribute('checked', 'checked');
                }
              }
            }
          }
          $priceFieldName = 'price_' . $psFieldId . '_' . key($psFieldValueId);
          if ($form->elementExists($priceFieldName)) {
            $element = $form->getElement($priceFieldName);
            if ($element->getAttribute('type') == 'select') {
              $element->setValue(key($psFieldValueId));
            }
          }
        }
      }
    }
  }

  /**
   * @param $tmpData
   * @return array
   */
  private function findPersonalFields($tmpData) {
    $contactId = $tmpData['contact_id'];
    $email = null;

    if ($contactId) {
      try {
        $contact = civicrm_api3('Contact', 'getsingle', [
          'return' => ["first_name", "last_name"],
          'sequential' => 1,
          'id' => $contactId
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new api_Exception(ts('Contact (id = %1) User can not be registered because contact do not exist', [1 => $contactId]), 'contact_does_not_exist');
      }

      try {
        $contactsEmail = civicrm_api3('Email', 'getsingle', [
          'return' => ["email"],
          'sequential' => 1,
          'contact_id' => $contactId,
          'is_primary' => 1,
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new api_Exception(ts('User was not registered.'), 'contact_cannot_be_registered');
      }

      $firstName = $contact['first_name'];
      $lastName = $contact['last_name'];
      if (isset($contactsEmail['email'])) {
        $email = $contactsEmail['email'];
      }
    } else {
      $firstName = $tmpData['first_name'];
      $lastName = $tmpData['last_name'];
      $email = $tmpData['email'];
    }

    return [
      'first_name' => $firstName,
      'last_name' => $lastName,
      'email' => $email,
    ];
  }

}
