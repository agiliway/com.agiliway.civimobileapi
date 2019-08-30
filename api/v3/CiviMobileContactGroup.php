<?php

/**
 * Deletes ContactGroup entity
 *
 * @param array $params
 *
 * @return array
 * @throws \api_Exception
 */
function civicrm_api3_civi_mobile_contact_group_delete($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForDeleteContactGroup()) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $contactGroupId = $params['id'];

  $groupContact = new CRM_Contact_BAO_GroupContact();
  $groupContact->id = $contactGroupId;
  $groupContactExistence = $groupContact->find(TRUE);
  if (empty($groupContactExistence)) {
    throw new api_Exception('ContactGroup(id=' . $contactGroupId . ' does not exist.)', 'contact_group_does_not_exist');
  }

  $groupContact->delete();

  return civicrm_api3_create_success([['deleted_contact_group_id' => $contactGroupId]]);
}
