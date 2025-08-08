<?php
/**
 * Fetch uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadFetchProcessor extends FilePondProcessor
{
    public function process()
    {
        // Stop here if no data supplied
        $url = $this->getProperty('url');
        if (empty($url)) {
            return $this->filePondFailure();
        }

        // Is this a valid url
        if (!FilePond::is_url($url)) {
            return $this->filePondFailure();
        };

        // Let's try to get the remote file content
        $file = FilePond::fetch($url);

        // Something went wrong
        if (!$file) {
            return $this->filePondFailure('', 500);
        }

        // Remote server returned invalid response
        if ($file['error'] !== 0) {
            return $this->filePondFailure($file['error'], 400);
        };

        // If we only return headers we store the file in the transfer folder
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            // deal with this file as if it's a file transfer, will return unique id to client
            $transfer = new Transfer();
            $transfer->restore($file);
            FilePond::store_transfer(TRANSFER_DIR, $transfer);

            // send transfer id back to client
            header('X-Content-Transfer-Id: ' . $transfer->getId());
        }

        // Return the file to the client
        FilePond::echo_file($file);
        @session_write_close();
        exit;
    }
}

return 'AjaxUploadFetchProcessor';
