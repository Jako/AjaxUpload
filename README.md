AjaxUpload
================================================================================

Display an upload button for uploading multiple files with progress counter. The
upload queue could be filled and saved by Formit hooks.

MODX Revolution Snippet and jQuery Script Wrapper for Andrew Valums great file
upload script (https://github.com/Valums-File-Uploader/file-uploader).

Features:
--------------------------------------------------------------------------------
With this snippet an upload button for uploading multiple files with
progress counter is generated. Works well in FF3.6+, Safari4+, Chrome and falls
back to hidden iframe based upload in other browsers, providing good user
experience everywhere.

All uploaded images and generated thumbnails are given random filenames to avoid
hotlinking uploaded not published fullsize images. Automatic thumbnail
generation for uploaded jpeg, png and gif files. Other uploaded files will get
a generic icon.

With two Formit hooks the upload queue could be pre filled from Formit field
value and saved into Formit field value.

Installation:
--------------------------------------------------------------------------------
MODX Package Management

Usage
--------------------------------------------------------------------------------

Insert in Formit form

```
[[!AjaxUpload?
&uid=`image`
&allowedExtensions=`jpg,jpeg,png,gif`
&thumbX=`75`
&thumbY=`75`
]]
```

and use the Formit hooks

```
[[!Formit?
...
&preHooks=`Formit2AjaxUpload`
&hooks=`AjaxUpload2Formit`
&ajaxuploadFieldname=`image`
&ajaxuploadTarget=`images/user/`
&ajaxuploadUid=`image`
]]
```

Parameters
--------------------------------------------------------------------------------

###For snippet call

Property | Description | Default
---- | ----------- | -------
uid | Unique upload queue id |  md5 of MODX 'site_url' setting
language | Snippet/Javascript language | -
allowedExtensions | Allowed file extensions for upload | jpg,jpeg,png,gif
maxFilesizeMb | Maximum size for one file to upload | 8
maxFiles | Maximum count of files to upload | 3
thumbX | Horizontal size of generated thumb | 100
thumbY | Vertical size of generated thumb | 100
addJquery | Add jQuery script at the end of the body | No
addJscript | Add the snippet javascript and the fileuploader script at the end of the body | Yes
addCss | Add the snippet css ad the end of the head | Yes

###Hook

Property | Description | Default
---- | ----------- | -------
ajaxuploadUid | Unique upload queue id |  md5 of MODX 'site_url' setting
ajaxuploadFieldname | Formit field, the filenames/paths of the (already) uploaded files are saved in | jpg,jpeg,png,gif
ajaxuploadTarget | Target path for the (already) uploaded files (relative to $modx->getOption['assetsPath']) | 8
ajaxuploadFieldformat | Format of the data saved in `ajaxuploadFieldname` | csv

Notes:
--------------------------------------------------------------------------------
1. The uploaded images will be saved with an unique filename.
2. The parameter `uid` should be set different for each upload button (separate multiple upload queues).
