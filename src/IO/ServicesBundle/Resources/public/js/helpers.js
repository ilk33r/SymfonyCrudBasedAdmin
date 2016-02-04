/**
 * Created by ilk3r on 14/08/15.
 */

if (!String.prototype.format)
{
	String.prototype.format = function()
	{
		var newStr			= this, i = 0;

		while (/%s/.test(newStr))
			newStr			= newStr.replace("%s", arguments[i++])

		return newStr;
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
	checkIsFunction				: function(callbackFunction)
	{
		if(callbackFunction && (typeof(callbackFunction) === 'function'))
		{
			return true;
		}else{
			return false;
		}
	},
	getBaseUrl					: function() {

		if (!window.location.origin) {
			window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
		}

		return window.location.origin;
	},
	isInteger					: function(number) {

		return '' + number === '' + parseInt(number);
	}
};

var IO_debug					= {
	isDevelopment				: false,
	log							: function ()
	{
		if(!window.console)
			return;

		if(!this.isDevelopment)
			return;

		console.log(arguments);
	}
};

function IO_JsonRequest(apiUrl, objectData, callback) {

	var requestUrl = "%s/ajax/%s/".format(IO_Helpers.getBaseUrl(), apiUrl);

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