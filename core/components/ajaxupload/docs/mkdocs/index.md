# Intro

AjaxUpload is an extra package for MODx Revolution. It displays an upload button for uploading multiple files with 
progress counter. The upload queue could be filled and saved by Formit hooks. The uploaded files could be attached to 
the FormIt mails.

### Requirements

* MODX Revolution 2.2.0+
* PHP v5.3+

### Features

With the snippet an upload button for uploading multiple files with progress counter is generated. Works well 
in FF3.6+, Safari4+, Chrome and falls back to hidden iframe based upload in other browsers, providing good user
experience everywhere.

All uploaded files and generated thumbnails are given random filenames to avoid hotlinking uploaded not published 
files. Automatic thumbnail generation for uploaded jpeg, png and gif files. Other uploaded files will get a 
generic icon the file extension.

With two FormIt hooks the upload queue could be pre filled from a FormIt field value and be saved into a FormIt 
field value. With a third FormIt hook the uploaded files could be attached to the FormIt mails.

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//piwik.partout.info/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 14]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//piwik.partout.info/piwik.php?idsite=14" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
