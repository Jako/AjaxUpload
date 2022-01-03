<?php
/**
 * Abstract processor
 *
 * @package ajaxupload
 * @subpackage processors
 */

namespace TreehillStudio\AjaxUpload\Processors;

use modProcessor;
use modX;
use TreehillStudio\AjaxUpload\AjaxUpload;

/**
 * Class Processor
 */
abstract class Processor extends modProcessor
{
    public $languageTopics = ['ajaxupload:default'];

    /** @var AjaxUpload */
    public $ajaxupload;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    function __construct(modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('ajaxupload.core_path', null, $this->modx->getOption('core_path') . 'components/ajaxupload/');
        $this->ajaxupload = $this->modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/');
    }

    abstract public function process();
}
