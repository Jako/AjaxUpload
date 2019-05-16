## How it works

Ajaxupload generates an upload button for uploading multiple files with progress
counter in FormIt forms. Works well in FF3.6+, Safari4+, Chrome and falls back to
hidden iframe based upload in other browsers, providing good user experience
everywhere.

All uploaded files and generated thumbnails are given random filenames to avoid
hotlinking uploaded not published files. Automatic thumbnail generation for
uploaded jpeg, png and gif files. Other uploaded files will get a generic icon
the file extension.

The package contains FormIt hooks for prefilling the upload queue from a FormIt
field value and be save the upload queue into a FormIt field value after a form
submission. A third FormIt hook could attach the uploaded files to the FormIt
mails.

## Display the Upload Button

To display the upload button, you have to insert the *AjaxUpload snippet* call
somewhere in a Resource. If you want to use it with FormIt, you should place the
snippet call in the FormIt form:

```
[[!AjaxUpload?
&uid=`image`
&allowedExtensions=`jpg,jpeg,png,gif`
&thumbX=`75`
&thumbY=`75`
]]
```

The *AjaxUpload snippet* call could use the following properties:

Property | Description | Default
---------|-------------|--------
uid | Unique upload queue id [^1] | md5 of MODX 'site_url' setting and the current resource id
language | Snippet/Javascript language | -
allowedExtensions | Allowed file extensions for upload | jpg,jpeg,png,gif
maxFilesizeMb | Maximum size for one file to upload | 8
maxFiles | Maximum count of files to upload | 3
thumbX | Horizontal size of generated thumb | 100
thumbY | Vertical size of generated thumb | 100
addJquery | Add jQuery script at the end of the body | 0 (No)
addJscript | Add the snippet javascript and the fileuploader script at the end of the body | 1 (Yes)
addCss | Add the snippet css ad the end of the head | 1 (Yes)

If you want to change the text output in the upload section (i.e. the upload
button), you have to edit the MODX lexicon in the namespace `ajaxupload`.

## Set and retrieve the upload queue

To set/retreive the uploaded images in the upload queue by FormIt, you have to
use the FormIt hooks in the *FormIt snippet* call:

```
[[!FormIt?
...
&preHooks=`Formit2AjaxUpload`
&hooks=`AjaxUpload2Formit`
&ajaxuploadFieldname=`image`
&ajaxuploadTarget=`images/user/`
&ajaxuploadUid=`image`
...
]]
```

The AjaxUpload2Formit and the Formit2AjaxUpload hook use almost the same properties:

Property | Description | Default
---------|-------------|--------
ajaxuploadUid | Unique upload queue id [^1] | md5 of MODX site_url setting and the current resource id
ajaxuploadFieldname | (required) FormIt field, the filenames/paths of the (already) uploaded files are saved in | -
ajaxuploadTarget | (required) Target path for the (already) uploaded files (relative to $modx->getOption['assetsPath']). The folder should exist or should be createable and it has to be writable for PHP. | -
ajaxuploadFieldformat | Format of the data saved in ajaxuploadFieldname | csv

The AjaxUpload2Formit hook uses additional properties:

Property | Description | Default
---------|-------------|--------
ajaxuploadClearQueue | Clear the upload queue after a sucessful run of the hook | 0 (No)
ajaxuploadAllowOverwrite | Allow overwrite of existing files with the same filename | 0 (No)

## Attach the uploaded files to a mail

If you want to attach the uploaded files to the email, you have to add the
AjaxUploadAttachments hook to the FormIt Call before the email hook:

```
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadAttachments,email`
]]
```

The AjaxUploadAttachments hook uses the properties of the hooks above.

[^1]: The parameter uid should be set different for each upload button on the site to separate multiple upload queues.

## System Settings

AjaxUpload uses the following system settings in the namespace `ajaxupload`:

Key | Description | Default
----|-------------|--------
ajaxupload.cache_expires | Expire Time of the AjaxUpload cache (in hours) | 4
