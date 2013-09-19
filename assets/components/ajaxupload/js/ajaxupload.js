// AjaxUpload Plugin
// Author: Thomas Jakobi <thomas.jakobi@partout.info>
;
(function($, window, document, undefined) {

	var pluginName = 'ajaxUpload',
			defaults = {
		debug: false,
		uploadAction: '',
		uid: '',
		dragText: 'Drop files here to upload',
		uploadButtonText: 'Upload a file',
		cancelButtonText: 'Cancel',
		failUploadText: 'Upload failed',
		deleteText: '{$_lang.deleteButton}',
		clearText: '{$_lang.clearButton}',
		hideShowDropArea: true,
		thumbX: '100px',
		thumbY: '100px',
		allowedExtensions: [],
		sizeLimit: 0,
		maxFiles: 3,
		messages: {
			typeError: "{file} has invalid extension. Only {extensions} are allowed.",
			sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
			minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
			emptyError: "{file} is empty, please select files again without it.",
			onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
		}
	};

	// The actual Plugin constructor
	function Plugin(el, options) {
		// Extending options
		this.options = $.extend({}, defaults, options);

		// Private
		this._defaults = defaults;
		this._name = pluginName;
		this.$el = $(el);
		this.count = 0;

		this.init();
	}

	// Separate functionality from object creation
	Plugin.prototype = {
		init: function() {
			var _this = this;

			var uploadItems = $('.file-uploader-items', _this.$el);
			// count already uploaded files
			_this.count = uploadItems.children().length;

			var uploader = new qq.FileUploader({
				element: $('.file-uploader-buttons', _this.$el)[0],
				action: _this.options.uploadAction,
				params: {
					uid: _this.options.uid,
					action: 'web/upload'
				},
				dragText: _this.options.dragText,
				uploadButtonText: _this.options.uploadButtonText,
				cancelButtonText: _this.options.cancelButtonText,
				failUploadText: _this.options.failUploadText,
				hideShowDropArea: true,
				allowedExtensions: _this.options.allowedExtensions,
				sizeLimit: _this.options.sizeLimit,
				messages: _this.options.messages,
				onComplete: function(id, fileName, uploadAnswer) {
					var fileid = uploadAnswer.fileid;
					if (uploadAnswer.success) {
						var fileWrap = $('<div>').addClass('file-wrap').data('fileid', fileid);
						var deleteButton = $('<div>').addClass('delete-button').html(_this.options.deleteText).click(function() {
							_this.delete(fileWrap, fileid);
						});
						var input = $('<input>').attr({
							name: _this.options.uid + '_fileid[]',
							value: fileid,
							type: 'hidden'
						});
						var thumb = $('<img>').attr({
							src: uploadAnswer.filename
						}).css({
							width: _this.options.thumbX,
							height: _this.options.thumbY,
							position: 'relative'
						});

						uploadItems.append(fileWrap.append(input, thumb, deleteButton));
						$('.qq-upload-list li', _this.$el).eq(id).hide();
						if (_this.options.debug) {
							alert('ID:' + id + '\nResponse:' + JSON.stringify(uploadAnswer));
						}
					}

				}
			});
			// init delete buttons
			$('.file-uploader-items .file-wrap .delete-button', _this.$el).click(function() {
				var fileWrap = $(this).parent();
				var fileid = fileWrap.data('fileid');
				_this.delete(fileWrap, fileid);
			});
			// append clear button
			var clearButton = $('<div>').addClass('qq-clear-button').html(_this.options.clearText).click(function() {
				_this.clear();
			});
			if (_this.maxFiles > 1) {
				$('.qq-upload-button', _this.$el).after(clearButton);
			}
		},
		delete: function(el, fileid) {
			var _this = this;

			$.get(_this.options.uploadAction, {
				'delete': fileid,
				'uid': _this.options.uid,
				'action': 'web/upload'
			}, function(deleteAnswer) {
				if (deleteAnswer.success) {
					el.fadeOut(function() {
						$(this).remove();
					});
					if (_this.options.debug) {
						alert(JSON.stringify(deleteAnswer) + '\nFile ' + fileid + ' deleted.');
					}
				}
			}, 'json');
		},
		clear: function() {
			var _this = this;

			$.get(_this.options.uploadAction, {
				'delete': 'all',
				'uid': _this.options.uid,
				'action': 'web/upload'
			}, function(clearAnswer) {
				if (clearAnswer.success) {
					$('.file-uploader-items', _this.$el).empty();
					$('.qq-upload-list li', _this.$el).remove();
					if (_this.options.debug) {
						alert(JSON.stringify(clearAnswer) + '\nAll Items deleted.');
					}
				}
			}, 'json');
		}
	};

	// The actual plugin
	$.fn[pluginName] = function(options) {
		var args = arguments;
		if (options === undefined || typeof options === 'object') {
			return this.each(function() {
				if (!$.data(this, 'plugin_' + pluginName)) {
					$.data(this, 'plugin_' + pluginName, new Plugin(this, options));
				}
			});
		} else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
			var returns;
			this.each(function() {
				var instance = $.data(this, 'plugin_' + pluginName);
				if (instance instanceof Plugin && typeof instance[options] === 'function') {
					returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
				}
				if (options === 'destroy') {
					$.data(this, 'plugin_' + pluginName, null);
				}
			});
			return returns !== undefined ? returns : this;
		}
	};
}(jQuery, window, document));