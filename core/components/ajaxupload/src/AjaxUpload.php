<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2025 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package ajaxupload
 * @subpackage classfile
 */

namespace TreehillStudio\AjaxUpload;

use modLexicon;
use modX;
use TreehillStudio\AjaxUpload\Helper\Parse;
use xPDO;

/**
 * class AjaxUpload
 */
class AjaxUpload
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public modX $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'ajaxupload';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'AjaxUpload';

    /**
     * The version
     * @var string $version
     */
    public $version = '2.0.0-b8';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * An array of debug information
     * @var array $debug
     */
    public $debug;

    /**
     * @var Parse $parse
     */
    public $parse = null;

    /**
     * AjaxUpload constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'cachePath' => $assetsPath . 'cache/',
        ], $options);

        $lexicon = $this->modx->getService('lexicon', modLexicon::class);
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('ajaxupload');

        // Add default options
        $resourceId = ($this->modx->resource) ? $this->modx->resource->get('id') : 0;
        $this->options = array_merge($this->options, [
            'debug' => $this->getBooleanOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
            'cacheExpires' => intval($this->getOption('cache_expires', $options, 4)),
            'uid' => preg_replace('/[^a-z0-9]/', '', $this->getOption('uid', $options, md5($this->modx->getOption('site_url') . '-' . $resourceId))),
            'newFilePermissions' => $this->modx->getOption('new_file_permissions', $options, '0644'),
            'newFolderPermissions' => $this->modx->getOption('new_folder_permissions', $options, '0755'),
        ]);
        $this->debug = [];

        if (!isset($_SESSION['ajaxupload'])) {
            $_SESSION['ajaxupload'] = [];
        }

        $this->parse = new Parse($this->modx);
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get Boolean Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return bool
     */
    public function getBooleanOption($key, $options = [], $default = null)
    {
        $option = $this->getOption($key, $options, $default);
        return ($option === 'true' || $option === true || $option === '1' || $option === 1);
    }

    /**
     * Load all class files and init defaults.
     *
     * @param array $properties properties to override the default config (if set)
     * @return boolean success state of initialization
     */
    public function initialize($properties = [])
    {
        // Override uid with properties;
        $this->options['uid'] = $this->getOption('uid', $properties, $this->options['uid']);

        if (!empty($properties)) {
            $acceptedFileTypes = $this->modx->getOption('acceptedFileTypes', $properties, 'image/jpeg,image/gif,image/png,image/webp');
            $acceptedFileTypes = (!is_array($acceptedFileTypes)) ? explode(',', $acceptedFileTypes) : $acceptedFileTypes;
            $this->options = array_merge($this->options, [
                'debug' => $this->getBooleanOption('debug', $properties, false),
                'cacheExpires' => intval($this->getOption('cacheExpires', $properties, 4)),
                'acceptedFileTypes' => $acceptedFileTypes,
                'maxFiles' => intval($this->modx->getOption('maxFiles', $properties, 3)),
                'maxFileSize' => $this->modx->getOption('maxFileSize', $properties, '8MB'),
                'addJscript' => $this->getBooleanOption('addJscript', $properties, true),
                'addCss' => $this->modx->getOption('addCss', $properties, true),
            ]);
        }
        if (!@is_dir($this->getOption('cachePath'))) {
            $this->createCachePath($this->getOption('cachePath'));
        }
        if ($this->getOption('uid')) {
            $this->createCachePath($this->getOption('cachePath') . 'tmp');
            $this->clearCachePath($this->getOption('cachePath') . 'tmp/', $this->getOption('cacheExpires'));
            $this->createCachePath($this->getOption('cachePath') . 'uploads');
            $this->clearCachePath($this->getOption('cachePath') . 'uploads/', $this->getOption('cacheExpires'));
            $this->createCachePath($this->getOption('cachePath') . 'variants');
            $this->clearCachePath($this->getOption('cachePath') . 'variants/', $this->getOption('cacheExpires'));
        }
        return true;
    }

    /**
     * @return bool
     */
    public function prepareFilePond()
    {
        // Prepare FilePond constants
        define('TRANSFER_DIR', $this->getOption('cachePath') . 'tmp');
        define('UPLOAD_DIR', $this->getOption('cachePath') . 'uploads');
        define('VARIANTS_DIR', $this->getOption('cachePath') . 'variants');
        define('METADATA_FILENAME', '.metadata');

        $success = $this->createCachePath($this->getOption('cachePath') . 'tmp');
        $success = $success && $this->createCachePath($this->getOption('cachePath') . 'uploads');
        $success = $success && $this->createCachePath($this->getOption('cachePath') . 'variants');
        if (!$success) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not create the cache path.', '', 'AjaxUpload');
        }
        return $success;
    }

    /**
     * @param string $path
     * @return string
     */
    public function relativeToAbsolutePath($path)
    {
        if (strpos($path, '/') === 0) {
            return $path;
        } elseif (preg_match('/\{(core|base|assets)_path}/', $path)) {
            return str_replace(array(
                '{core_path}',
                '{base_path}',
                '{assets_path}',
            ), array(
                $this->modx->getOption('core_path', null, MODX_CORE_PATH),
                $this->modx->getOption('base_path', null, MODX_BASE_PATH),
                $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH),
            ), $path);
        } else {
            return $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . $path;
        }
    }

    /**
     * Get the relative path between two paths
     *
     * @param string $from
     * @param string $to
     * @param string $separator
     * @return string
     */
    public function relativePath($from, $to, $separator = DIRECTORY_SEPARATOR)
    {
        $from = str_replace(array('/', '\\'), $separator, $from);
        $to = str_replace(array('/', '\\'), $separator, $to);

        $arFrom = explode($separator, rtrim($from, $separator));
        $arTo = explode($separator, rtrim($to, $separator));
        while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
            array_shift($arFrom);
            array_shift($arTo);
        }

        return str_pad('', count($arFrom) * 3, '..' . $separator) . implode($separator, $arTo);
    }

    /**
     * Recursive clear all files and folders in cache older than specified hours.
     *
     * @param string $path Specified hours
     * @param int $hours Specified hours
     * @return void
     */
    public function clearCachePath($path, $hours = 4)
    {
        $cache = opendir($path);
        if ($cache === false) {
            return;
        }
        $limit = time() - $hours * 60 * 60;
        while (($file = readdir($cache)) !== false) {
            // Skip current and parent directory files
            if ($file === "." || $file === "..") {
                continue;
            }
            // Recursive clear the folders inside
            if (is_dir($path . $file)) {
                $this->clearCachePath($path . $file . '/', $hours);
                if (count(glob($path . $file . '/*')) === 0) {
                    @rmdir($path . $file);
                }
            }
            // Clear files older than specified hours
            $filelastmodified = filemtime($path . $file);

            if (($filelastmodified < $limit) && is_file($path . $file)) {
                @unlink($path . $file);
            }
        }
        closedir($cache);
    }

    /**
     * @param string $path The created path
     * @return bool
     */
    public function createCachePath($path)
    {
        $cacheManager = $this->modx->getCacheManager();
        if (!$cacheManager->writeTree($path, ['new_folder_permissions' => $this->getOption('newFolderPermissions')])) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not create the cache path "' . $path . '".', '', 'AjaxUpload');
            return false;
        }
        return true;
    }
}
