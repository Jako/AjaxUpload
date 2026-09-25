<?php
/**
 * Abstract processor
 *
 * @package ajaxupload
 * @subpackage processors
 */

namespace TreehillStudio\AjaxUpload\Processors;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use xPDO;

/**
 * Class Processor
 */
abstract class FilePondProcessor extends Processor
{
    /**
     * @var string $uid
     */
    protected string $uid;

    /**
     * @var array|null
     */
    protected ?array $uidConfig;

    /**
     * @var FinfoMimeTypeDetector
     */
    protected FinfoMimeTypeDetector $finfoMimeTypeDetector;

    /**
     * {@inheritDoc}
     * @return mixed
     */
    abstract public function process();

    /**
     * @return bool
     */
    public function initialize(): bool
    {
        $uid = $this->getProperty('uid');
        if (empty($uid)) {
            $this->filePondFailure();
        }

        $this->uid = preg_replace($this->ajaxupload->getOption('uidExcludePattern'), '', $uid);
        if (!$this->ajaxupload->prepareFilePond()) {
            $this->filePondFailure();
        }

        $token = preg_replace('[^0-9a-f]', '', $this->getProperty('token'));
        $cacheKey = 'ajaxupload_' . $token;
        $cacheOptions = [
            xPDO::OPT_CACHE_KEY => $this->modx->getOption('cache_resource_key', null, 'resource'),
            xPDO::OPT_CACHE_HANDLER => $this->modx->getOption('cache_resource_handler', null, $this->modx->getOption(xPDO::OPT_CACHE_HANDLER)),
        ];
        $this->uidConfig = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
        if (!$this->uidConfig) {
            $this->filePondFailure();
        }
        if ($this->uidConfig['uid'] !== $this->uid) {
            $this->filePondFailure();
        }

        $this->finfoMimeTypeDetector = new FinfoMimeTypeDetector();

        return parent::initialize();
    }

    /**
     * Return a failure message from the processor.
     * @param string $msg
     * @param int $code
     * @return never-returns
     */
    public function filePondFailure(string $msg = '', int $code = 400)
    {
        header('Content-Type: text/plain');
        http_response_code($code);
        echo $msg;
        @session_write_close();
        exit();
    }

    /**
     * Return a success message from the processor.
     * @param string $msg
     * @param int $code
     * @return never-returns
     */
    public function filePondSuccess(string $msg = '', int $code = 200)
    {
        header('Content-Type: text/plain');
        http_response_code($code);
        echo $msg;
        @session_write_close();
        exit();
    }

    /**
     * Check wether the file type is valid
     *
     * @param $file
     * @return bool
     */
    protected function isValidFileType($file): bool
    {
        $mimeType = $this->finfoMimeTypeDetector->detectMimeTypeFromFile($file['tmp_name']);
        if (!$this->isValidMimeType($mimeType)) {
            return false;
        }
        if ($this->isDeniedExtension(pathinfo($file['name'], PATHINFO_EXTENSION))) {
            return false;
        }
        return true;
    }

    /**
     * Check whether the MIME type is valid
     *
     * @param $mimeType
     * @return bool
     */
    protected function isValidMimeType($mimeType): bool
    {
        if (empty($this->uidConfig['acceptedFileTypes'])) {
            return true;
        }
        foreach ($this->uidConfig['acceptedFileTypes'] as $type) {
            if (strpos($type, '/*') !== false) {
                $type = substr($mimeType, 0, strrpos($type, '/*') + 1);
                return (strpos($mimeType, $type) === 0);
            } elseif ($mimeType === $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether the file extension is denied
     *
     * @param $fileExtension
     * @return bool
     */
    protected function isDeniedExtension($fileExtension): bool
    {
        $fileExtension = strtolower($fileExtension);
        if (in_array($fileExtension, $this->ajaxupload->getOption('deniedExtensions'))) {
            return true;
        }
        return false;
    }
}
