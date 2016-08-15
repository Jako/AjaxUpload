## Display the Upload Button

To display the upload button, you have to insert the AjaxUpload snippet call somewhere in a Resource. If you want to 
use it with FormIt, you should place the snippet call in the FormIt form:

```
[[!AjaxUpload?
&uid=`image`
&allowedExtensions=`jpg,jpeg,png,gif`
&thumbX=`75`
&thumbY=`75`
]]
```

The AjaxUpload snippet could use the following properties:

Property | Description | Default
---------|-------------|--------
uid | Unique upload queue id [^1] | md5 of MODX 'site_url' setting and the current resource id
language | Snippet/Javascript language | -
allowedExtensions | Allowed file extensions for upload | jpg,jpeg,png,gif
maxFilesizeMb | Maximum size for one file to upload | 8
maxFiles | Maximum count of files to upload | 3
thumbX | Horizontal size of generated thumb | 100
thumbY | Vertical size of generated thumb | 100
addJquery | Add jQuery script at the end of the body | No
addJscript | Add the snippet javascript and the fileuploader script at the end of the body | Yes
addCss | Add the snippet css ad the end of the head | Yes

## FormIt Hooks

To set/retreive the uploaded images in the upload queue by FormIt, you have to use the FormIt hooks in the FormIt call:

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

The AjaxUpload2Formit and the Formit2AjaxUpload hook use the same properties:

Property | Description | Default
---------|-------------|--------
ajaxuploadUid | Unique upload queue id [^1] | md5 of MODX site_url setting and the current resource id
ajaxuploadFieldname | (required) FormIt field, the filenames/paths of the (already) uploaded files are saved in | jpg,jpeg,png,gif
ajaxuploadTarget | (required) Target path for the (already) uploaded files (relative to $modx->getOption['assetsPath']). The folder should exist or should be createable and it has to be writable for PHP. | -
ajaxuploadFieldformat | Format of the data saved in ajaxuploadFieldname | csv

## FormIt Attachments

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

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//piwik.partout.info/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 13]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//piwik.partout.info/piwik.php?idsite=13" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
