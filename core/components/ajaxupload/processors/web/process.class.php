<?php
/**
 * Process uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadProcessProcessor extends FilePondProcessor
{
    public function process()
    {
        // Check the post
        $post = FilePond::get_post($this->uid);
        if (!$post) {
            $this->filePondFailure();
        }

        $transfer = new Transfer();
        $transfer->populate($this->uid);
        $files = $transfer->getFiles();

        // Something went wrong, most likely a field name mismatch
        if ($files !== null && count($files) === 0) {
            $this->filePondFailure();
        }

        foreach ($files as $file) {
            if (!$this->isValidFileType($file)) {
                $this->filePondFailure();
            }
        }

        // Store data
        FilePond::store_transfer(TRANSFER_DIR, $transfer);

        // Return uploaded file server id
        $this->filePondSuccess($transfer->getId(), 201);
    }
}

return 'AjaxUploadProcessProcessor';
