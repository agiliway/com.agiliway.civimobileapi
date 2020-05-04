<?php

class CRM_CiviMobileAPI_Page_PublicApi_Api extends CRM_CiviMobileAPI_Page_PublicApi_ApiBase {

  /**
   * Available api params
   *
   * @var array
   */
  public $apiSettings = [
    [
      'entityName' => 'Event',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info'],
          'availableParams' => [
            'id',
            'event_start_date',
            'event_type_id',
            'start_date',
            'event_end_date',
            'end_date',
            'is_monetary',
            'summary',
            'description',
            'event_description',
            'title',
            'return',
            'options'
          ],
          'availableReturnFields' => [
            'id',
            'title',
            'event_title',
            'summary',
            'description',
            'event_description',
            'event_type_id',
            'is_public',
            'start_date',
            'event_start_date',
            'end_date',
            'event_end_date',
            'is_online_registration',
            'event_full_text',
            'is_monetary',
            'is_show_location',
            'currency_symbol',
            'currency',
            'max_participants',
            'is_share',
            'registration_start_date',
            'registration_end_date',
            'is_map',
            'loc_block_id',
            'loc_block_id.email_id.email',
            'loc_block_id.email_2_id.email',
            'loc_block_id.phone_id.phone',
            'loc_block_id.phone_2_id.phone',
            'loc_block_id.phone_id.phone_type_id.label',
            'loc_block_id.phone_2_id.phone_type_id.label',
            'loc_block_id.address_id.country_id.name',
            'loc_block_id.address_id.state_province_id.name',
            'loc_block_id.address_id.city',
            'loc_block_id.address_id.street_address',
            'loc_block_id.address_id.postal_code',
            'loc_block_id.address_id.geo_code_1',
            'loc_block_id.address_id.geo_code_2',
            'url'
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'showOnlyActive',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'showOnlyPublicEvents',
            ]
          ],
          'transforms' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Transform',
              'method' => 'addEventUrl',
            ]
          ]
        ]
      ]
    ],
    [
      'entityName' => 'OptionValue',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info'],
          'availableParams' => [
            'option_group_id',
            'return',
            'options'
          ],
          'availableReturnFields' => [
            'id',
            'label',
            'value',
            'name',
            'weight',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'showOnlyActive',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'validateOptionGroupName',
            ]
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'CiviMobileGetPriceSetByEvent',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info'],
          'availableParams' => [
            'event_id',
            'return',
            'options'
          ],
          'availableReturnFields' => [
            'id',
            'price_set_id',
            'label',
            'type',
            'is_required',
            'items',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'Participant',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info', 'view event participants'],
          'availableParams' => [
            'event_id',
            'return',
            'options'
          ],
          'availableReturnFields' => [
            'id',
            'contact_id',
            'event_id',
            'status_id',
            'role_id',
            'display_name',
            'contact_type',
            'image_URL',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isPublicEvent',
            ],
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'ParticipantStatusType',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info', 'view event participants'],
          'availableParams' => [
            'id',
            'return',
            'options'
          ],
          'availableReturnFields' => [
            'id',
            'name',
            'label',
            'weight',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'showOnlyActive',
            ],
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'CiviMobilePublicParticipant',
      'availableActions' => [
        [
          'actionName' => 'create',
          'actionPermissions' => ['view event info', 'register for events', 'profile create'],
          'availableParams' => [
            'last_name',
            'first_name',
            'event_id',
            'contact_email',
          ],
          'availableReturnFields' => [
            'id',
            'contact_id',
            'event_id',
            'status_id',
            'role_id',
            'participant_public_key',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'forbiddenForJoomla',
            ],
          ],
          'transforms' => []
        ],
        [
          'actionName' => 'get_ticket',
          'actionPermissions' => ['view event info'],
          'availableParams' => [
            'public_key',
          ],
          'availableReturnFields' => [
            'participant_contact_display_name',
            'participant_status_name',
            'participant_role_name',
            'participant_fee_amount',
            'participant_fee_amount_currency',
            'event_name',
            'event_start_date',
            'event_end_date',
            'qr_code_link',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'CiviMobileCmsRegistration',
      'availableActions' => [
        [
          'actionName' => 'create',
          'actionPermissions' => [],
          'availableParams' => [
            'password',
            'username',
            'email',
            'first_name',
            'last_name',
          ],
          'availableReturnFields' => [
            'message',
            'success_code',
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowCmsRegistration',
            ],
          ],
          'transforms' => []
        ]
      ]
    ],
    [
      'entityName' => 'CiviMobileParticipantPaymentLink',
      'availableActions' => [
        [
          'actionName' => 'get',
          'actionPermissions' => ['view event info', 'register for events'],
          'availableParams' => [
            'event_id',
            'contact_id',
            'price_set',
            'first_name',
            'last_name',
            'email',
          ],
          'availableReturnFields' => [
            'link',
            'participantPublicKey'
          ],
          'middleware' => [
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'isAllowPublicInfoApi',
            ],
            [
              'class' => 'CRM_CiviMobileAPI_Page_PublicApi_Middleware',
              'method' => 'forbiddenForJoomla',
            ],
          ],
          'transforms' => []
        ]
      ]
    ]
  ];

}
