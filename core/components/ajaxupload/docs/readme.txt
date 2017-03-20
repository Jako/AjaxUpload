AjaxUpload
==========

Display an upload button for uploading multiple files with progress counter. The
upload queue could be filled and saved by FormIt hooks.

MODX Revolution Snippet and jQuery Script Wrapper for Andrew Valums great file
upload script (https://github.com/Valums-File-Uploader/file-uploader).

Features
--------

With the snippet an upload button for uploading multiple files with
progress counter is generated. Works well in FF3.6+, Safari4+, Chrome and falls
back to hidden iframe based upload in other browsers, providing good user
experience everywhere.

All uploaded files and generated thumbnails are given random filenames to avoid
hotlinking uploaded not published files. Automatic thumbnail generation for
uploaded jpeg, png and gif files. Other uploaded files will get a generic icon
the file extension.

With two FormIt hooks the upload queue could be pre filled from FormIt field
value and saved into FormIt field value. With a third FormIt hook uploaded
files could be attached to the FormIt mails.

Installation
------------
MODX Package Management

Documentation
-------------
http://jako.github.io/AjaxUpload/

GitHub Repository
-----------------
https://github.com/Jako/AjaxUpload
