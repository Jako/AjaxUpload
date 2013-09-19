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
 * Properties for the Formit2AjaxUpload snippet.
 */
$properties = array(
	array(
		'name' => 'ajaxuploadUid',
		'desc' => 'prop_formit2ajaxupload.ajaxuploadUid',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'ajaxuploadFieldname',
		'desc' => 'prop_formit2ajaxupload.ajaxuploadFieldname',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'ajaxuploadTarget',
		'desc' => 'prop_formit2ajaxupload.ajaxuploadTarget',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'ajaxupload:properties',
	),
	array(
		'name' => 'ajaxuploadFieldformat',
		'desc' => 'prop_formit2ajaxupload.ajaxuploadFieldformat',
		'type' => 'list',
		'options' => array(
			array('text' => 'CSV', 'value' => 'csv'),
			array('text' => 'JSON', 'value' => 'json')
		),
		'value' => 'csv',
		'lexicon' => 'ajaxupload:properties',
	)
);

return $properties;