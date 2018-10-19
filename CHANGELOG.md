# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)

---

## [Unreleased]

### Added

- Hooks for Push Notifications
  - create activity
  - edit activity
  - delete activity
  - create a case
  - ...
- set check_permission false for ...
- BAO/DAO for Push Notification entity
- auto_install.sql and - auto_uninstall.sql
- WordPress support

#### API

- `CiviMobileAllowedRelationshipTypes` - Get get list of available relationship types based on the contact id
- `CiviMobileCaseRole` - Get get list of available case roles for case based on case type
- `CiviMobilePermission` - Return the list of different permissions
- `PushNotification` - Save push tockens
- `PushNotificationEventReminder` - This API get called when run schedule job "Notify all participants that event is going to start"

#### ApiWrapper

### Changed

- `Upgrader` - _install_ (now sql)
- `Auth` API now is working with WordPress

### Removed

-

---

## [1.1] - 2018-08-10

### Added

- _`upgrade_0001`_ - delete _Push Notification_ custom group

### Changed

- `Upgrader` - remove _install_, remove custom group on _uninstall_ and remove _enable_/_disable_

### Fixed

- Remove custom group from Contact form

### Removed

- XML install file for _Push Notification_ custom group.
- PushNotification helper class.
- PushNotification API

---

## [1.0] - 2018-07-25

### Added

- Custom group for push notification (using XML).
- The pop-up window with notification that you can now using CiviMobile app.
- New `civimobile_secret_validation` hook for custom app secret validation
- set check_permission false for ...

#### API

- `PushNotification` (_`create`_) - save push token for users.
- `MyEvent` (_`get`_) - return an events list where user is registered.
- `CiviMobileSystem` (_`get`_) - return system info (_cms_, _cms_version_, _ext_version_).
- `CiviMobileCalendar` (_`get`_) - return an events, cases and activities for calendar.
- `Auth` (_`create`_) - auth method to huck the CMS dependency.

#### ApiWrapper

- `Activity` (_`getSingle`_)
  - _`details`_ - replace the &nbsp; to simple space from the text
  - _`short_description`_ - first 200 symbols of _`details`_ attribute
  - _`source_record_type`_
  - _`source_record_id`_
  - _`source_record_title`_
- `Address` (_`get`_) - check_permission = false
- `Case` (_`getSingle`_).
  - _`details`_ - replace the &nbsp; to simple space from the text
  - _`short_description`_ - first 200 symbols of _`details`_ attribute
  - _`image_URL`_ - an avatar link for each contact
- `Contact` (_`getSingle`_)
  - _`current_employer_id`_ - contact_id of employer
- `Event` (_`getSingle`_)
  - _`url`_ - the website url of event

[unreleased]: https://github.com/agiliway/com.agiliway.civimobileapi/compare/v1.1...HEAD
[2.0]: https://github.com/agiliway/com.agiliway.civimobileapi/compare/v1.1...v2.0
[1.1]: https://github.com/agiliway/com.agiliway.civimobileapi/compare/v1.0...v1.1
[1.0]: https://github.com/agiliway/com.agiliway.civimobileapi/tree/v1.0
