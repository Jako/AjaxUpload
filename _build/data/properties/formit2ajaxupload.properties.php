<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
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