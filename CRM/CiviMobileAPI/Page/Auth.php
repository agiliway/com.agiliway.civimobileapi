<?php

use CRM_CiviMobileAPI_Utils_Request as Request;
use CRM_CiviMobileAPI_Utils_CmsUser as CmsUser;
use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

/**
 * Provides authentication functionality for CiviMobile application
 */
class CRM_CiviMobileAPI_Page_Auth extends CRM_Core_Page
{
    /**
     * Number of attempts
     */
    const ATTEMPT = 3;

    /**
     * For how many minutes block the request
     */
    const BLOCK_MINUTES = 1;

    /**
     * Handles the request and prepares all contact information for response
     * @return null|void
     */
    public function run()
    {
        civimobileapi_secret_validation();
       
        $nullObject = CRM_Utils_Hook::$_nullObject;
        CRM_Utils_Hook::singleton()->commonInvoke(0, $nullObject, $nullObject, $nullObject, $nullObject, $nullObject, $nullObject, 'civimobile_auth_pre', '');

        if (!CmsUser::getInstance()->validateCMS())
            $this->sendErrorResponse(ts('Sorry, but CiviMobile are not supporting your system yet.'));

        if (!$this->validateAttempts())
            $this->sendErrorResponse(ts('You are blocked for a %1 min. Please try again later', [1 => self::BLOCK_MINUTES]));

        //$email = 'vadym';
        $email = Request::getInstance()->post('email', 'String');
        if (!$email)
            $this->sendErrorResponse(ts('Required field'), 'email');
        
        // $password = 'password';
        $password = Request::getInstance()->post('password', 'String');
        if (!$password)
            $this->sendErrorResponse(ts('Required field'), 'password');
            
        $cmsUserId = CmsUser::getInstance()->validateUser($email, $password);
        if ($cmsUserId === FALSE)
            $this->sendErrorResponse(ts('Wrong email or password'));

        $contact = $this->findContact($cmsUserId);
        if (!$contact)
            $this->sendErrorResponse(ts('There are no such contact in CiviCRM'));
        
        $api_key = $contact->api_key ? $contact->api_key : $this->setApiKey($contact->id);
        if (!$api_key)
            $this->sendErrorResponse(ts('Something went wrong, we can not create the API KEY'));

        $data['values'] = [
            'api_key' => $api_key,
            'key' => $this->getSiteKey(),
            'id' => $contact->id,
            'display_name' => $contact->display_name,
        ];

        CRM_Utils_Hook::singleton()->commonInvoke(4, $data, $email, $password, $contact->id, $nullObject, $nullObject, 'civimobile_auth_success', '');

        $this->sendSuccessResponse($data);
    }

    /**
     * Save the number of attempts and block the request
     * @return bool
     */
    private function validateAttempts()
    {
        //TODO: save the number of attempts and block the request
        return TRUE;
    }

    /**
     * Find contact in CiviCRM
     * @param $cmsUserId
     * @return CRM_Contact_BAO_Contact
     */
    private function findContact($cmsUserId)
    {
        $contact = new CRM_Contact_BAO_Contact();
        $contact->get('id', $this->findContactRelation($cmsUserId));
        return $contact;
    }

    /**
     * Find CiviCRM contact id within relation
     * @param $uid
     * @return CRM_Contact_BAO_Contact
     */
    private function findContactRelation($uid)
    {
        try {
            $ufMatch = civicrm_api3('UFMatch', 'get', [
                'uf_id' => $uid,
                'sequential' => 1,
            ]);
            $contact_id = $ufMatch ['values'][0]['contact_id'];
        } catch (Exception $e) {
            $contact_id = FALSE;
        }
        return $contact_id;
    }

    /**
     * Gets CiviCRM Site Key
     * @return string
     */
    private function getSiteKey()
    {
        return CIVICRM_SITE_KEY;
    }

    /**
     * Generates and seves new api key for user
     * @param $uid
     * @return bool|string
     */
    private function setApiKey($uid)
    {
        try {
            $bytes = openssl_random_pseudo_bytes(10);
            $api_key = bin2hex($bytes);
            civicrm_api3('Contact', 'create', array(
                'id' => $uid,
                'api_key' => $api_key,
            ));
        } catch (Exception $e) {
            $api_key = FALSE;
        }
        return $api_key;
    }

    /**
     * Prepares success JSON response
     * @param $data
     */
    private function sendSuccessResponse($data)
    {
        $data['is_error'] = 0;
        JsonResponse::sendResponse(200, $data);
    }

    /**
     * Prepares wrong JSON response
     * @param $message
     * @param null $field
     */
    private function sendErrorResponse($message, $field = NULL)
    {
        $data = [
            'is_error' => 1,
            'error_message' => $message
        ];
        if ($field)
            $data['error_field'] = $field;

        JsonResponse::sendResponse(404, $data);
    }
}
