/**
 * Created by ilk3r on 03/11/2016.
 */

function IORoute(metaBlockName, contentBlockName, contentContainerId,
				 javascriptBlockName, styleBlockName, unloadPageScriptBlockName, options) {

	this.options = (options === undefined) ? {} : options;
	this.maxHistory = (this.options.maxHistory === undefined) ? 7 : this.options.maxHistory;
	this.metaBlockName = metaBlockName;
	this.contentBlockName = contentBlockName;
	this.contentContainerId = contentContainerId;
	this.javascriptBlockName = javascriptBlockName;
	this.styleBlockName = styleBlockName;
	this.unloadPageScriptBlockName = unloadPageScriptBlockName;
	this.currentLinks = [];
	this.isPageLoading = false;
	this.lastPageUrl = '';
	this.pageHistory = [];
	this.updateHistory = true;
	this.pageUnload = null;
	this.addedScripts = [];
	this.addedStyles = [];
	this.waitingScriptLoads = 0;
	this.evalScript = "";

	if(this.isEnabled()) {

		window.IORoute = this;

		window.addEventListener('popstate', function(evt) {

			evt.preventDefault();

			// The popstate event is fired each time when the current history entry changes.
			if(window.IORoute.updateHistory) {

				window.IORoute.goToHistory();
			}

		}, false);

		window.onbeforeunload = function() {
			window.name = "IOReloadPage";
		};

		this.listenLinks();
	}
}

IORoute.prototype = {
	constructor: IORoute,
	isEnabled: function () {

		return typeof(window.history) != 'undefined';
	},
	listenLinks: function () {

		$('a').each(function () {

			var anchor = $(this);
			var ignoreStr = anchor.attr('data-ignoreroute');
			var ignore = (typeof(ignoreStr) != 'undefined' && ignoreStr == 'true');
			var href = anchor.attr('href');
			if(typeof(href) != 'undefined' && !ignore) {

				var useRouterForThisLink = true;

				if(href.substr(0, 5) == 'http:') {

					useRouterForThisLink = false;
				}else if(href.substr(0, 4) == 'ftp:') {

					useRouterForThisLink = false;
				}else if(href.substr(0, 6) == 'https:') {

					useRouterForThisLink = false;
				}else if(href.substr(0, 1) == '#') {

					useRouterForThisLink = false;
				}else if(href.substr(0, 11) == 'javascript:') {

					useRouterForThisLink = false;
				}

				if(useRouterForThisLink) {

					window.IORoute.currentLinks.push(anchor);
					anchor.click(function (evt) {

						if(evt.which == 1) {

							evt.preventDefault();
							window.IORoute.linkClicked($(this).attr('href'));
						}
					});
				}
			}
		});
	},
	unbindLinks: function () {

		for (var i = 0, il = this.currentLinks.length; i < il; i++) {

			this.currentLinks[i].unbind('click');
		}
		this.currentLinks = [];
	},
	linkClicked: function (href) {

		if(this.isPageLoading) {
			return;
		}

		this.isPageLoading = true;
		if(typeof(this.startLoader) != 'undefined') {
			this.startLoader();
		}

		if(this.pageUnload != null && (typeof(this.pageUnload) === 'function')) {

			this.pageUnload();
			this.pageUnload = null;
		}

		for (var as = 0, asl = this.addedScripts.length; as < asl; as++) {

			var currentScriptId = this.addedScripts[as];
			$('#' + currentScriptId).remove();
		}
		this.addedScripts = [];
		this.waitingScriptLoads = 0;
		this.evalScript = "";

		for (var ast = 0, astl = this.addedStyles.length; ast < astl; ast++) {

			var currentStyleId = this.addedStyles[ast];
			$('#' + currentStyleId).remove();
		}
		this.addedStyles = [];

		this.lastPageUrl = href;
		var pageInHistory = false;
		for (var i = 0, hl = this.pageHistory.length; i < hl; i++) {

			var currentPageHistory = this.pageHistory[i];
			if(currentPageHistory.url == href) {

				pageInHistory = true;
				this.finishPageLoad(i);
				break;
			}
		}

		if(!pageInHistory) {
			$.get(href, function (response) {

				window.IORoute.parsePageData(response);
			}, 'json');
		}
	},
	parsePageData: function (responseObject) {

		var metaData = {};
		if(typeof(responseObject[this.metaBlockName]) != 'undefined') {
			metaData = this.parseTitleblock(responseObject[this.metaBlockName]);
		}else{
			metaData = {
				title: '',
				description: '',
				keywords: ''
			};
		}

		var contentHtml = '';
		if(typeof(responseObject[this.contentBlockName]) != 'undefined') {

			contentHtml = responseObject[this.contentBlockName];
		}

		var javascripts = '';
		if(typeof(responseObject[this.javascriptBlockName]) != 'undefined') {

			javascripts = responseObject[this.javascriptBlockName];
		}

		var styles = '';
		if(typeof(responseObject[this.styleBlockName]) != 'undefined') {

			styles = responseObject[this.styleBlockName];
		}

		var unloadScript = '';
		if(typeof(responseObject[this.unloadPageScriptBlockName]) != 'undefined') {

			unloadScript = responseObject[this.unloadPageScriptBlockName];
		}

		this.updateHistoryData(this.lastPageUrl, metaData, contentHtml, javascripts, styles, unloadScript);
		this.finishPageLoad();
	},
	parseTitleblock: function (input) {

		var response = {};
		var parser = new DOMParser();
		var currentDocument = parser.parseFromString(input, 'text/html');
		response.title = currentDocument.title;
		var metas = currentDocument.getElementsByTagName('meta');
		for (var i = 0, il = metas.length; i < il; i++) {

			var currentMetaData = metas[i];
			if(currentMetaData.name == 'description') {

				response.description = currentMetaData.content;
			}else if(currentMetaData.name == 'keywords') {

				response.keywords = currentMetaData.content;
			}
		}

		return response;
	},
	parseAndExecuteJavascriptsBlock: function (input) {

		var parser = new DOMParser();
		var documentJS = parser.parseFromString(input, 'text/html');
		var scripts = documentJS.getElementsByTagName('script');

		for (var i = 0, sl = scripts.length; i < sl; i++) {

			var currentScript = scripts[i];
			if(currentScript.type == 'text/javascript') {

				if(currentScript.innerHTML.length < 2) {

					var clonedScript = document.createElement('script');
					clonedScript.type = 'text/javascript';
					clonedScript.id = 'io_js_' + i;
					this.addedScripts.push(clonedScript.id);
					this.waitingScriptLoads += 1;
					clonedScript.onload = function () {
						window.IORoute.updateWaitingScriptLoads();
					};
					clonedScript.onerror  = function () {
						window.IORoute.updateWaitingScriptLoads();
					};

					clonedScript.src = currentScript.getAttribute('src', -1);
					document.body.appendChild(clonedScript);
				}else{

					this.evalScript += currentScript.innerHTML;
					if(this.waitingScriptLoads == 0) {
						eval(this.evalScript);
					}
				}
			}
		}
	},
	parseAndExecuteStylesBlock: function (input) {

		var parser = new DOMParser();
		var documentStyle = parser.parseFromString(input, 'text/html');
		var cssFiles = documentStyle.getElementsByTagName('link');

		for (var i = 0, sl = cssFiles.length; i < sl; i++) {

			var currentCss = cssFiles[i];
			if(currentCss.rel == 'stylesheet') {

				var clonedCss = document.createElement('link');
				clonedCss.id = 'io_css_' + i;
				this.addedStyles.push(clonedCss.id);
				clonedCss.rel = currentCss.rel;
				clonedCss.type = currentCss.type;
				clonedCss.href = currentCss.getAttribute('href', -1);
				document.body.appendChild(clonedCss);
			}
		}

		var styleBlocks = documentStyle.getElementsByTagName('style');
		for (var j = 0, stl = styleBlocks.length; j < stl; j++) {

			var currentStyle = styleBlocks[i];

			var clonedStyle = document.createElement('style');
			clonedCss.type = 'text/css';
			clonedCss.id = 'io_style_' + j;
			this.addedStyles.push(clonedCss.id);
			clonedStyle.innerHTML = currentStyle.innerHTML;
			document.body.appendChild(clonedStyle);
		}
	},
	finishPageLoad: function (pageIdx) {

		var dataIdx = (pageIdx === undefined) ? (this.pageHistory.length - 1) : pageIdx;
		var historyData = this.pageHistory[dataIdx];

		this.unbindLinks();
		if(this.updateHistory) {

			history.pushState({}, historyData.metaData.title, historyData.url);
		}else{
			this.updateHistory = true;
		}
		document.title = historyData.metaData.title;
		var metas = document.getElementsByTagName('meta');
		for (var i = 0, il = metas.length; i < il; i++) {

			var currentMetaData = metas[i];
			if(currentMetaData.name == 'description') {

				currentMetaData.content = historyData.metaData.description;
			}else if(currentMetaData.name == 'keywords') {

				currentMetaData.content = historyData.metaData.keywords;
			}
		}

		if(historyData.styles.length > 2) {

			this.parseAndExecuteStylesBlock(historyData.styles);
		}

		var domContainer = $('#' + this.contentContainerId);
		domContainer.css('opacity', '0').html(historyData.contentHtml);

		if(historyData.javascripts.length > 2) {

			this.parseAndExecuteJavascriptsBlock(historyData.javascripts);
		}

		if(historyData.unloadScript.length > 2) {

			eval(historyData.unloadScript);
		}

		this.listenLinks();
		if(typeof(this.stopLoader) != 'undefined') {
			this.stopLoader();
		}

		domContainer.animate({opacity: 1}, 500);
		$('html, body').animate({scrollTop: 0}, 500);
		this.isPageLoading = false;
	},
	updateHistoryData: function (url, metaData, contentHtml, javascripts, styles, unloadScript) {

		var historyData = {
			"url": url,
			"metaData": metaData,
			"contentHtml": contentHtml,
			"javascripts": javascripts,
			"styles": styles,
			"unloadScript": unloadScript
		};

		if(this.pageHistory.length < this.maxHistory) {

			this.pageHistory.push(historyData);
		}else {

			var tmpPageHistory = [];
			for (var i = 1, hl = this.pageHistory.length; i < hl; i++) {
				tmpPageHistory.push(this.pageHistory[i]);
			}
			tmpPageHistory.push(historyData);
			this.pageHistory = tmpPageHistory;
		}
	},
	goToHistory: function () {

		this.updateHistory = false;
		this.linkClicked(window.location.pathname);
	},
	setPageUnload: function (callback) {

		this.pageUnload = callback;
	},
	updateWaitingScriptLoads: function () {

		this.waitingScriptLoads -= 1;

		if(this.waitingScriptLoads == 0) {
			eval(this.evalScript);
		}
	}
};