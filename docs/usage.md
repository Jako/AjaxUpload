## How it works

Ajaxupload creates an upload area for uploading multiple files in FormIt forms.
All uploaded files are given random file names to avoid hotlinking of uploaded
unpublished files.

With two FormIt hooks the upload queues can be pre-filled from FormIt field
value and saved into FormIt field value. With two other FormIt hooks the
uploaded files can be attached to the FormIt mails and deleted after the form
submit.

## Display the Upload Area

To display the upload area, you need to place the *AjaxUpload snippet* call
in a form handled by FormIt:

```html
[[!AjaxUpload?
&uid=`image`
&acceptedFileTypes=`image/jpeg,image/gif,image/png,image/webp`
]]
```

The AjaxUpload snippet uses the following properties:

| Property          | Description                                                                                                                | Default                                                    |
|-------------------|----------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------|
| acceptedFileTypes | Accepted file mime types for upload.                                                                                       | image/jpeg,image/gif,image/png,image/webp                  |
| addCss            | Add the CSS at the end of the head.                                                                                        | 1 (Yes)                                                    |
| addJscript        | Add the snippet javascript and the fileuploader script at the end of the body.                                             | 1 (Yes)                                                    |
| fieldformat       | The value of ajaxuploadFieldformat property used in the according FormIt snippet call.                                     | csv                                                        |
| maxFiles          | Maximum count of files to upload.                                                                                          | 3                                                          |
| maxFileSize       | Maximum size for one file to upload.                                                                                       | 8MB                                                        |
| placeholderPrefix | The value of placeholderPrefix property used in the according FormIt snippet call.                                         | fi.                                                        |
| scriptTpl         | Name of a chunk that contains the Javascript code for the upload section.                                                  | tplAjaxuploadScript                                        |
| showCredits       | Show the credits of pqina/FilePond javascript                                                                              | 1 (Yes)                                                    |
| targetMediasource | The value of ajaxuploadTargetMediasource property used in the according FormIt snippet call.                               | -                                                          |
| uid               | Comma separated list of unique upload queue ids.                                                                           | md5 of MODX 'site_url' setting and the current resource id |
| uploadSectionTpl  | Name of a chunk that contains the HTML code for the upload section.                                                        | tplAjaxuploadUploadSection                                 |
| value             | Comma separated list of files in the upload queue. Supercedes the value that is retrieved with the Formit2AjaxUpload hook. | -                                                          |

[^1]: The parameter uid has to be set different for each upload button on the site to separate multiple upload queues.

All text output of the snippet can be edited in the MODX lexicon in the namespace `ajaxupload`.

## Set and retrieve the upload queue

To set/retreive the uploaded files in the upload queue by FormIt, you have to
use the FormIt hooks in the *FormIt snippet* call:

```html
[[!FormIt?
...
&preHooks=`Formit2AjaxUpload`
&hooks=`AjaxUpload2Formit`
&ajaxuploadUid=`image`
&ajaxuploadTarget=`images/user/`
...
]]
```

The AjaxUpload2Formit and the Formit2AjaxUpload hooks use almost the same properties:

| Property                    | Description                                                                                                                                 | Default                                   |
|-----------------------------|---------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------|
| ajaxuploadCacheExpires      | Expire Time of the AjaxUpload cache (in hours)                                                                                              | System setting `ajaxupload.cache_expires` |
| ajaxuploadFieldformat       | Format of the data saved in `ajaxuploadUid`.                                                                                                | csv                                       |
| ajaxuploadTarget            | Target path for the uploaded files. The folder should exist or be created and must be writable for PHP.                                     | -                                         |
| ajaxuploadTargetMediasource | If not empty, The target path is prefixed with the media source base path. Otherwise the target path is prefixed with the MODX assets path. | -                                         |
| ajaxuploadUid               | Comma separated list of unique upload queue ids. Also the names of the fields used in the hooks.                                            | -                                         |

!!! caution "Caution"

     If the ajaxuploadTargetRelativePath property is used, the ajaxuploadTarget property has to contain an absolute path. i.e:
     ```
     &ajaxuploadTarget=`[[++base_path]]mycustompath/images`
     &ajaxuploadTargetRelativePath=`[[++base_path]]mycustompath`
     ```

The AjaxUpload2Formit hook uses the following additional properties:

| Property                   | Description                                               | Default |
|----------------------------|-----------------------------------------------------------|---------|
| ajaxuploadAllowOverwrite   | Allow overwrite of existing files with the same filename. | 1 (Yes) |
| ajaxuploadSanitizeFilename | Sanitize the filename of the uploaded file.               | 0 (No)  |

## Attach the uploaded files to a mail

If you want to attach the uploaded files to the email, you have to add the
AjaxUploadAttachments hook to the FormIt Call before the email hook:

```html
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadAttachments,email`
]]
```

The AjaxUploadAttachments hook uses the properties of the hooks above.

## Remove the uploaded files

If you want to remove the uploaded files i.e. after the mail is sent, you have to add the AjaxUploadRemove hook after the email hook:

```html
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadAttachments,email,AjaxUploadRemove`
]]
```

The AjaxUploadRemove hook uses the properties of the hooks above.

## Make the upload required

If you want to make the upload required, you have to add the AjaxUploadRequired
hook to the FormIt Call before the email hook:

```html
[[!FormIt?
...
&hooks=`AjaxUpload2Formit,AjaxUploadRequired,email`
]]
```

The AjaxUploadRequired hook uses additional properties:

| Property                  | Description                                        | Default                                   |
|---------------------------|----------------------------------------------------|-------------------------------------------|
| ajaxuploadRequiredMessage | The error message added, when no file is uploaded. | Lexicon entry `ajaxupload.uploadRequired` |

## System Settings

AjaxUpload uses the following system settings in the namespace `ajaxupload`:

| Key                      | Name        | Description                                    | Default |
|--------------------------|-------------|------------------------------------------------|---------|
| ajaxupload.cache_expires | Expire Time | Expire Time of the AjaxUpload cache (in hours) | 4       |
| ajaxupload.debug         | Debug       | Log debug information in the MODX error log.   | No      |
