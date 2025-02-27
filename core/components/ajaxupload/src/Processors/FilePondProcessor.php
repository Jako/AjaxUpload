<?php
/**
 * Abstract processor
 *
 * @package ajaxupload
 * @subpackage processors
 */

namespace TreehillStudio\AjaxUpload\Processors;

use xPDO;

/**
 * Class Processor
 */
abstract class FilePondProcessor extends Processor
{
    /**
     * @var string $uid
     */
    protected $uid;

    abstract public function process();

    public function initialize()
    {
        $this->uid = preg_replace('/[^a-z0-9]/', '', $this->getProperty('uid', '-'));

        if (!$this->ajaxupload->prepareFilePond()) {
            return $this->filePondFailure();
        }

        return parent::initialize();
    }

    /**
     * Return a success message from the processor.
     * @param string $msg
     * @param int $code
     * @return string|void
     */
    public function filePondSuccess(string $msg = '', int $code = 200)
    {
        header('Content-Type: text/plain');
        http_response_code($code);
        echo $msg;
        @session_write_close();
        exit;
    }

    /**
     * Return a failure message from the processor.
     * @param string $msg
     * @param int $code
     * @return string|void
     */
    public function filePondFailure(string $msg = '', int $code = 400)
    {
        header('Content-Type: text/plain');
        http_response_code($code);
        echo $msg;
        @session_write_close();
        exit;
    }
}
