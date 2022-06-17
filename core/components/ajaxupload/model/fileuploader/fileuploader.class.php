<?php

/****************************************
Example of how to use this uploader class...
You can uncomment the following lines (minus the require) to use these as your defaults.

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;

require('valums-file-uploader/server/php.php');
$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload('uploads/');

// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

/******************************************/



/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {

    /**
     * Save the file to the specified path
     * @param $path
     * @throws Exception
     * @return boolean true on success
     */
	function save($path) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {
			return false;
		}

		$target = fopen($path, "w");
		fseek($temp, 0);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}

	function getName() {
		return $_GET['qqfile'];
	}

	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])) {
			return (int) $_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {

    /**
     * Save the file to the specified path
     * @param $path
     * @return boolean true on success
     */
	function save($path) {
		if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
			return false;
		}
		return true;
	}

	function getName() {
		return $_FILES['qqfile']['name'];
	}

	function getSize() {
		return $_FILES['qqfile']['size'];
	}

}

class qqFileUploader {

	public $filename;
	public $extension;
	public $path;
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;

		$this->checkServerSettings();

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}
	}

	public function getName(){
		return ($this->file) ? $this->file->getName() : false;
	}

	private function checkServerSettings() {
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
			$maxLimit = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			$minLimit = min($postSize, $uploadSize) / 1024 / 1024 . 'M';
            die (json_encode(array('error' => 'Please increase post_max_size and upload_max_filesize to ' . $maxLimit . ' or restrict the sizeLimit Parameter of AjaxUpload to ' . $minLimit)));
		}
	}

	private function toBytes($str) {
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last) {
			case 'g' :
				$val *= 1024;
			case 'm' :
				$val *= 1024;
			case 'k' :
				$val *= 1024;
		}
		return $val;
	}

    /**
     * Handle upload
     *
     * @param $uploadDirectory
     * @param bool $replaceOldFile
     * @param array $messages
     * @return array array('success'=>true) or array('error'=>'error message')
     * @throws Exception
     * @throws Exception
     */
	function handleUpload($uploadDirectory, $replaceOldFile = false, $messages = array()) {
		if (!is_writable($uploadDirectory)) {
			return array('error' => $messages['notWritable']);
		}

		if (!$this->file) {
			return array('error' => $messages['noFile']);
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => $messages['emptyFile']);
		}

		if ($size > $this->sizeLimit) {
			return array('error' => $messages['largeFile']);
		}

		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		//$filename = md5(uniqid());
		$ext = @$pathinfo['extension'];		// hide notices if extension is empty

		if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => str_replace('[[+allowedExtensions]]', $these, $messages['wrongExtension']));
		}

		if (!$replaceOldFile) {
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
			$this->filename = $filename;
			$this->extension = $ext;
			$this->path = $uploadDirectory;
			return array('success' => true);
		} else {
			return array('error' => $messages['saveError']);
		}
	}

}
