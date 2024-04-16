<?php
/**
 * Setting Lexicon Entries for AjaxUpload
 *
 * @package ajaxupload
 * @subpackage lexicon
 */
$_lang['setting_ajaxupload.cache_expires'] = 'Ablaufzeit';
$_lang['setting_ajaxupload.cache_expires_desc'] = 'Ablaufzeit des AjaxUpload-Cache (in Stunden)';
$_lang['setting_ajaxupload.debug'] = 'Debug';
$_lang['setting_ajaxupload.debug_desc'] = 'Debug-Informationen im MODX Fehlerprotokoll ausgeben.';
$_lang['setting_ajaxupload.filename_restrict_chars'] = 'Methode zur Einschränkung der erlaubten Zeichen im Dateinamen';
$_lang['setting_ajaxupload.filename_restrict_chars_desc'] = 'Die Methode zur Einschränkung der im Dateinamen verwendeten Zeichen. "pattern" erlaubt die Angabe eines RegEx-Musters, "legal" erlaubt alle zulässigen URL-Zeichen, "alpha" erlaubt nur Buchstaben des Alphabets, und "alphanumeric" erlaubt nur Buchstaben und Zahlen. Wenn leer, wird der Wert aus der entsprechenden MODX FURL-Systemeinstellung übernommen.';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern'] = 'RegEx zur Einschränkung der erlaubten Zeichen im Dateinamen';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern_desc'] = 'Ein gültiges RegEx zur Einschränkung von Zeichen im Dateinamen. Wenn leer, wird der Wert aus der entsprechenden MODX FURL-Systemeinstellung übernommen.';
$_lang['setting_ajaxupload.filename_translit'] = 'Transliterations Methode für den Dateinamen';
$_lang['setting_ajaxupload.filename_translit_desc'] = 'Der Standardwert ist iconv_ascii, um mögliche Probleme mit Zeichen mit Akzent zu vermeiden. Falls leer, wird der Wert aus der entsprechenden MODX FURL-Systemeinstellung übernommen.';
$_lang['setting_ajaxupload.image_tpl'] = 'Bild Template';
$_lang['setting_ajaxupload.image_tpl_desc'] = 'Name eines Chunks, der den HTML-Code für die Anzeige des hochgeladenen Bildes enthält.';
