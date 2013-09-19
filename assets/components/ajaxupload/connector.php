<?php
/**
 * AjaxUpload
 *
 * Copyright 2008-2012 by Thomas Jakobi <thomas.jakobi@partout.info>
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
 * @subpackage connector
 *
 * ajaxupload connector
 */
/* Allow anonymous users */
define('MODX_REQP', FALSE);

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
if (!$modx->loadClass('AjaxUpload', $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/ajaxupload/') . 'model/ajaxupload/', TRUE, TRUE)) {
	$modx->log(modX::LOG_LEVEL_ERROR, '[AjaxUpload] Could not load AjaxUpload class.');
	return '';
}
if ($_REQUEST['action'] == 'web/upload') {
	$version = $modx->getVersionData();
	if (version_compare($version['full_version'], '2.1.1-pl') >= 0) {
		if ($modx->user->hasSessionContext($modx->context->get('key'))) {
			$_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
		} else {
			$_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
			$_SERVER['HTTP_MODAUTH'] = 0;
		}
	} else {
		$_SERVER['HTTP_MODAUTH'] = $modx->site_id;
	}
	$_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

/* handle request */
$processorsPath = $modx->getOption('processorsPath', $modx->ajaxupload->config, $ajaxuploadCorePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $processorsPath,
	'location' => '',
));