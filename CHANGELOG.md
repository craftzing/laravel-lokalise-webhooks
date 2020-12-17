Changelog
===

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0-rc.2] - 2020-12-17

### Added
- As suggested in the [Lokalise docs](https://docs.lokalise.com/en/articles/3184756-webhooks), webhook requests are now
 restricted to known Lokalise IPs by default. This behaviour can be turned off using a new config value.

### Fixed
- `.github`, `.php_cs.dist`, `CHANGELOG.md` and `CONTRIBUTING.md` should no longer be included on export. 

## [1.0.0-rc.1] - 2020-12-16

First release candidate for the initial release.
