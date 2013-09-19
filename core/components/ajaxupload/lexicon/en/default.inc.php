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
 * Default English Lexicon Entries for AjaxUpload
 */
$_lang['ajaxupload'] = 'AjaxUpload';
$_lang['ajaxupload.notWritable'] = "Server error. Upload directory isn't writable.";
$_lang['ajaxupload.noFile'] = "No files were uploaded.";
$_lang['ajaxupload.emptyFile'] = "File is empty.";
$_lang['ajaxupload.largeFile'] = "File is too large.";
$_lang['ajaxupload.wrongExtension'] = "File has an invalid extension, it should be one of [[+allowedExtensions]].";
$_lang['ajaxupload.saveError'] = "Could not save uploaded file. The upload was cancelled or a server error encountered.";
$_lang['ajaxupload.maxFiles'] = "Only [[+maxFiles]] different files are allowed to upload.";
$_lang['ajaxupload.notFound'] = "File is not found.";
$_lang['ajaxupload.thumbnailGenerationProblem'] = "Thumbnail generation failed.";
$_lang['ajaxupload.dropArea'] = "Drop files here to upload.";
$_lang['ajaxupload.uploadButton'] = "Upload";
$_lang['ajaxupload.deleteButton'] = "Delete";
$_lang['ajaxupload.clearButton'] = "Delete all";
$_lang['ajaxupload.cancel'] = "Cancel";
$_lang['ajaxupload.failed'] = "Failed";
$_lang['ajaxupload.typeError'] = "Unfortunately the file(s) you selected weren't the type we were expecting. Only {extensions} files are allowed.";
$_lang['ajaxupload.sizeError'] = "{file} is too large, maximum file size is {sizeLimit}.";
$_lang['ajaxupload.minSizeError'] = "{file} is too small, minimum file size is {minSizeLimit}.";
$_lang['ajaxupload.emptyError'] = "{file} is empty, please select files again without it.";
$_lang['ajaxupload.onLeave'] = "The files are being uploaded, if you leave now the upload will be cancelled.";
?>