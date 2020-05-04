<?php

/**
 * Gives you the ability to work with CMS
 */
class CRM_CiviMobileAPI_Utils_Cms {

  /**
   * Returns site`s name in different CMS`
   *
   * @return string|null
   */
  public static function getSiteName() {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS ) {
      return get_bloginfo('name');
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      return JFactory::getConfig()->get('sitename');
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6 || $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
      return variable_get('site_name', '');
    }

    return null;
  }

  /**
   * Returns default rss url
   *
   * @return string
   */
  public static function getCmsRssUrl() {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS && function_exists('get_feed_link')) {
      return get_feed_link('rss2');
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      return str_replace("/administrator/", "", CIVICRM_UF_BASEURL) . "/?format=feed&type=rss";
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6 || $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
      return CIVICRM_UF_BASEURL . "/rss.xml";
    }

    return '';
  }

}
