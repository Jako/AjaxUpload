<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2014 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage build
 *
 * AjaxUpload build script
 */
ob_start();

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME', 'AjaxUpload');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '1.1.1');
define('PKG_RELEASE', 'pl');

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'properties' => $root . '_build/data/properties/',
    'permissions' => $root . '_build/data/permissions/',
    'chunks' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'snippets' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'pages' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/pages/',
    'templates' => $root . 'core/components/' . PKG_NAME_LOWER . '/templates/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
);
unset($root);
$download = (isset($_GET['download']) && $_GET['download']) ? true : false;

$hasAssets = is_dir($sources['source_assets']); /* Transfer the files in the assets dir. */
$hasCore = is_dir($sources['source_core']); /* Transfer the files in the core dir. */

$hasContexts = file_exists($sources['data'] . 'transport.contexts.php');
$hasResources = file_exists($sources['data'] . 'transport.resources.php');
$hasValidators = file_exists($sources['data'] . 'transport.validators.php'); /* Run a validators before installing anything */
$hasResolvers = file_exists($sources['data'] . 'transport.resolvers.php');
$hasSetupOptions = file_exists($sources['data'] . 'transport.options.php'); /* HTML/PHP script to interact with user */
$hasMenu = file_exists($sources['data'] . 'transport.menus.php'); /* Add items to the MODx Top Menu */
$hasSettings = file_exists($sources['data'] . 'transport.settings.php'); /* Add new MODx System Settings */
$hasContextSettings = file_exists($sources['data'] . 'transport.contextsettings.php');

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx = new modX();
$modx->initialize('mgr');

echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);

$assetsPath = ($hasAssets) ? '{assets_path}components/' . PKG_NAME_LOWER . '/' : '';
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/', $assetsPath);

/* See what we have based on the files */
$hasSnippets = file_exists($sources['data'] . '/transport.snippets.php');
$hasChunks = file_exists($sources['data'] . '/transport.chunks.php');
$hasTemplates = file_exists($sources['data'] . '/transport.templates.php');
$hasTemplateVariables = file_exists($sources['data'] . '/transport.tvs.php');
$hasPlugins = file_exists($sources['data'] . '/transport.plugins.php');
$hasPropertySets = file_exists($sources['data'] . '/transport.propertysets.php');

/* create category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in category "' . PKG_NAME . '".');

/* add snippets */
if ($hasSnippets) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding snippets.');
    $snippets = include $sources['data'] . 'transport.snippets.php';
    if (is_array($snippets)) {
        if ($category->addMany($snippets, 'Snippets')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($snippets) . ' snippets.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in snippets failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No snippets defined in "transport.snippets.php".');
    }
    unset($snippets);
    if (!$download) flush();
}

/* add chunks */
if ($hasChunks) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding chunks.');
    $chunks = include $sources['data'] . 'transport.chunks.php';
    if (is_array($chunks)) {
        if ($category->addMany($chunks, 'Chunks')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($chunks) . ' chunks.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in chunks failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No chunks defined in "transport.chunks.php".');
    }
    unset($chunks);
    if (!$download) flush();
}

/* add templates  */
if ($hasTemplates) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding templates.');
    $templates = include $sources['data'] . '/transport.templates.php';
    if (is_array($templates)) {
        if ($category->addMany($templates, 'Templates')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($templates) . ' templates.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in templates failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No templates defined in "transport.templates.php".');
    }
    unset($templates);
    if (!$download) flush();
}

/* add template variables  */
if ($hasTemplateVariables) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding template variables.');
    $tvs = include $sources['data'] . '/transport.tvs.php';
    if (is_array($tvs)) {
        if ($category->addMany($tvs, 'TemplateVars')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($tvs) . ' template variables.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in template variables failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No template variables defined in "transport.tvs.php".');
    }
    unset($tvs);
    if (!$download) flush();
}

/* add plugins */
if ($hasPlugins) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding plugins.');
    $plugins = include $sources['data'] . 'transport.plugins.php';
    if (is_array($plugins)) {
        if ($category->addMany($plugins, 'Plugins')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' plugins.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in plugins failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No plugins defined in "transport.plugins.php".');
    }
    unset($plugins);
    if (!$download) flush();
}

/* add property sets */
if ($hasPropertySets) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding property sets.');
    $propertySets = include $sources['data'] . '/transport.propertysets.php';
    if (is_array($propertySets)) {
        if ($category->addMany($propertySets, 'PropertySets')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($propertySets) . ' property sets.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in property sets failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No property sets defined in "transport.propertysets.php".');
    }
    if (!$download) flush();
}

/* create category vehicle */

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true
);

if ($hasValidators) {
    $attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}

if ($hasSnippets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name'
    );
}

if ($hasChunks) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name'
    );
}

if ($hasPlugins) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
            'PluginEvents' => array(
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::UNIQUE_KEY => array('pluginid', 'event')
            )
        )
    );
}

if ($hasTemplates) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'templatename'
    );
}

if ($hasTemplateVariables) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name'
    );
}

if ($hasPropertySets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PropertySets'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name'
    );
}

$vehicle = $builder->createVehicle($category, $attr);
unset($category, $attr);

if ($hasValidators) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding validators ...');
    $validators = include $sources['data'] . 'transport.validators.php';
    if (!is_array($validators)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No validators defined.');
    } else {
        foreach ($validators as $key => $validator) {
            if (file_exists($validator['source'])) {
                $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . $key . ' validator.');
                $vehicle->validate('php', $validator);
            } else {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Could not find validator ' . $key . ' file.');
            }
        }
    }
    if (!$download) flush();
}

if ($hasResolvers) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding resolvers ...');
    $resolvers = include $sources['data'] . 'transport.resolvers.php';
    if (!is_array($resolvers)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No resolvers defined.');
    } else {
        foreach ($resolvers as $resolver) {
            $vehicle->resolve($resolver['type'], $resolver['resolver']);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($resolvers) . ' php/file resolvers.');
    }
    if (!$download) flush();
}

$builder->putVehicle($vehicle);
unset($vehicle, $resolvers, $resolver);

/* load contexts */
if ($hasContexts) {
    $contexts = include $sources['data'] . 'transport.contexts.php';
    if (!is_array($contexts)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Contexts defined.');
    } else {
        $attributes = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($contexts as $context) {
            $vehicle = $builder->createVehicle($context, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($contexts) . ' Contexts.');
        unset($contexts, $context, $attributes);
    }
    if (!$download) flush();
}

/* load resources */
if ($hasResources) {
    $resources = include $sources['data'] . 'transport.resources.php';
    if (!is_array($resources)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Resources defined.');
    } else {
        $attributes = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'pagetitle',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                'ContentType' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
            ),
        );
        foreach ($resources as $resource) {
            $vehicle = $builder->createVehicle($resource, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($resources) . ' Resources.');
        unset($resources, $resource, $attributes);
    }
    if (!$download) flush();
}

/* load system settings */
if ($hasSettings) {
    $settings = include $sources['data'] . 'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No System Settings defined.');
    } else {
        $attributes = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
        unset($settings, $setting, $attributes);
    }
    if (!$download) flush();
}

/* load context settings */
if ($hasContextSettings) {
    $settings = include $sources['data'] . 'transport.contextsettings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Context Settings defined.');
    } else {
        $attributes = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' Context Settings.');
        unset($settings, $setting, $attributes);
    }
    if (!$download) flush();
}

/* load menu */
if ($hasMenu) {
    $menus = include $sources['data'] . 'transport.menus.php';
    if (is_array($menus)) {
        $attributes = array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'text',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                'Action' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array('namespace', 'controller'),
                    xPDOTransport::RELATED_OBJECTS => true,
                    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                        'Children' => array(
                            xPDOTransport::PRESERVE_KEYS => false,
                            xPDOTransport::UPDATE_OBJECT => true,
                            xPDOTransport::UNIQUE_KEY => array('namespace', 'controller'),
                        ),
                    ),
                ),
            ),
        );
        foreach ($menus as $menu) {
            $vehicle = $builder->createVehicle($menu, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($menus) . ' Menus.');
    } else {
        $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in menus failed.');
    }
    unset($menus, $menu, $attributes);
    if (!$download) flush();
}

/* now pack in the license file, readme and changelog */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');
if (!$download) flush();

/* zip up package */
$built = $builder->pack();
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip ...');
if (!$download) flush();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

if ($built) {
    if (!$download) {
        $modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");
        ob_end_flush();
        exit();
    } else {
        ob_end_clean();

        $filename = $builder->filename;
        $directory = $builder->directory;

        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Length: ' . filesize($directory . $filename));
        readfile($directory . $filename);
    }
} else {
    $modx->log(modX::LOG_LEVEL_FATAL, "\n<br />Error: No Package Built.<br />\nExecution time: {$totalTime}\n");
    ob_end_flush();
}

exit();