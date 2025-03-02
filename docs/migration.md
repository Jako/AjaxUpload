## From 1.x to 2.x

With version 2.x the javascript handling of the file upload has been changed
from Valums-File-Uploader/file-uploader to pqina/filepond. Because of this
change, some snippet/hook properties are no longer needed and some snippet/hook
properties are added. Some system settings have also been changed.

### AjaxUpload snippet properties

The following snippet properties have to be removed or modified in the
AjaxUpload snippet call:

| Old property      | Change                                                                                                        |
|-------------------|---------------------------------------------------------------------------------------------------------------|
| addJquery         | Remove from snippet call.                                                                                     |
| allowedExtensions | The property has to be replaced with the `acceptedFileTypes` property. The property value has to be modified. |
| language          | Remove from snippet call.                                                                                     |
| maxFilesizeMb     | The property has to be replaced with the `maxFileSize` property. The property value has to be modified.       |
| thumbX            | Remove from snippet call.                                                                                     |
| thumbY            | Remove from snippet call.                                                                                     |

The following new properties are available and have to be added or modified in
the AjaxUpload snippet call:

| New property      | Change                                                                                                                                |
|-------------------|---------------------------------------------------------------------------------------------------------------------------------------|
| acceptedFileTypes | The property has to be replaced from the `allowedExtensions` property. The property value has to be modified.                         |
| fieldformat       | The property has to be filled with the value of the `ajaxuploadFieldformat` property used in the according FormIt snippet call.       |
| maxFileSize       | The property has to be replaced from the `maxFilesizeMb` property. The property value has to be modified.                             |
| placeholderPrefix | The property has to be filled with the value of the `placeholderPrefix` property used in the according FormIt snippet call.           |
| scriptTpl         | The property has to be added to the snippet call, if you don't want to use the default.                                               |
| showCredits       | The property has to be added to the snippet call, if you don't want to use the default.                                               |
| targetMediasource | The property has to be filled with the value of the `ajaxuploadTargetMediasource` property used in the according FormIt snippet call. |
| value             | The property has to be added to the snippet call, if you don't want to use the default.                                               |

### FormIt hook properties

The following FormIt hook properties have to be removed or modified in the
FormIt snippet call:

| Old property         | Change                    |
|----------------------|---------------------------|
| ajaxuploadClearQueue | Remove from snippet call. |
| ajaxuploadFieldname  | Remove from snippet call. |

The following new properties are available and have to be added or modified in
the FormIt snippet call:

| New property                | Change                                                                                  |
|-----------------------------|-----------------------------------------------------------------------------------------|
| ajaxuploadCacheExpires      | The property has to be added to the snippet call, if you don't want to use the default. |
| ajaxuploadSanitizeFilename  | The property has to be added to the snippet call, if you don't want to use the default. |
| ajaxuploadTargetMediasource | The property has to be added to the snippet call, if you don't want to use the default. |

### System settings

The following system settings are removed and not used anymore:

| Old Setting          | Change  |
|----------------------|---------|
| ajaxupload.image_tpl | Removed |
