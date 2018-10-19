<?php

/**
 * Return the list of different permissions
 *
 * @return mixed
 */
function civicrm_api3_civi_mobile_permission_get() {
  try {
    $permissions = [];

    //Access Permissions
    $accessToCiviCrm = CRM_Core_Permission::check('access CiviCRM');
    $permissions['access'] = [
      "accessCiviCRM" => $accessToCiviCrm ? 1 : 0,
    ];

    //Contact Permissions
    $viewAllContacts = CRM_Core_Permission::check('view all contacts');
    $editAllContacts = CRM_Core_Permission::check('edit all contacts');
    $viewMyContact = CRM_Core_Permission::check('view my contact');
    $editMyContact = CRM_Core_Permission::check('edit my contact');
    $addContact = CRM_Core_Permission::check('add contacts');
    $deleteContact = CRM_Core_Permission::check('delete contacts');

    $permissions["contact"] = [
      "view" => [
        "all" => ($viewAllContacts || $editAllContacts) ? 1 : 0,
        "my" => ($viewMyContact || $editMyContact || $viewAllContacts || $editAllContacts) ? 1 : 0,
      ],
      "edit" => [
        "all" => $editAllContacts ? 1 : 0,
        "my" => ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      "create" => $addContact ? 1 : 0,
      "delete" => ($viewAllContacts || $viewMyContact || $editMyContact || $editAllContacts) && $deleteContact ? 1 : 0,
      "search" => $viewMyContact ? 1 : 0,
    ];

    $deleteActivities = CRM_Core_Permission::check('Delete activities');

    // Activity Permission
    $permissions["activity"] = [
      "view" => [
        "all" => ($viewAllContacts || $editAllContacts) ? 1 : 0,
        "my" => ($viewMyContact || $editMyContact || $viewAllContacts || $editAllContacts) ? 1 : 0,
      ],
      "edit" => [
        "all" => $editAllContacts ? 1 : 0,
        "my" => ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      "create" => [
        "all" => ($viewAllContacts || $editAllContacts) ? 1 : 0,
        "my" => ($viewMyContact || $editMyContact || $viewAllContacts || $editAllContacts) ? 1 : 0,
      ],
      "delete" => [
        "my" => $deleteActivities ? 1 : 0,
      ]

    ];

    // Case Permissions
    $editAllCase = CRM_Core_Permission::check('access all cases and activities');
    $editMyCase = CRM_Core_Permission::check('access my cases and activities');

    $permissions["case"] = [
      "view" => [
        "all" => $editAllCase ? 1 : 0,
        "my" => ($editMyCase || $editAllCase) ? 1 : 0,
      ],
      "edit" => [
        "all" => $editAllCase ? 1 : 0,
        "my" => ($editMyCase || $editAllCase) ? 1 : 0,
      ],
      "activity" => [
        "view" => [
          "all" => ($viewAllContacts || $editAllContacts) && ($editAllCase) ? 1 : 0,
          "my" => ($viewMyContact || $editMyContact || $viewAllContacts || $editAllContacts) && ($editMyCase || $editAllCase) ? 1 : 0,
        ],
        "edit" => [
          "all" => $editAllContacts && $editAllCase ? 1 : 0,
          "my" => ($editMyContact || $editAllContacts) ? 1 : 0,
        ],
      ],
      "role" => 0, //TODO: finish this
    ];

    // Event Permissions
    $accessCiviEvent = CRM_Core_Permission::check("access CiviEvent");
    $viewAllEvent = CRM_Core_Permission::check("view event info");
    $editAllEvents = CRM_Core_Permission::check("edit all events");
    $editEventParticipants = CRM_Core_Permission::check('edit event participants');

    $permissions["event"] = [
        "view" => [
          "all" => ($accessCiviEvent && $viewAllEvent) ? 1 : 0,
          "my" => $accessCiviEvent ? 1 : 0,
        ],
        "edit" => [
          "all" => ($accessCiviEvent && $editAllEvents) ? 1 : 0,
          "my" => ($accessCiviEvent && $editAllEvents) ? 1 : 0,
        ],
        "register" => $editEventParticipants ? 1 : 0,
    ];

    $nullObject = CRM_Utils_Hook::$_nullObject;
    CRM_Utils_Hook::singleton()
      ->commonInvoke(1, $permissions, $nullObject, $nullObject, $nullObject, $nullObject, $nullObject, 'civimobile_permission', '');

    $result = [
      'is_error' => 0,
      'version' => 3,
      'values' => [$permissions],
    ];
  } catch (Exception $e) {
    $result = [
      'is_error' => 1,
      'version' => 3,
      'values' => 'Something went wrong',
    ];
  }

  return $result;

}
