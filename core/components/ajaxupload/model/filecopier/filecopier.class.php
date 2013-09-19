<?php

/**
 * Handle existing files on server
 */
class qqCopyFile {

	private $file;

	function __construct($filepath = '') {
		if (file_exists($filepath)) {
			$pathinfo = pathinfo($filepath);
			$this->file = array(
				'fullpath' => $filepath,
				'name' => $pathinfo['basename'],
				'size' => filesize($filepath)
			);
		} else {
			$this->file = FALSE;
		}
	}

	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		if (!copy($this->file['fullpath'], $path)) {
			return false;
		}
		return true;
	}

	function getName() {
		return $this->file['name'];
	}

	function getSize() {
		return $this->file['size'];
	}

}

class qqFileCopier {

	public $filename;
	public $extension;
	public $path;
	private $allowedExtensions = array();
	private $sizeLimit = 0;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 0, $filepath = '') {
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;

		if ($filepath) {
			$this->file = new qqCopyFile($filepath);
		} else {
			$this->file = false;
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
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory, $replaceOldFile = FALSE, $messages = array()) {
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
		$ext = $pathinfo['extension'];

		if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => sprintf($messages['wrongExtension'], $these));
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
