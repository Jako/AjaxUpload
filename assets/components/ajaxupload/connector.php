<?php
/**
 * AjaxUpload connector
 *
 * @package ajaxupload
 * @subpackage connector
 *
 * @var modX $modx
 */

// Allow anonymous users for web/upload processor
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'web/upload') {
    define('MODX_REQP', false);
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
/** @var AjaxUpload $ajaxupload */
$ajaxupload = $modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/', [
    'core_path' => $corePath
]);

// Set HTTP_MODAUTH for web processors
if (defined('MODX_REQP') && MODX_REQP === false) {
    $_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));
}

$processorsPath = $ajaxupload->getOption('processorsPath');

// Handle request
$modx->request->handleRequest([
    'processors_path' => $processorsPath,
    'location' => ''
]);
