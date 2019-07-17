# CiviMobileAPI

The **CiviMobileAPI** extension (`com.agiliway.civimobileapi`) is a CiviCRM component that allows to use a mobile application [CiviMobile](https://civimobile.org).

[CiviMobile](https://civimobile.org) is a native mobile application granting CiviCRM users immediate, secure and high-speed connection to CiviCRM, so that they leverage the combined benefits of the software and their smartphones.

## ![Screenshot](./img/civimobileapi.png)

[CiviMobile](https://civimobile.org) Features:

- **Graphical Calendar** - all the scheduled information is displayed in a graphical calendar on the dashboard on the mobile app;  
- **Contacts** - users can view the list of all contacts in the system, add new contacts and edit contact information. A variety of contact information options is available including several phone numbers, websites, social media profiles, etc.;     
- **Membership** – users can access and renew their membership 
- **Contributions** – access to the history of all contributions, aggregated total and average payments.   
- **Relationships** – users can create a relationship between oneself and other individuals or organizations and optionally set a start date and end date for the relationship
- **Events & Registration** - ability to filter all the available events by type, date or title, check the event description, register yourself or others for the chosen event, view past and future events, share the information about events, and view events’ locations on the map
- **Participants Management & Check-In** - ability to view and manage event participants and their statuses. Additionally, a user can use Check-In functionality to mark participants who have attended an event while an built-in QR scanner will make this process fast and reliable 
- **Navigation** - users can switch to map navigation to easily find a direction to the location (e.g. a branch office or an event location)
- **Cases** - grants immediate access to user’s cases details
- **Activities** - allows to access details of user’s activities, their priority statuses and information about other constituents engaged in them
- **Push-notifications** - notifications about the updates in the system are displayed on the dashboard of the mobile phone
- **Working Offline** - can work in the offline mode
- **Settings** – users can configurate a set of parameters customizing the view and work of the application
- **4 locales** - currently, supports 6 locales: English, German, French, Italian, Spanish and Dutch


More details about how to install CiviMobileAPI extension and start using CiviMobile can be found at official CiviMobile website [https://civimobile.org](https://civimobile.org).   

Also, our step-by-step videos will lead you through the process of installing the CiviMobileAPI on [Drupal](https://www.youtube.com/watch?v=jNVMLSfU1ug), [Joomla](https://www.youtube.com/watch?v=mli8HkxVu60) and [WordPress](https://www.youtube.com/watch?v=mDjHEglfVT4&t=4s).    

The latest CiviMobile application can be downloaded from [AppStore](https://itunes.apple.com/us/app/civimobile/id1404824793?mt=8) of [Google PlayMarket](https://play.google.com/store/apps/details?id=com.agiliway.civimobile), which should be accessed from the mobile devices of users.  


## Requirements

- CiviCRM v4.7.x+
- PHP v5.4+
- Drupal 7.x
- Joomla 3.x
- WordPress 4.8+

*WordPress API patch is required. Please install [CiviCRM API fix for Wordpress](https://github.com/mecachisenros/civicrm-wp-rest) or [contact us](mailto:civicrm@agiliway.com) for details.

## Installation (git/cli)

To install the extension on an existing CiviCRM site:

```
mkdir sites/all/modules/civicrm/ext
cd sites/default/files/civicrm/ext
git clone https://github.com/agiliway/com.agiliway.civimobileapi com.agiliway.civimobileapi
```

1. Install it within the CiviCRM Extensions tab of the administration panel:

- go to Administer -> System Settings -> Extensions
- Click on Install button for CiviMobileAPI extension

2. Clear the cache:

- Administer -> System Settings -> Cleanup Caches and Update Paths

3. Install CiviMobile app into your smartphone from App Store or Google Play Market
4. Open the app on your device and enter the same login information you use for a web version (username and password) and your CiviCRM website URL – a URL you enter to access your CiviCRM system
