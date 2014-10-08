<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage prehook
 */
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
$ajaxuploadAssetsPath = $modx->getOption('ajaxupload.assets_path', null, $modx->getOption('assets_path') . 'components/ajaxupload/');
$ajaxuploadAssetsUrl = $modx->getOption('ajaxupload.assets_url', null, $modx->getOption('assets_url') . 'components/ajaxupload/');

$ajaxuploadFieldname = $modx->getOption('ajaxuploadFieldname', $scriptProperties, '');
$ajaxuploadFieldformat = $modx->getOption('ajaxuploadFieldformat', $scriptProperties, 'csv');
$ajaxuploadTarget = $modx->getOption('ajaxuploadTarget', $scriptProperties, '');
$scriptProperties['debug'] = $modx->getOption('ajaxuploadDebug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false));
$scriptProperties['uid'] = $modx->getOption('ajaxuploadUid', $scriptProperties, '');

$debug = $scriptProperties['debug'];

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', true, true)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not load modPhpThumb class.');
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
if (!$ajaxUpload->initialize()) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not initialize AjaxUpload class.');
	if ($debug) {
		return 'Could not load initialize AjaxUpload class.';
	} else {
		return '';
	}
}

$success = true;
switch (true) {
	case (empty($ajaxuploadFieldname)) :
		$hook->addError($scriptProperties['uid'], 'Missing parameter ajaxuploadFieldname.');
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadFieldname.', '', 'AjaxUpload');
		$success = false;
		break;
	case (empty($ajaxuploadTarget)) :
		$hook->addError($scriptProperties['uid'], 'Missing parameter ajaxuploadTarget.');
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'AjaxUpload');
		$success = false;
		break;
	default :
		if (!isset($_POST)) {
			$ajaxuploadValue = $hook->getValue($ajaxuploadFieldname);
		} else {
			$ajaxuploadValue = $ajaxUpload->getValue($ajaxuploadFieldformat);
		}
		if ($ajaxuploadValue) {
			switch ($ajaxuploadFieldformat) {
				case 'json' :
					$ajaxuploadValue = json_decode($ajaxuploadValue);
					break;
				case 'csv':
				default :
					$ajaxuploadValue = explode(',', $ajaxuploadValue);
			}
			$ajaxUpload->retrieveUploads($ajaxuploadValue);
		}
		$success = true;
}
return $success;
?>