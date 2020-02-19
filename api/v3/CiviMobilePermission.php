<?php

/**
 * Returns the list of different permissions
 *
 * @return mixed
 */
function civicrm_api3_civi_mobile_permission_get() {
  try {
    $permissions = [];

    $accessToCiviCrm = CRM_Core_Permission::check('access CiviCRM');
    $accessUploadedFiles = CRM_Core_Permission::check('access uploaded files');
    $viewAllContacts = CRM_Core_Permission::check('view all contacts');
    $editAllContacts = CRM_Core_Permission::check('edit all contacts');
    $viewMyContact = CRM_Core_Permission::check('view my contact');
    $editMyContact = CRM_Core_Permission::check('edit my contact');
    $addContact = CRM_Core_Permission::check('add contacts');
    $deleteContact = CRM_Core_Permission::check('delete contacts');
    $viewAllNotes = CRM_Core_Permission::check('view all notes');
    $addContactNotes = CRM_Core_Permission::check('add contact notes');
    $deleteActivities = CRM_Core_Permission::check('delete activities');
    $viewAllActivities = CRM_Core_Permission::check('view all activities');
    $editAllCase = CRM_Core_Permission::check('access all cases and activities');
    $editMyCase = CRM_Core_Permission::check('access my cases and activities');
    $accessCiviEvent = CRM_Core_Permission::check('access CiviEvent');
    $viewAllEvent = CRM_Core_Permission::check('view event info');
    $editAllEvents = CRM_Core_Permission::check('edit all events');
    $editEventParticipants = CRM_Core_Permission::check('edit event participants');
    $viewEventParticipants = CRM_Core_Permission::check('view event participants');
    $registerForEvents = CRM_Core_Permission::check('register for events');
	  $deleteInEvent = CRM_Core_Permission::check('delete in CiviEvent');
    $accessCiviMember = CRM_Core_Permission::check('access CiviMember');
    $editMemberships = CRM_Core_Permission::check('edit memberships');
    $deleteInCiviMember = CRM_Core_Permission::check('delete in CiviMember');
    $accessCiviContribute = CRM_Core_Permission::check('access CiviContribute');
    $editContributions = CRM_Core_Permission::check('edit contributions');
    $deleteInCiviContribute = CRM_Core_Permission::check('delete in CiviContribute');
    $accessToProfileListings = CRM_Core_Permission::check('profile listings and forms');
    $accessAllCustomData = CRM_Core_Permission::check('access all custom data');
    $canCheckInOnEvent = CRM_Core_Permission::check(CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT);

    $permissions['access'] = [
      'accessCiviCRM' => $accessToCiviCrm && $viewMyContact ? 1 : 0,
    ];

    $permissions['contact'] = [
      'view' => [
        'all' =>  $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      'create' => $accessToCiviCrm && $viewMyContact && $addContact ? 1 : 0,
      'delete' => [
		    'all' => $accessToCiviCrm && $viewMyContact && $deleteContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
		    'my' => $accessToCiviCrm && $viewMyContact && $deleteContact ? 1 : 0,
      ],
      'search' => $accessToCiviCrm && $viewMyContact && $viewAllContacts ? 1 : 0,
      'access_uploaded_files' => $accessUploadedFiles ? 1 : 0,
    ];

    $permissions['activity'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($editAllCase || $viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $viewAllActivities && ($editAllCase || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewAllActivities && $viewMyContact && ($editAllContacts || $editMyContact) ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $viewAllActivities && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $viewAllActivities && ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      'delete' => [
        'my' => $accessToCiviCrm && $viewMyContact && $deleteActivities ? 1 : 0,
      ],
    ];

    $permissions['case'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllCase && $editMyCase ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editMyCase ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllCase && $editMyCase && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editMyCase ? 1 : 0,
      ],
      'activity' => [
        'view' => [
          'all' => $accessToCiviCrm && $viewMyContact && $editAllCase && $editMyCase ? 1 : 0,
          'my' => $accessToCiviCrm && $viewMyContact && $editMyCase ? 1 : 0,
        ],
        'edit' => [
          'all' => $accessToCiviCrm && $viewMyContact && $editAllCase && $editMyCase && ($viewAllContacts || $editAllContacts) ? 1 : 0,
          'my' => $accessToCiviCrm && $viewMyContact && $editMyCase ? 1 : 0,
        ],
      ],
      'role' => 0,
    ];

    $permissions['event'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $editAllEvents ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $editAllEvents ? 1 : 0,
      ],
      'register' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $registerForEvents && $viewAllEvent && $accessToProfileListings ? 1 : 0,
      'view_my_tickets' => CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForViewMyTickets() ? 1 : 0,
      'is_enough_permission_for_changing_participant_statuses' => CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForChangingParticipantStatuses() ? 1 : 0,
    ];

    $permissions['relationship'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && (($editMyContact && $viewAllContacts) || $editAllContacts) ? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editAllContacts || ($viewAllContacts && $editMyContact)) ? 1 : 0,
      ],
      'disable' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editAllContacts || ($viewAllContacts && $editMyContact)) ? 1 : 0,
      ],
    ];

    $permissions['participant'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $editEventParticipants && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $editEventParticipants && ($viewAllContacts || $editAllContacts) ? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $deleteInEvent && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $deleteInEvent ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $editEventParticipants && ($viewAllContacts || $editAllContacts) && $editAllEvents ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessCiviEvent && $viewAllEvent && $viewEventParticipants && $editEventParticipants && $editAllEvents ? 1 : 0,
      ],
      'register_for_events' => ($registerForEvents) ? 1 : 0,
    ];

    $permissions['membership'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviMember && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviMember ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviMember && $editMemberships && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviMember && $editMemberships ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviMember && $editMemberships && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviMember && $editMemberships ? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviMember && $deleteInCiviMember && $deleteInCiviContribute && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviMember && $deleteInCiviMember && $deleteInCiviContribute ? 1 : 0,
      ],
    ];

    $permissions['contribution'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviContribute && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviContribute ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $editContributions && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $editContributions ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $editContributions && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $editContributions ? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $deleteInCiviContribute && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact && $accessCiviContribute && $deleteInCiviContribute ? 1 : 0,
      ],
    ];

    $permissions['note'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' =>  $accessToCiviCrm && $viewMyContact ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      'create' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts) ? 1 : 0,
      ],
    ];

    $permissions['contact_group'] = [
      'delete' => CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForDeleteContactGroup() ? 1 : 0,
    ];

    $permissions['custom_fields'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) && $accessAllCustomData ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $accessAllCustomData ? 1 : 0,
      ],
      'edit' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts && $accessAllCustomData ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts) && $accessAllCustomData ? 1 : 0,
      ],
    ];

    $permissions['group'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact ? 1 : 0,
      ],
      'remove' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
      ],
      'rejoin' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
      ],
      'add_to_group' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts)? 1 : 0,
      ],
      'delete' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
      ],
    ];

    $permissions['tags'] = [
      'view' => [
        'all' => $accessToCiviCrm && $viewMyContact && ($viewAllContacts || $editAllContacts) ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact ? 1 : 0,
      ],
      'add' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts)? 1 : 0,
      ],
      'remove' => [
        'all' => $accessToCiviCrm && $viewMyContact && $editAllContacts ? 1 : 0,
        'my' => $accessToCiviCrm && $viewMyContact && ($editMyContact || $editAllContacts)? 1 : 0,
      ],
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
