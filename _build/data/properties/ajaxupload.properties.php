<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
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
        'value' => false,
        'lexicon' => 'ajaxupload:properties',
    ),
    array(
        'name' => 'addJscript',
        'desc' => 'prop_ajaxupload.addJscript',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'ajaxupload:properties',
    ),
    array(
        'name' => 'addCss',
        'desc' => 'prop_ajaxupload.addCss',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'ajaxupload:properties',
    )
);

return $properties;