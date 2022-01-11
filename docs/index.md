# AjaxUpload

AjaxUpload is an extra package for MODx Revolution. It displays an upload button
for uploading multiple files with progress counter. The upload queue could be
filled and saved by Formit hooks. The uploaded files could be attached to the
FormIt mails.

### Requirements

* MODX Revolution 2.8+
* PHP 7.2+

### Features

* Generate an upload button for uploading multiple files with progress counter. 
* Works well in FF3.6+, Safari4+, Chrome and falls back to hidden iframe based upload in other browsers
* Uploaded files and generated thumbnails gets random filenames to avoid hotlinking uploaded not published files.
* Automatic thumbnail generation for uploaded jpeg, png and gif files.
* FormIt hooks to prefill the upload queue, read the upload queue after form submission and attach the uploaded files to FormIt mails.
