<?php
/**
 * Setting Lexicon Entries for AjaxUpload
 *
 * @package ajaxupload
 * @subpackage lexicon
 */
$_lang['setting_ajaxupload.cache_expires'] = 'Время истекло';
$_lang['setting_ajaxupload.cache_expires_desc'] = 'Истекает время кэша AjaxUpload (в часах)';
$_lang['setting_ajaxupload.debug'] = 'Отладка';
$_lang['setting_ajaxupload.debug_desc'] = 'Записывать отладочную информацию в лог ошибок MODX.';
$_lang['setting_ajaxupload.filename_restrict_chars'] = 'Метод ограничения символов имени файла';
$_lang['setting_ajaxupload.filename_restrict_chars_desc'] = 'Метод, используемый для ограничения символов, используемых в имени файла. "pattern" позволяет указать шаблон RegEx, "legal" позволяет использовать любые легальные символы URL, "alpha" позволяет использовать только буквы алфавита, а "alphanumeric" позволяет использовать только буквы и цифры. Если значение пустое, то наследуется значение из эквивалентного системного параметра MODX FURL.';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern'] = 'Шаблон ограничения символов имени файла';
$_lang['setting_ajaxupload.filename_restrict_chars_pattern_desc'] = 'Действительный RegEx для ограничения символов в имени файла. Если пусто, наследуется значение из эквивалентного системного параметра MODX FURL.';
$_lang['setting_ajaxupload.filename_translit'] = 'Метод транслитерации имен файлов';
$_lang['setting_ajaxupload.filename_translit_desc'] = 'По умолчанию имеет значение iconv_ascii, чтобы устранить потенциальные проблемы с акцентированными символами. Если значение пустое, то наследуется значение из эквивалентного системного параметра MODX FURL.';
$_lang['setting_ajaxupload.image_tpl'] = 'Шаблон изображения';
$_lang['setting_ajaxupload.image_tpl_desc'] = 'Имя фрагмента, содержащего HTML-код для отображения загруженного изображения.';
