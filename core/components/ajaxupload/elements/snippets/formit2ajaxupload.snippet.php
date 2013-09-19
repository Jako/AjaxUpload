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
 * @subpackage prehook
 */
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', NULL, $modx->getOption('core_path') . 'components/ajaxupload/');

$ajaxuploadFieldname = $modx->getOption('ajaxuploadFieldname', $scriptProperties, '');
$ajaxuploadFieldformat = $modx->getOption('ajaxuploadFieldformat', $scriptProperties, 'csv');
$ajaxuploadTarget = $modx->getOption('ajaxuploadTarget', $scriptProperties, '');
$scriptProperties['debug'] = $modx->getOption('ajaxuploadDebug', $scriptProperties, $modx->getOption('ajaxupload.debug', NULL, FALSE));
$scriptProperties['uid'] = $modx->getOption('ajaxuploadUid', $scriptProperties, '');

$debug = $scriptProperties['debug'];

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', TRUE, TRUE)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not load modPhpThumb class.');
	if ($debug) {
		return 'Could not load AjaxUpload class.';
	} else {
		return '';
	}
}
$ajaxUpload = new AjaxUpload($modx, $scriptProperties);
if (!$ajaxUpload->initialize()) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not initialize AjaxUpload class.');
	if ($debug) {
		return 'Could not load initialize AjaxUpload class.';
	} else {
		return '';
	}
}

$success = TRUE;
switch (TRUE) {
	case (empty($ajaxuploadFieldname)) :
		$hook->addError('message', 'Missing parameter ajaxuploadTarget.');
		$success = FALSE;
		break;
	case (empty($ajaxuploadTarget)) :
		$hook->addError('message', 'Missing parameter ajaxuploadTarget.');
		$success = FALSE;
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
		$success = TRUE;
}
return $success;
?>