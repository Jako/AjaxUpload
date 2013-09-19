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
 * @subpackage build
 *
 * Properties for the AjaxUpload snippet.
 */
$properties = array(
	array(
		'name' => 'uid',
		'desc' => 'prop_ajaxupload.uid',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'language',
		'desc' => 'prop_ajaxupload.language',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'allowedExtensions',
		'desc' => 'prop_ajaxupload.allowedExtensions',
		'type' => 'textfield',
		'options' => '',
		'value' => 'jpg,jpeg,png,gif',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'maxFilesizeMb',
		'desc' => 'prop_ajaxupload.maxFilesizeMb',
		'type' => 'textfield',
		'options' => '',
		'value' => '8',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'maxFiles',
		'desc' => 'prop_ajaxupload.maxFiles',
		'type' => 'textfield',
		'options' => '',
		'value' => '3',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'thumbX',
		'desc' => 'prop_ajaxupload.thumbX',
		'type' => 'textfield',
		'options' => '',
		'value' => '100',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'thumbY',
		'desc' => 'prop_ajaxupload.thumbY',
		'type' => 'textfield',
		'options' => '',
		'value' => '100',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'addJquery',
		'desc' => 'prop_ajaxupload.addJquery',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => FALSE,
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'addJscript',
		'desc' => 'prop_ajaxupload.addJscript',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => TRUE,
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'addCss',
		'desc' => 'prop_ajaxupload.addCss',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => TRUE,
		'lexicon' => 'ajaxupload:properties',
	)
);

return $properties;