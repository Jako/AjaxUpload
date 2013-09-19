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
 * snippets for AjaxUpload package
 */
$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
	'id' => 1,
	'name' => 'AjaxUpload',
	'description' => 'Upload button for uploading multiple files with progress-bar.',
	'snippet' => getSnippetContent($sources['snippets'] . 'ajaxupload.snippet.php'),
		), '', TRUE, TRUE);
$properties = include $sources['properties'] . 'ajaxupload.properties.php';
$snippets[1]->setProperties($properties);
unset($properties);
$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
	'id' => 2,
	'name' => 'Formit2AjaxUpload',
	'description' => 'AjaxUpload Formit preHook. Prefill the upload queue from Formit field.',
	'snippet' => getSnippetContent($sources['snippets'] . 'formit2ajaxupload.snippet.php'),
		), '', TRUE, TRUE);
$properties = include $sources['properties'] . 'formit2ajaxupload.properties.php';
$snippets[2]->setProperties($properties);
unset($properties);
$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
	'id' => 3,
	'name' => 'AjaxUpload2Formit',
	'description' => 'AjaxUpload Formit hook. Save the upload queue into Formit field.',
	'snippet' => getSnippetContent($sources['snippets'] . 'ajaxupload2formit.snippet.php'),
		), '', TRUE, TRUE);
$properties = include $sources['properties'] . 'ajaxupload2formit.properties.php';
$snippets[3]->setProperties($properties);
unset($properties);

return $snippets;