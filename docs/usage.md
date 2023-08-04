## How it works

Ajaxupload creates an upload button for uploading multiple files with progress
counter in FormIt forms. Works well in FF3.6+, Safari4+, Chrome and falls back
to a hidden iframe-based upload in other browsers, providing a good user
experience everywhere.

All uploaded files and generated thumbnails are given random file names to avoid
hotlinking of uploaded unpublished files. Automatic thumbnail generation for
uploaded jpg, png and gif files. Other uploaded files get a generic file
extension icon.

The package includes FormIt hooks for pre-filling the upload queue from a FormIt
field and saving the upload queue to a FormIt field after a form submission. A
third FormIt hook can append the uploaded files to the FormIt mails.

## Display the Upload Button

To display the upload button, you need to place the *AjaxUpload snippet* call
somewhere in a Resource. If you want to use it with FormIt, you need to place
the snippet call in the FormIt form:

```
[[!AjaxUpload?
&uid=`image`
&allowedExtensions=`jpg,jpeg,png,gif`
&thumbX=`75`
&thumbY=`75`
]]
```

The AjaxUpload snippet uses the following properties:

| Property          | Description                                                                    | Default                                                    |
|-------------------|--------------------------------------------------------------------------------|------------------------------------------------------------|
| addCss            | Add the snippet css ad the end of the head.                                    | 1 (Yes)                                                    |
| addJquery         | Add jQuery script at the end of the body.                                      | 0 (No)                                                     |
| addJscript        | Add the snippet javascript and the fileuploader script at the end of the body. | 1 (Yes)                                                    |
| allowedExtensions | Allowed file extensions for upload.                                            | jpg,jpeg,png,gif                                           |
| language          | Snippet/Javascript language.                                                   | -                                                          |
| maxFiles          | Maximum count of files to upload.                                              | 3                                                          |
| maxFilesizeMb     | Maximum size for one file to upload.                                           | 8                                                          |
| thumbX            | Horizontal size of generated thumb.                                            | 100                                                        |
| thumbY            | Vertical size of generated thumb.                                              | 100                                                        |
| uid               | Unique upload queue id [^1].                                                   | md5 of MODX 'site_url' setting and the current resource id |
| uploadSectionTpl  | Name of a chunk that contains the HTML code for the upload section.            | tplAjaxuploadUploadSection                                 |

[^1]: The parameter uid has to be set different for each upload button on the site to separate multiple upload queues.

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

| Property              | Description                                                                                                | Default |
|-----------------------|------------------------------------------------------------------------------------------------------------|---------|
| ajaxuploadFieldformat | Format of the data saved in `ajaxuploadFieldname`.                                                         | csv     |
| ajaxuploadFieldname   | **(required)** Formit field, the filenames/paths of the (already) uploaded files are saved in.             | -       |
| ajaxuploadTarget      | **(required)** Target path for the (already) uploaded files (relative to $modx->getOption['assetsPath']).  | -       |
| ajaxuploadUid         | Unique upload queue id.                                                                                    | -       |

The folder in `ajaxuploadTarget` has to exist and it has to be writable for PHP
or it must be createable.

The AjaxUpload2Formit hook uses additional properties:

| Property                 | Description                                               | Default |
|--------------------------|-----------------------------------------------------------|---------|
| ajaxuploadAllowOverwrite | Allow overwrite of existing files with the same filename. | 1 (Yes) |
| ajaxuploadClearQueue     | Clear the upload queue after a sucessful run of the hook. | 0 (No)  |

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

## Remove the uploaded files

If you want to remove the uploaded files i.e. after the mail is sent, you have to add the AjaxUploadRemove hook after the email hook:

```
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadAttachments,email,AjaxUploadRemove`
]]
```

## Make the upload required

If you want to make the upload required, you have to add the AjaxUploadRequired
hook to the FormIt Call before the email hook:

```
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadRequired,email`
]]
```

The AjaxUploadRequired hook uses additional properties:

| Property                  | Description                                                                                                      | Default |
|---------------------------|------------------------------------------------------------------------------------------------------------------|---------|
| ajaxuploadRequiredMessage | The error message added, when no file is uploaded. It defaults to the lexicon entry `ajaxupload.uploadRequired`. | -       |

## System Settings

AjaxUpload uses the following system settings in the namespace `ajaxupload`:

| Key                      | Name           | Description                                                                    | Default            |
|--------------------------|----------------|--------------------------------------------------------------------------------|--------------------|
| ajaxupload.cache_expires | Expire Time    | Expire Time of the AjaxUpload cache (in hours)                                 | 4                  |
| ajaxupload.debug         | Debug          | Log debug information in the MODX error log.                                   | No                 |
| ajaxupload.image_tpl     | Image Template | Name of a chunk that contains the HTML code for displaying the uploaded image. | tplAjaxUploadImage |
