# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.2] - 2025-03-22

### Added

- Fix form submission only works when a file is uploaded [#80]

## [2.0.1] - 2025-03-13

### Added

- Add ajaxuploadRequiredUid property for the AjaxUploadRequired hook - Thanks to [Travis Botello](https://github.com/travisbotello) 

## [2.0.0] - 2025-03-02

### Breaking

- Add and remove some snippet properties of the snippets and hooks (see https://jako.github.io/AjaxUpload/migration/)

### Added

- Support for multiple upload queues in the hooks of one form.

### Changed

- Switch from Valums-File-Uploader/file-uploader to pqina/filepond

## [1.6.6] - 2024-06-23

### Added

- Add AjaxUploadRemove hook
- Add an option to sanitize file names in the AjaxUpload2FormIt hook - Thanks to [Hugo Peek](https://github.com/hugopeek)

### Fixed

- Snippet name of the AjaxUploadRemove hook

## [1.6.5] - 2023-03-10

### Added

- Add scriptTpl and uploadSectionTpl property for AjaxUpload snippet
- Add ajaxupload.image_tpl system setting.

### Fixed

- Fix ajaxuploadAllowOverwrite property in AjaxUpload2FormIt hook
- Fix Default lexicon entry is not set in AjaxUploadRequired [#62]

## [1.6.4] - 2023-01-24

### Fixed

- Fix wrong content in snippet AjaxUploadRequired - Thanks to [halftrainedharry](https://github.com/halftrainedharry)

## [1.6.3] - 2022-09-21

### Fixed

- Fix processor not found message after deleting an uploaded image

## [1.6.2] - 2022-06-17

### Fixed

- Add AjaxUploadRequired hook

## [1.6.1] - 2022-01-11

### Fixed

- Fix a wrong initialized uid property

## [1.6.0] - 2022-01-02

### Changed

- Code refactoring
- Full MODX 3 compatibility
- Use chunks for templates

## [1.5.8] - 2021-05-28

### Changed

- Fix boolean default settings in the FormIt hooks
- Fix creating new filenames for existing files
- Debug options for AjaxUploadAttachments hook

## [1.5.7] - 2019-05-16

### Changed

- Class based upload processor
- Update russian lexicon
- Fix ajaxuploadAllowOverwrite option

## [1.5.6] - 2019-02-22

### Changed

- Fix uploading a file with the same name
- Compatibility with FormIt 4.2.x

## [1.5.5] - 2018-06-04

### Changed

- Restrict maxConnections to prevent upload queue issues

## [1.5.4] - 2018-05-17

### Changed

- The language setting of the snippet was not always regarded
- Bugfix for Drag and drop upload

## [1.5.3] - 2016-08-30

### Added

- ajaxuploadAllowOverwrite property for the AjaxUpload2Formit snippet

## [1.5.2] - 2016-08-25

### Added

- Target folder is created, if it does not exist
- ajaxuploadClearQueue property for the AjaxUpload2Formit snippet

### Changed

- Better solution for upload queue issues
- Bugfix for clearing the upload cache too early

## [1.5.1] - 2016-08-15

### Changed

- Bugfix for array_key_exists warning

## [1.5.0] - 2016-06-22

### Added

- Usage of maxConnections to prevent upload queue issues

## [1.4.2] - 2016-03-03

### Changed

- Improved check for not existing files in the upload queue
- Improved check for post back in Formit2AjaxUpload hook

## [1.4.1] - 2016-02-10

### Changed

- Bugfix for removing not existing images from the queue
- Changed snippet property from cache_expires to cacheExpires respectively ajaxuploadCacheExpires

## [1.4.0] - 2016-02-09

### Added

- cache_expires System Setting

## [1.3.0] - 2016-08-11

### Added

- AjaxUploadAttachments snippet

### Changed

- Improved error messages and error logging

## [1.2.0] - 2015-08-08

### Added

- Log file/folder copying/creating errors in the MODX error log
- The default upload queue uid is resource specific

### Changed

- Bugfix for deleteExisting method

## [1.1.1] - 2014-10-10

### Added

- Logging errors in MODX system log

### Changed

- Some snippet parameters have been set to default if the FormIt hooks were executed after the AjaxUpload snippet
- Fix for maxFilesizeMb parameter

## [1.1.0] - 2013-09-20

### Added

- Generic thumbnail generation

## [1.0.0] - 2013-09-01

### Added

- Initial release for MODX Revolution
