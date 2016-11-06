/**
 * ------------------------------------------------
 * Admin JS
 * ------------------------------------------------
 *
 * @package		shared
 * @createdate	Apr 19 15 21:07
 * @version		1.0.0
 * @author		ilker ozcan
 *
 */


// fix webkit autofill for add object form
$(document).ready(function(){

	$('input').each(function()
	{
		var obj		= $(this);
		setTimeout(function()
		{
			obj.removeAttr('readonly');
		}, 150);
	});
});

// confirm box for delete object
function deleteAction()
{
	return confirm('Are you sure to delete this object ?');
}

// rich text editor
function IORichText(inputId)
{
	var instance			= this;

	if(window.user_pref)
	{
		user_pref("capability.policy.policynames", "allowclipboard");
		user_pref("capability.policy.allowclipboard.Clipboard.paste", "allAccess");
	}

	$('#btnRichTextBold-' + inputId).click(function(){
		instance.bold();
	});
	$('#btnRichTextItalic-' + inputId).click(function(){
		instance.italic();
	});
	$('#btnRichTextUnderline-' + inputId).click(function(){
		instance.underline();
	});
	$('#btnRichTextHead-' + inputId).click(function(){
		instance.textHead();
	});
	$('#btnRichTextLeft-' + inputId).click(function(){
		instance.textLeft();
	});
	$('#btnRichTextCenter-' + inputId).click(function(){
		instance.textCenter();
	});
	$('#btnRichTextRight-' + inputId).click(function(){
		instance.textRight();
	});
	$('#btnRichTextOrderedList-' + inputId).click(function(){
		instance.orderedList();
	});
	$('#btnRichTextUnorderedList-' + inputId).click(function(){
		instance.unorderedList();
	});
	$('#btnRichTextCreateLink-' + inputId).click(function(){
		instance.createLink();
	});
	$('#btnRichTextAddPicture-' + inputId).click(function(){
		instance.addPicture();
	});
	$('#btnRichTextCut-' + inputId).click(function(){
		instance.cut();
	});
	$('#btnRichTextCopy-' + inputId).click(function(){
		instance.copy();
	});
	$('#btnRichTextPaste-' + inputId).click(function(){
		instance.paste();
	});
	$('#btnRichTextHtml-' + inputId).click(function(){
		instance.openHtmlModal();
	});
	$('#btnRichTextRemoveFormat-' + inputId).click(function(){
		instance.removeTextFormat();
	});
	$('#btnRichTextUndo-' + inputId).click(function(){
		instance.undo();
	});
	$('#btnRichTextRedo-' + inputId).click(function(){
		instance.redo();
	});

	this.editableDiv = document.getElementById('adminRichTextDiv-' + inputId);
	this.hiddenInput = $('#' + inputId);

	var modalHtml = '<div class="modal fade" id="richTextEditUserInsertDialog"><div class="modal-dialog"><div class="modal-content">';
	modalHtml += '<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	modalHtml += '<h4 class="modal-title" id="richTextEditInsertTitle"></h4>';
	modalHtml += '</div><div class="modal-body">';
	modalHtml += '<textarea rows="8" id="richTextEditUserInsertContent" class="form-control"></textarea>';
	modalHtml += '</div><div class="modal-footer"><button id="richTextEditUserInsertContentSave" type="button" class="btn btn-primary">Save changes</button>';
	modalHtml += '</div></div></div></div>';

	$('body').append(modalHtml);

	document.execCommand('styleWithCSS', false, 'false');
	document.execCommand('styleWithCSS', false, 'true');
	document.execCommand('insertbronreturn', false, 'false');
	document.execCommand('insertbronreturn', false, 'true');

	$('button[type="submit"]').click(function()
	{
		instance.hiddenInput.val(instance.editableDiv.innerHTML);
	});

	this.imageResizeStartX			= 0;
	this.imageResizeStartY			= 0;
	this.imageResizeStartWidth		= 0;
	this.imageResizeStartHeight		= 0;
}

IORichText.prototype			= {
	constructor					: IORichText,
	bold						: function()
	{
		document.execCommand('bold', false);
		this.editableDiv.focus();
	},
	italic						: function()
	{
		document.execCommand('italic', false);
		this.editableDiv.focus();
	},
	underline					: function()
	{
		document.execCommand('underline', false);
		this.editableDiv.focus();
	},
	textHead					: function()
	{
		document.execCommand('formatblock', false, 'H4');
		this.editableDiv.focus();
	},
	textLeft					: function()
	{
		document.execCommand('justifyleft', false);
		this.editableDiv.focus();
	},
	textCenter					: function()
	{
		document.execCommand('justifycenter', false);
		this.editableDiv.focus();
	},
	textRight					: function()
	{
		document.execCommand('justifyright', false);
		this.editableDiv.focus();
	},
	orderedList					: function()
	{
		document.execCommand('insertorderedlist', false);
		this.editableDiv.focus();
	},
	unorderedList				: function()
	{
		document.execCommand('insertunorderedlist', false);
		this.editableDiv.focus();
	},
	createLink					: function()
	{
		$('#richTextEditInsertTitle').html('Write the URL here');
		var instance			= this;

		var currentRange;
		if (window.getSelection)
		{
			var tmpSelection		= window.getSelection();
			if (tmpSelection.rangeCount)
			{
				currentRange		= tmpSelection.getRangeAt(0);
			}
		}

		var contentHtml				= this.editableDiv.innerHTML;
		var startOffset				= contentHtml.length;
		var endOffset				= contentHtml.length;

		if(currentRange)
		{
			startOffset				= currentRange.startOffset;
			endOffset				= currentRange.endOffset;
		}

		$('#richTextEditUserInsertContentSave').click(function()
		{
			$(this).unbind('click');

			$('#richTextEditUserInsertDialog').on('hidden.bs.modal', function (e) {
				$('#richTextEditUserInsertDialog').unbind('hidden.bs.modal');
				instance.editableDiv.focus();
				var node		= instance.editableDiv.firstChild;
				if(document.createRange)
				{
					var range		= document.createRange();
					range.setStart(node, startOffset);
					range.setEnd(node, endOffset);

					var selection	= window.getSelection();
					selection.removeAllRanges();
					selection.addRange(range);
				}

				document.execCommand('createlink', false, $('#richTextEditUserInsertContent').val());
				instance.editableDiv.focus();
			});

			$('#richTextEditUserInsertDialog').modal('hide');
		});

		$('#richTextEditUserInsertDialog').modal('show');
	},
	addPicture					: function()
	{
		$('#richTextEditInsertTitle').html('Write the image URL here');
		var instance			= this;

		$('#richTextEditUserInsertContentSave').click(function()
		{
			$(this).unbind('click');

			$('#richTextEditUserInsertDialog').on('hidden.bs.modal', function (e) {
				$('#richTextEditUserInsertDialog').unbind('hidden.bs.modal');

				var imageAreaNode			= document.createElement('div');
				imageAreaNode.className		= 'imageArea';
				instance.editableDiv.appendChild(imageAreaNode);
				var imageNode				= document.createElement('img');
				imageNode.src				= $('#richTextEditUserInsertContent').val();
				imageAreaNode.appendChild(imageNode);
				var imageEndNode			= document.createElement('div');
				imageEndNode.innerHTML		= ' &nbsp; <br><div></div> &nbsp; ';
				instance.editableDiv.appendChild(imageEndNode);
				instance.editableDiv.focus();

				var initDrag				= function(event)
				{
					instance.imageResizeStartX		= event.clientX;
					instance.imageResizeStartY		= event.clientY;
					instance.imageResizeStartWidth	= startWidth = parseInt(document.defaultView.getComputedStyle(imageAreaNode).width, 10);
					instance.imageResizeStartHeight = parseInt(document.defaultView.getComputedStyle(imageAreaNode).height, 10);

					var doDrag				= function(event)
					{
						imageAreaNode.style.width		= (instance.imageResizeStartWidth + event.clientX - instance.imageResizeStartX) + 'px';
						imageAreaNode.style.height		= (instance.imageResizeStartHeight + event.clientY - instance.imageResizeStartY) + 'px';
					};

					var stopDrag			= function(event)
					{
						window.removeEventListener('mousemove', doDrag, false);
						window.removeEventListener('mouseup', this, false);
					};

					window.addEventListener('mousemove', doDrag, false);
					window.addEventListener('mouseup', stopDrag, false);
				};

				imageAreaNode.addEventListener('mousedown', initDrag);
			});

			$('#richTextEditUserInsertDialog').modal('hide');
		});

		$('#richTextEditUserInsertDialog').modal('show');
	},
	cut							: function()
	{
		document.execCommand('cut', false);
		this.editableDiv.focus();
	},
	copy						: function()
	{
		document.execCommand('copy', false);
		this.editableDiv.focus();
	},
	paste						: function()
	{
		document.execCommand('paste', false);
		this.editableDiv.focus();
	},
	openHtmlModal				: function()
	{
		$('#richTextEditInsertTitle').html('HTML Code');
		var currentHtml			= this.editableDiv.innerHTML;
		var instance			= this;
		$('#richTextEditUserInsertContent').val(currentHtml);

		$('#richTextEditUserInsertContentSave').click(function()
		{
			$(this).unbind('click');

			$('#richTextEditUserInsertDialog').on('hidden.bs.modal', function (e) {
				$('#richTextEditUserInsertDialog').unbind('hidden.bs.modal');

				instance.editableDiv.innerHTML			= $('#richTextEditUserInsertContent').val();
				instance.editableDiv.focus();
			});

			$('#richTextEditUserInsertDialog').modal('hide');
		});

		$('#richTextEditUserInsertDialog').modal('show');
	},
	removeTextFormat			: function()
	{
		document.execCommand('removeFormat', false);
		this.editableDiv.focus();
	},
	undo						: function()
	{
		document.execCommand('undo', false);
		this.editableDiv.focus();
	},
	redo						: function()
	{
		document.execCommand('redo', false);
		this.editableDiv.focus();
	}
};

// ajax image uploader
function IOImageField(inputId, uploadPath, uploadRoute)
{
	this.inputId				= inputId;
	this.imageUploading			= false;
	this.uploadPath				= uploadPath;
	this.uploadRoute			= uploadRoute;
	var instance				= this;

	$('#fakeInput-' + inputId).change(function (event)
	{
		if(!instance.imageUploading)
			instance.imageFileStatus(event);
	});

	$('#uploadArea-'+inputId).click(function()
	{
		if(!instance.imageUploading)
			$('#fakeInput-'+inputId).click();
	});

	$('#uploadArea-'+inputId).bind('dragover', function (event)
	{
		$(this).css('border-style', 'dashed');
		$(this).css('border-width', '2px');
		event.stopPropagation();
		event.preventDefault();
	});

	$('#uploadArea-'+inputId).bind('dragleave', function (event)
	{
		$(this).css('border-style', 'solid');
		$(this).css('border-width', '1px');
		event.stopPropagation();
		event.preventDefault();
	});

	$('#uploadArea-'+inputId).bind('drop', function (evt)
	{
		$(this).css('border-style', 'solid');
		$(this).css('border-width', '1px');

		var dt			= event.dataTransfer;
		var files		= dt.files;
		var fileCount	= files.length;

		evt.stopPropagation();
		evt.preventDefault();

		if(!instance.imageUploading)
		{
			if(fileCount > 0)
			{
				var isImageFile		= false;

				switch(files[0].type.toLowerCase())
				{
					case 'image/jpeg':
						isImageFile		= true;
						break;
					case 'image/pjpeg':
						isImageFile		= true;
						break;
					case 'image/gif':
						isImageFile		= true;
						break;
					case 'image/png':
						isImageFile		= true;
						break;
					default:
						isImageFile		= false;
						break;
				}

				if(!isImageFile)
				{
					instance.setError('Error!', 'The file is not a valid image file.');
				}else{
					instance.uploadImage(files[0], files[0].type, files[0].name);
				}
			}
		}
	});

	this.setupOnBeforeUnload();
	this.setupModal();
}

IOImageField.prototype = {
	constructor					: IOImageField,
	imageFileStatus				: function(event)
	{
		var fileType;
		var fileName;
		var file;

		if(event.target != undefined)
		{
			fileType	= event.target.files[0].type;
			fileName	= event.target.files[0].name;
			file		= event.target.files[0];
		}else
		if(event.srcElement != undefined)
		{
			fileType	= event.srcElement.files[0].type;
			fileName	= event.srcElement.files[0].name;
			file		= event.srcElement.files[0];
		}

		if(fileType != undefined)
		{
			var isImageFile		= false;

			switch(fileType.toLowerCase())
			{
				case 'image/jpeg':
					isImageFile		= true;
					break;
				case 'image/pjpeg':
					isImageFile		= true;
					break;
				case 'image/gif':
					isImageFile		= true;
					break;
				case 'image/png':
					isImageFile		= true;
					break;
				default:
					isImageFile		= false;
					break;
			}

			if(!isImageFile)
			{
				this.setError('Error!', 'The file is not a valid image file.');
			}else{
				this.uploadImage(file, fileType, fileName);
			}
		}
	},
	uploadImage					: function(file, fileType, fileName)
	{
		this.imageUploading		= true;
		this.setProgressbar(10);
		$('#progress-' + this.inputId).show();

		var reader						= new FileReader();
		var instance					= this;
		var uploadUrl					= this.uploadRoute;

		reader.onloadend = function()
		{
			var fileData		= reader.result;

			$.ajax(
				{
					url: uploadUrl,
					cache: false,
					contentType: 'application/json; charset=UTF-8',
					data: JSON.stringify({
						"imageData"		: fileData,
						"imageType"		: fileType,
						"imageName"		: fileName,
						"fieldId"		: instance.inputId
					}),
					dataType: 'json',
					type: 'POST',
					xhr: function () {
						var xhr = new window.XMLHttpRequest();
						xhr.addEventListener("progress", function (evt) {

							if (evt.lengthComputable) {
								var percentComplete = Math.round(evt.loaded / evt.total * 100);
								instance.setProgressbar(percent);
							}
						}, false);
						return xhr;
					},
					success: function(response)
					{
						if(response.status)
						{
							instance.imageUploading			= false;
							instance.imageUploadComplete(response.data);
						}else{
							instance.imageUploading			= false;
							instance.setProgressbar(100);
							instance.setError('Error!', response.data);
						}
					},
					error: function()
					{
						instance.imageUploading			= false;
						instance.setProgressbar(100);
						instance.setError('Error!', 'An error has occured');
					}
				}
			);
		};

		reader.readAsDataURL(file);
	},
	imageUploadComplete			: function(fileName)
	{
		this.setProgressbar(100);
		this.imageUploading		= false;
		var imageUrl			= this.uploadPath + '/' + fileName;
		$('#uploadArea-'+this.inputId).html('<img src="' + imageUrl + '">');
		$('#'+this.inputId).val(fileName);
	},
	setupOnBeforeUnload			: function()
	{
		var ua							= window.navigator.userAgent;
		var msie						= ua.indexOf("MSIE ");
		var instance					= this;
		if(msie <= 0)
		{
			window.onbeforeunload	= function(event)
			{
				if(instance.imageUploading)
				{
					event.returnValue	= 'Uploading progress does not finished yet. Do you want to continue ?';
				}
			};
		}
	},
	setupModal					: function()
	{
		var modalHtml				= '<div class="modal fade" id="imageUploadAlertDialog"><div class="modal-dialog"><div class="modal-content">';
		modalHtml					+= '<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		modalHtml					+= '<h4 class="modal-title" id="imageUploadAlertDialogTitle"></h4>';
		modalHtml					+= '</div><div class="modal-body">';
		modalHtml					+= '<p id="imageUploadAlertDialogMessage"></p>';
		modalHtml					+= '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
		modalHtml					+= '</div></div></div></div>';

		if($('#imageUploadAlertDialog').length <= 0)
		{
			$('body').append(modalHtml);
		}
	},
	setError					: function(title, message)
	{
		$('#imageUploadAlertDialogTitle').html(title);
		$('#imageUploadAlertDialogMessage').html(message);
		$('#imageUploadAlertDialog').modal('show');
	},
	setProgressbar				: function(percent)
	{
		var progress			= $('#progress-' + this.inputId);
		var progressBar			= progress.children('.progress-bar');
		progressBar.attr('aria-valuenow', percent);
		progressBar.css('width', percent + '%');
		progressBar.html(percent + '%');

		if(percent == 100)
		{
			setTimeout(function()
			{
				progress.hide();
			}, 1000);
		}
	}
};

// ajax multiple image uploader
function IOMultipleImageField(inputId, uploadPath, uploadRoute, fullName) {

	this.inputId = inputId;
	this.imageUploading = false;
	this.uploadPath = uploadPath;
	this.uploadRoute = uploadRoute;
	this.fullName = fullName;
	this.currentLoopIdx = 0;
	var instance = this;

	$('#fakeInput-' + inputId).change(function (event) {

		if(!instance.imageUploading)
			instance.imageFileStatus(event);
	});

	var uploadArea = $('#uploadArea-' + inputId);
	this.currentLoopIdx = uploadArea.children().length;

	uploadArea.click(function() {

		if(!instance.imageUploading)
			$('#fakeInput-'+inputId).click();
	});

	uploadArea.bind('dragover', function (event) {

		$(this).css('border-style', 'dashed');
		$(this).css('border-width', '2px');
		event.stopPropagation();
		event.preventDefault();
	});

	uploadArea.bind('dragleave', function (event) {
		$(this).css('border-style', 'solid');
		$(this).css('border-width', '1px');
		event.stopPropagation();
		event.preventDefault();
	});

	uploadArea.bind('drop', function (evt) {
		$(this).css('border-style', 'solid');
		$(this).css('border-width', '1px');

		var dt			= event.dataTransfer;
		var files		= dt.files;
		var fileCount	= files.length;

		evt.stopPropagation();
		evt.preventDefault();

		if(!instance.imageUploading)
		{
			if(fileCount > 0)
			{
				var isImageFile		= false;

				switch(files[0].type.toLowerCase())
				{
					case 'image/jpeg':
						isImageFile		= true;
						break;
					case 'image/pjpeg':
						isImageFile		= true;
						break;
					case 'image/gif':
						isImageFile		= true;
						break;
					case 'image/png':
						isImageFile		= true;
						break;
					default:
						isImageFile		= false;
						break;
				}

				if(!isImageFile) {

					instance.setError('Error!', 'The file is not a valid image file.');
				}else{
					instance.uploadImage(files[0], files[0].type, files[0].name);
				}
			}
		}
	});

	this.setupOnBeforeUnload();
	this.setupModal();
	this.bindDeleteButtons();
}

IOMultipleImageField.prototype = {
	constructor: IOImageField,
	imageFileStatus: function(event) {

		var fileType;
		var fileName;
		var file;

		if(event.target != undefined) {

			fileType	= event.target.files[0].type;
			fileName	= event.target.files[0].name;
			file		= event.target.files[0];

		}else if(event.srcElement != undefined) {
			fileType	= event.srcElement.files[0].type;
			fileName	= event.srcElement.files[0].name;
			file		= event.srcElement.files[0];
		}

		if(fileType != undefined) {
			var isImageFile		= false;

			switch(fileType.toLowerCase()) {
				case 'image/jpeg':
					isImageFile		= true;
					break;
				case 'image/pjpeg':
					isImageFile		= true;
					break;
				case 'image/gif':
					isImageFile		= true;
					break;
				case 'image/png':
					isImageFile		= true;
					break;
				default:
					isImageFile		= false;
					break;
			}

			if(!isImageFile) {
				this.setError('Error!', 'The file is not a valid image file.');
			}else{
				this.uploadImage(file, fileType, fileName);
			}
		}
	},
	uploadImage: function(file, fileType, fileName) {

		this.imageUploading		= true;
		this.setProgressbar(10);
		$('#progress-' + this.inputId).show();

		var reader						= new FileReader();
		var instance					= this;
		var uploadUrl					= this.uploadRoute;

		reader.onloadend = function() {

			var fileData		= reader.result;

			$.ajax(
				{
					url: uploadUrl,
					cache: false,
					contentType: 'application/json; charset=UTF-8',
					data: JSON.stringify({
						"imageData"		: fileData,
						"imageType"		: fileType,
						"imageName"		: fileName,
						"fieldId"		: instance.inputId
					}),
					dataType: 'json',
					type: 'POST',
					xhr: function () {
						var xhr = new window.XMLHttpRequest();
						xhr.addEventListener("progress", function (evt) {

							if (evt.lengthComputable) {
								var percentComplete = Math.round(evt.loaded / evt.total * 100);
								instance.setProgressbar(percent);
							}
						}, false);
						return xhr;
					},
					success: function(response)
					{
						if(response.status) {

							instance.imageUploading			= false;
							instance.imageUploadComplete(response.data);
						}else{

							instance.imageUploading			= false;
							instance.setProgressbar(100);
							instance.setError('Error!', response.data);
						}
					},
					error: function() {

						instance.imageUploading			= false;
						instance.setProgressbar(100);
						instance.setError('Error!', 'An error has occured');
					}
				}
			);
		};

		reader.readAsDataURL(file);
	},
	imageUploadComplete	: function(fileName) {

		this.setProgressbar(100);
		this.imageUploading		= false;
		var imageUrl			= this.uploadPath + '/' + fileName;
		$('#uploadArea-'+this.inputId).append('<div id="multipleimagearea-' + this.inputId + '-' + this.currentLoopIdx + '" data-loopidx="' + this.currentLoopIdx + '"><a class="deletethisimage" href="#deletethisimage">x</a><img src="' + imageUrl + '"></div>');
		$('#adminMultipleImageUploadAreaInputs-' + this.inputId).append('<input type="hidden" name="' + this.fullName + '[]" value="' + fileName + '" data-loopidx="' + this.currentLoopIdx + '" />');
		this.currentLoopIdx += 1;
		this.unbindDeleteButtons();
		this.bindDeleteButtons();
	},
	setupOnBeforeUnload			: function() {
		var ua							= window.navigator.userAgent;
		var msie						= ua.indexOf("MSIE ");
		var instance					= this;
		if(msie <= 0) {
			window.onbeforeunload	= function(event) {
				if(instance.imageUploading)
				{
					event.returnValue	= 'Uploading progress does not finished yet. Do you want to continue ?';
				}
			};
		}
	},
	setupModal					: function() {
		var modalHtml				= '<div class="modal fade" id="imageUploadAlertDialog"><div class="modal-dialog"><div class="modal-content">';
		modalHtml					+= '<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		modalHtml					+= '<h4 class="modal-title" id="imageUploadAlertDialogTitle"></h4>';
		modalHtml					+= '</div><div class="modal-body">';
		modalHtml					+= '<p id="imageUploadAlertDialogMessage"></p>';
		modalHtml					+= '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
		modalHtml					+= '</div></div></div></div>';

		if($('#imageUploadAlertDialog').length <= 0) {
			$('body').append(modalHtml);
		}
	},
	setError					: function(title, message) {
		$('#imageUploadAlertDialogTitle').html(title);
		$('#imageUploadAlertDialogMessage').html(message);
		$('#imageUploadAlertDialog').modal('show');
	},
	setProgressbar: function(percent) {
		var progress			= $('#progress-' + this.inputId);
		var progressBar			= progress.children('.progress-bar');
		progressBar.attr('aria-valuenow', percent);
		progressBar.css('width', percent + '%');
		progressBar.html(percent + '%');

		if(percent == 100) {
			setTimeout(function() {
				progress.hide();
			}, 1000);
		}
	},
	bindDeleteButtons: function () {

		var instance = this;
		var uploadArea = $('#uploadArea-' + this.inputId);
		uploadArea.children().each(function () {

			var $this = $(this);
			var loopIdx = $this.attr('data-loopidx');
			$this.children('a').click(function (evt) {

				instance.imageUploading = true;
				evt.preventDefault();
				instance.deleteImage(loopIdx);
			});
		});
	},
	unbindDeleteButtons: function () {

		var uploadArea = $('#uploadArea-' + this.inputId);
		uploadArea.children().each(function () {

			var $this = $(this);
			$this.children('a').unbind('click');
		});
	},
	deleteImage: function (loopIdx) {

		$('#multipleimagearea-' + this.inputId + '-' + loopIdx).remove();
		$('#adminMultipleImageUploadAreaInputs-' + this.inputId).children().each(function () {

			var $this = $(this);
			var currentLoopIdx = $this.attr('data-loopidx');
			if(currentLoopIdx == loopIdx) {
				$this.remove();
			}
		});

		var instance = this;
		setTimeout(function () {

			instance.imageUploading = false;
		}, 750);
	}
};