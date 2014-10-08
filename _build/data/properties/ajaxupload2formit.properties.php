<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage build
 *
 * Properties for the AjaxUpload2Formit snippet.
 */
$properties = array(
    array(
        'name' => 'ajaxuploadUid',
        'desc' => 'prop_ajaxupload2formit.ajaxuploadUid',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'ajaxupload:properties',
    ),
    array(
        'name' => 'ajaxuploadFieldname',
        'desc' => 'prop_ajaxupload2formit.ajaxuploadFieldname',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'ajaxupload:properties',
    ),
    array(
        'name' => 'ajaxuploadTarget',
        'desc' => 'prop_ajaxupload2formit.ajaxuploadTarget',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'ajaxupload:properties',
    ),
    array(
        'name' => 'ajaxuploadFieldformat',
        'desc' => 'prop_ajaxupload2formit.ajaxuploadFieldformat',
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