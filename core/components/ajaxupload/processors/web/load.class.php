<?php
/**
 * Load uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadLoadProcessor extends FilePondProcessor
{
    public function process()
    {
        // Check the ref
        $ref = $this->getProperty('ref');
        if (empty($ref)) {
            return $this->filePondFailure();
        }

        // Set the file path
        $path = UPLOAD_DIR . DIRECTORY_SEPARATOR . FilePond::restrict_filename($ref);

        // Return file
        FilePond::echo_file($path);
        @session_write_close();
        exit;
    }
}

return 'AjaxUploadLoadProcessor';
