# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- Add options 'all' and 'group' to occ expire-password command - [#77](https://github.com/owncloud/password_policy/issues/77)

### Changed
- More user-friendly message in first time login page - [#81](https://github.com/owncloud/password_policy/issues/81)

### Fixed
- Password history of user is now cleared when user is deleted - [#83](https://github.com/owncloud/password_policy/issues/83)
- Improve config messages and field grouping in settings - [#80](https://github.com/owncloud/password_policy/issues/80)
- Characters "<" and ">" can now be added in special chars rule - [#82](https://github.com/owncloud/password_policy/issues/82)
- Correct doc link and add notification features to description - [#75](https://github.com/owncloud/password_policy/issues/75)
- Fix off by one error where history would apply to one more history entry than configured - [#64](https://github.com/owncloud/password_policy/issues/64)
- Return a constant if the user cannot change their password - [#62](https://github.com/owncloud/password_policy/issues/62)
- Return a constant not a magic number if the user is null - [#60](https://github.com/owncloud/password_policy/issues/60)

## 2.0.0 - 2018-07-17
### Added
- Password history rule - [#10](https://github.com/owncloud/password_policy/pull/10) [#34](https://github.com/owncloud/password_policy/issues/34) 
- Password expiration - [#15](https://github.com/owncloud/password_policy/pull/15) [#27](https://github.com/owncloud/password_policy/issues/27) [#31](https://github.com/owncloud/password_policy/issues/31) [#51](https://github.com/owncloud/password_policy/issues/51) [#56](https://github.com/owncloud/password_policy/issues/56)

### Changed
- Relicensed as GPLv2 starting with 2.0.0

### Fixed
- Allow empty passwords for public links even when rules are set - [#36](https://github.com/owncloud/password_policy/issues/36)
- Remove wrong placeholder on special character input field - [#6](https://github.com/owncloud/password_policy/issues/6)
- Spelling errors - [#5](https://github.com/owncloud/password_policy/issues/5)

## 1.1.0
### Added
- Add compliant password generation event handler for other apps that need it - #109

## 1.0.3
### Changed
- Change description, add docs - #105
- Translate save button - #102
- Change headline and subheadlines - #92

[Unreleased]: https://github.com/owncloud/password_policy/compare/v2.0.0..HEAD

