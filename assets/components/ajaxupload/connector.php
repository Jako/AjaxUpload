<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2015 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage connector
 *
 * AjaxUpload connector
 */
/* Allow anonymous users */
define('MODX_REQP', false);

include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
if (!defined('MODX_CORE_PATH')) {
    include_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config.core.php';
}
if (!defined('MODX_CORE_PATH')) {
    exit('config.core.php not found');
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$ajaxuploadCorePath = realpath($modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/')) . '/';

if (!$modx->loadClass('AjaxUpload', $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/ajaxupload/') . 'model/ajaxupload/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load AjaxUpload class.', '', 'AjaxUpload');
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
$path = $modx->getOption('processorsPath', null, $ajaxuploadCorePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));