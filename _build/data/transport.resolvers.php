<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage build
 *
 * Resolvers for the AjaxUpload package
 */
$resolvers = array();

/* create the resolvers array */
if (is_dir($sources['source_assets'])) {
    $resolvers[] = array(
        'type' => 'file',
        'resolver' => array(
            'source' => $sources['source_assets'],
            'target' => "return MODX_ASSETS_PATH . 'components/';")
    );
}
if (is_dir($sources['source_core'])) {
    $resolvers[] = array(
        'type' => 'file',
        'resolver' => array(
            'source' => $sources['source_core'],
            'target' => "return MODX_CORE_PATH . 'components/';")
    );
}
return $resolvers;
