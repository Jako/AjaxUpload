<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage snippet
 */
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
$ajaxuploadAssetsPath = $modx->getOption('ajaxupload.assets_path', null, $modx->getOption('assets_path') . 'components/ajaxupload/');
$ajaxuploadAssetsUrl = $modx->getOption('ajaxupload.assets_url', null, $modx->getOption('assets_url') . 'components/ajaxupload/');
$debug = $modx->getOption('debug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false));

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', true, true)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not load AjaxUpload class.');
	if ($debug) {
		return 'Could not load AjaxUpload class.';
	} else {
		return '';
	}
}

$scriptProperties['ajaxupload.core_path'] = $ajaxuploadCorePath;
$scriptProperties['ajaxupload.assets_path'] = $ajaxuploadAssetsPath;
$scriptProperties['ajaxupload.assets_url'] = $ajaxuploadAssetsUrl;
$ajaxUpload = new AjaxUpload($modx, $scriptProperties);
if (!$ajaxUpload->initialize($scriptProperties)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not initialize AjaxUpload class.');
	if ($debug) {
		return 'Could not load initialize AjaxUpload class.';
	} else {
		return '';
	}
}
return $ajaxUpload->output() . $ajaxUpload->debugOutput();
