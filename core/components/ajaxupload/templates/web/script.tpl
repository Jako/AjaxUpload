<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function() {
		$('#file-uploader-{$params.uid}').ajaxUpload({
			uploadAction: '{$params.uploadAction}',
			uid: '{$params.uid}',
			dragText: '{$_lang.dropArea}',
			uploadButtonText: '{$_lang.uploadButton}',
			cancelButtonText: '{$_lang.cancel}',
			failUploadText: '{$_lang.failed}',
			deleteText: '{$_lang.deleteButton}',
			clearText: '{$_lang.clearButton}',
			hideShowDropArea: true,
			thumbX: '{$params.thumbX}px',
			thumbY: '{$params.thumbY}px',
			allowedExtensions: [{$params.allowedExtensionsString}],
			sizeLimit: {$params.sizeLimit},
			maxFiles: {$params.maxFiles},
			messages: {
				typeError: "{$_lang.typeError}",
				sizeError: "{$_lang.sizeError}",
				minSizeError: "{$_lang.minSizeError}",
				emptyError: "{$_lang.emptyError}",
				onLeave: "{$_lang.onLeave}"
			}
		});
	});
	/* ]]> */
</script>