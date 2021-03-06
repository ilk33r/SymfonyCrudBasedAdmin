/*!
 * Created by ilk3r on 18/02/16.
 */


if (!String.prototype.format) {

	String.prototype.format = function() {
		var newStr			= this, i = 0;

		while (/%s/.test(newStr))
			newStr = newStr.replace("%s", arguments[i++]);

		return newStr;
	}
}

if(!Array.prototype.contains) {

	Array.prototype.contains = function(k) {

		for(var i=0; i < this.length; i++){
			if(this[i] === k){
				return true;
			}
		}
		return false;
	}
}

var IO_Helpers					= {
	getRandomArbitary			: function (minVal, maxVal)
	{
		return Math.random() * (maxVal - minVal) + minVal;
	},
	getRandomInt 				: function (minVal, maxVal)
	{
		return Math.floor(Math.random() * (maxVal - minVal + 1)) + minVal;
	},
	getScrollbarWidth			: function ()
	{
		var outer					= document.createElement("div");
		outer.style.visibility		= "hidden";
		outer.style.width			= "100px";
		outer.style.msOverflowStyle	= "scrollbar"; // needed for WinJS apps
		document.body.appendChild(outer);

		var widthNoScroll			= outer.offsetWidth;
		outer.style.overflow		= "scroll";
		var inner					= document.createElement("div");
		inner.style.width			= "100%";
		outer.appendChild(inner);

		var widthWithScroll			= inner.offsetWidth;
		outer.parentNode.removeChild(outer);
		return widthNoScroll - widthWithScroll;
	},
	doGetCaretPosition			: function (oField)
	{
		var iCaretPos				= 0;

		if (document.selection)
		{
			oField.focus();
			var oSel = document.selection.createRange();
			oSel.moveStart('character', -oField.value.length);
			iCaretPos = oSel.text.length;
		}
		else if (oField.selectionStart || oField.selectionStart == '0')
			iCaretPos = oField.selectionStart;

		return (iCaretPos);
	},
	wordLimit					: function (text, WordCount)
	{
		var wordCount			= WordCount || 3;
		var words				= text.split(' ');
		var resultText			= '';

		for(var i = 0; i < words.length; i++)
		{
			resultText			+= words[i] + ' ';

			if(i >= 3)
			{
				resultText		+= '...';
				break;
			}
		}

		return resultText;
	},
	characterLimitForWord		: function (text, CharacterCount)
	{
		var characterCount				= CharacterCount || 20;
		var words						= text.split(' ');
		var currentCharacterCount		= 0;
		var resultText					= '';

		for(var i = 0; i < words.length; i++)
		{
			currentCharacterCount		+= words[i].length + 1;
			if(currentCharacterCount >= characterCount)
			{
				resultText		+= '...';
				break;
			}

			resultText			+= words[i] + ' ';
		}

		return resultText;
	},
	isSearchEngine				: function ()
	{
		var response			= false;
		var userAgent			= navigator.userAgent || navigator.vendor || window.opera;

		if(/Googlebot|Facebook|Slurp|search.msn.com|nutch|simpy|bot|ASPSeek|crawler|msnbot|Libwww-perl|FAST|Baidu/i.test(userAgent))
			response			= true;

		return response;
	},
	checkIsFunction: function(callbackFunction) {
		if(callbackFunction && (typeof(callbackFunction) === 'function'))
		{
			return true;
		}else{
			return false;
		}
	},
	getBaseUrl: function() {

		if (!window.location.origin) {
			window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
		}

		return window.location.origin;
	},
	isInteger: function(number) {

		return '' + number === '' + parseInt(number);
	},
	parseUri: function(url) {

		var result = {};

		var anchor = document.createElement('a');
		anchor.href = url;

		var keys = 'protocol hostname host pathname port search hash href'.split(' ');
		for (var keyIndex in keys) {
			var currentKey = keys[keyIndex];
			result[currentKey] = anchor[currentKey];
		}

		result.toString = function() { return anchor.href; };
		result.requestUri = result.pathname + result.search;
		return result;

	},
	getScreenType: function() {

		var swRef = screen.width;
		if(swRef < 768) {
			return 'xs';
		}else if(swRef > 767 && swRef < 992) {
			return 'sm';
		}else if(swRef > 991 && swRef < 1200) {
			return 'md';
		}else{
			return 'lg';
		}
	},
	generateFormData: function(formId) {

		var form = document.getElementById(formId);
		var inputs = form.getElementsByTagName('input');
		var selects = form.getElementsByTagName('select');
		var textareas = form.getElementsByTagName('textarea');

		var postData = {};

		for(var i = 0, il = inputs.length; i < il; i++) {

			var iname = inputs[i].name;
			postData[iname] = inputs[i].value;
		}

		for(var s = 0, sl = selects.length; s < sl; s++) {

			var sname = selects[s].name;
			postData[sname] = selects[s].value;
		}

		for(var t = 0, tl = textareas.length; t < tl; t++) {

			var tname = textareas[t].name;
			postData[tname] = textareas[t].value;
		}

		return postData;
	}
};

var IO_debug					= {
	isDevelopment				: false,
	log							: function () {
		if(!window.console)
			return;

		if(!this.isDevelopment)
			return;

		console.log(arguments);
	}
};

function IO_JsonRequest(apiUrl, objectData, callback, changeUrl) {

	var urlChange = (typeof(changeUrl) != 'undefined') ? changeUrl : true;

	var requestUrl = null;
	if(urlChange) {
		requestUrl = "%s/%s/".format(IO_Helpers.getBaseUrl(), apiUrl);
	}else{
		requestUrl = apiUrl;
	}

	var xhr = $.ajax(requestUrl, {
		method: 'POST',
		contentType: 'application/json',
		dataType: 'json',
		data: JSON.stringify(objectData),
		success: function (response) {
			if(IO_Helpers.checkIsFunction(callback)) {
				callback(response);
			}
		}
	});

	return xhr;
}

function IO_CheckScrollIsBottom(callback) {

	window.onscroll = function(ev) {
		if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 60)) {
			if(IO_Helpers.checkIsFunction(callback)) {
				callback();
			}
		}
	};
}
