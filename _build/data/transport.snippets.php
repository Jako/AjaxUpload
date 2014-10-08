<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage build
 *
 * Snippets for AjaxUpload package
 */
$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'AjaxUpload',
    'description' => 'Upload button for uploading multiple files with progress-bar.',
    'snippet' => getSnippetContent($sources['snippets'] . 'ajaxupload.snippet.php'),
), '', true, true);
$properties = include $sources['properties'] . 'ajaxupload.properties.php';
$snippets[1]->setProperties($properties);
unset($properties);
$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'Formit2AjaxUpload',
    'description' => 'AjaxUpload Formit preHook. Prefill the upload queue from Formit field.',
    'snippet' => getSnippetContent($sources['snippets'] . 'formit2ajaxupload.snippet.php'),
), '', true, true);
$properties = include $sources['properties'] . 'formit2ajaxupload.properties.php';
$snippets[2]->setProperties($properties);
unset($properties);
$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'AjaxUpload2Formit',
    'description' => 'AjaxUpload Formit hook. Save the upload queue into Formit field.',
    'snippet' => getSnippetContent($sources['snippets'] . 'ajaxupload2formit.snippet.php'),
), '', true, true);
$properties = include $sources['properties'] . 'ajaxupload2formit.properties.php';
$snippets[3]->setProperties($properties);
unset($properties);

return $snippets;