# CiviMobileAPI

The **CiviMobileAPI** extension (`com.agiliway.civimobileapi`) is a CiviCRM component that allows to use a mobile application [CiviMobile](https://civimobile.agiliway.com/).

[CiviMobile](https://civimobile.agiliway.com/) is a native mobile application granting CiviCRM users immediate, secure and high-speed connection to CiviCRM, so that they leverage the combined benefits of the software and their smartphones.

## ![Screenshot](./img/civimobileapi.png)

[CiviMobile](https://civimobile.agiliway.com/) Features:

- **Graphical Calendar** - displays all the schedule information in a graphical calendar on the dashboard
- **Profiles** - provides access to the profiles of individual members and organisational branches on the go
- **Search** - allows to find the right contact in the CRM system and dial a person right away
- **Events & Registration** - allows to filter all the available events by type, date or title, register to the chosen event right away, view past and future events, share the information about events and view their locations on the map
- **Cases** - grants immediate access to user’s cases details
- **Activities** - allows to access details of user’s activities, their priority statuses and information about other constituents engaged in them
- **Working Offline** - can work in the offline mode
- **4 locales** - currently, supports 4 locales: English, German, French and Italian

The [CiviMobile](https://civimobile.agiliway.com/) application itself can be downloaded from [AppStore](https://itunes.apple.com/us/app/civimobile/id1404824793?mt=8) of [Google Play](https://play.google.com/store/apps/details?id=com.agiliway.civimobile), which should be accessed from the mobile devices of users.

More details about how to start with [CiviMobile](https://civimobile.agiliway.com/) can be found [here](https://civimobile.agiliway.com/#how-to-start).

## Requirements

- PHP v5.4+
- CiviCRM v4.7.x+

## Installation

>**Note**: it is only a part of instruction that describes a CiviCRM extension installation steps. Full instruction can be found [here](https://civimobile.agiliway.com/#how-to-start).

To install CiviMobileAPI extension you have to follow standard CiviCRM rules - [Installing a new extension](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension).
1. Download the extension package (zip or tar file) from the [download URL](https://github.com/agiliway/com.agiliway.civimobileapi/releases/latest) or use GIT for it ([Installation using GIT/CLI](#Installation_using_GITCLI_41)).
2. Unzip / untar the package and place it in your configured extensions directory. The default configurations are:
  * Drupal `/sites/default/files/civicrm/custom_ext`
  * WordPress `/wp-content/plugins/files/civicrm/custom_ext`
  * Joomla `/media/civicrm/custom_ext`
3. Go to Administer -> System Settings -> Extensions.
4. Click on Install button for **CiviMobileAPI** extension.
5. Clear the cache: Administer -> System Settings -> Cleanup Caches and Update Paths.
6. The WordPress requires additional plugin [CiviCRMApiFix](https://github.com/agiliway/com.agiliway.civicrmapifixforwordpress) 

## Installation using GIT/CLI

To install the extension on an existing CiviCRM site using GIT:
1. Go to extension folder.
2. Clone the extension
```
git clone https://github.com/agiliway/com.agiliway.civimobileapi com.agiliway.civimobileapi
```
