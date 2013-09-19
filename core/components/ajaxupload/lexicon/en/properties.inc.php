<?php
/**
 * AjaxUpload
 *
 * Copyright 2013 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * AjaxUpload is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * AjaxUpload is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * AjaxUpload; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package ajaxupload
 * @subpackage classfile
 *
 * Properties English Lexicon Entries for AjaxUpload
 */
$_lang['prop_ajaxupload.uid'] = 'Unique upload queue id';
$_lang['prop_ajaxupload.language'] = 'Snippet/Javascript language';
$_lang['prop_ajaxupload.allowedExtensions'] = 'Allowed file extensions for upload';
$_lang['prop_ajaxupload.maxFilesizeMb'] = 'Maximum size for one file to upload';
$_lang['prop_ajaxupload.maxFiles'] = 'Maximum count of files to upload';
$_lang['prop_ajaxupload.thumbX'] = 'Horizontal size of generated thumb';
$_lang['prop_ajaxupload.thumbY'] = 'Vertical size of generated thumb';
$_lang['prop_ajaxupload.addJquery'] = 'Add jQuery script at the end of the body';
$_lang['prop_ajaxupload.addJscript'] = 'Add the snippet javascript and the fileuploader script at the end of the body';
$_lang['prop_ajaxupload.addCss'] = 'Add the snippet css ad the end of the head';

$_lang['prop_ajaxupload2formit.ajaxuploadUid'] = 'Unique upload queue id';
$_lang['prop_ajaxupload2formit.ajaxuploadFieldname'] = 'Formit field, the filenames/paths of the (already) uploaded files are saved in';
$_lang['prop_ajaxupload2formit.ajaxuploadTarget'] = 'Target path for the (already) uploaded files (relative to $modx->getOption[\'assetsPath\'])';
$_lang['prop_ajaxupload2formit.ajaxuploadFieldformat'] = 'Format of the data saved in \'ajaxuploadFieldname\'';

$_lang['prop_formit2ajaxupload.ajaxuploadUid'] = $_lang['prop_ajaxupload2formit.ajaxuploadUid'];
$_lang['prop_formit2ajaxupload.ajaxuploadFieldname'] = $_lang['prop_ajaxupload2formit.ajaxuploadFieldname'];
$_lang['prop_formit2ajaxupload.ajaxuploadTarget'] = $_lang['prop_ajaxupload2formit.ajaxuploadTarget'];
$_lang['prop_formit2ajaxupload.ajaxuploadFieldformat'] = $_lang['prop_ajaxupload2formit.ajaxuploadFieldformat'];
