<?php
/**
 * Setting Lexicon Entries for AjaxUpload
 *
 * @package ajaxupload
 * @subpackage lexicon
 */
$_lang['setting_ajaxupload.cache_expires'] = 'Expire Time';
$_lang['setting_ajaxupload.cache_expires_desc'] = 'Expire Time of the AjaxUpload cache (in hours)';
$_lang['setting_ajaxupload.debug'] = 'Debug';
$_lang['setting_ajaxupload.debug_desc'] = 'Log debug information in the MODX error log.';
$_lang['setting_ajaxupload.filename_restrict_chars'] = 'Filename character restriction method';
$_lang['setting_ajaxupload.filename_restrict_chars_desc'] = 'The method used to restrict characters used in the filename. "pattern" allows a RegEx pattern to be provided, "legal" allows any legal URL characters, "alpha" allows only letters of the alphabet, and "alphanumeric" allows only letters and numbers. If empty, the value from the equivalent MODX FURL system setting is inherited.';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern'] = 'Filename character restriction pattern';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern_desc'] = 'A valid RegEx for restricting characters in the filename. If empty, the value from the equivalent MODX FURL system setting is inherited.';
$_lang['setting_ajaxupload.filename_translit'] = 'Filename transliteration method';
$_lang['setting_ajaxupload.filename_translit_desc'] = 'Defaults to iconv_ascii, to eliminate potential issues with accented characters. If empty, the value from the equivalent MODX FURL system setting is inherited.';
$_lang['setting_ajaxupload.image_tpl'] = 'Image Template';
$_lang['setting_ajaxupload.image_tpl_desc'] = 'Name of a chunk that contains the HTML code for displaying the uploaded image.';
