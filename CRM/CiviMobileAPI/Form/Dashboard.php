<?php
/*--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
+--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
+--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +-------------------------------------------------------------------*/

class CRM_CiviMobileAPI_Form_Dashboard extends CRM_Core_Form {

  /**
   * @throws \CRM_Core_Exception
   * @throws \Exception
   */
  public function preProcess() {
    parent::preProcess();

    $cid = CRM_Utils_Request::retrieve('cid', 'Integer');

    if (!($cid == CRM_Core_Session::singleton()->getLoggedInContactID() || CRM_Core_Permission::check('administer CiviCRM'))) {
      throw new Exception('Permission denied');
    }

    $this->add('hidden', 'cid', $cid);
  }

  /**
   * Build the form object.
   *
   * @return void
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => ts('Logout from mobile'),
        'isDefault' => TRUE,
      ]
    ]);
  }

  /**
   * @throws \CiviCRM_API3_Exception
   */
  public function postProcess() {
    $params = $this->exportValues();

    CRM_CiviMobileAPI_Utils_Contact::logoutFromMobile($params['cid']);
  }

}
