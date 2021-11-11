/**
 * @package         Regular Labs Library
 * @version         21.11.1666
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function() {
	'use strict';

	window.RegularLabs = window.RegularLabs || {};

	window.RegularLabs.Scripts = window.RegularLabs.Scripts || {
		version: '21.11.1666',

		ajax_list        : [],
		started_ajax_list: false,
		ajax_list_timer  : null,

		loadAjax: function(url, success, fail, query, timeout, dataType, cache) {
			if (url.indexOf('index.php') !== 0 && url.indexOf('administrator/index.php') !== 0) {
				url = url.replace('http://', '');
				url = `index.php?rl_qp=1&url=${encodeURIComponent(url)}`;
				if (timeout) {
					url += `&timeout=${timeout}`;
				}
				if (cache) {
					url += `&cache=${cache}`;
				}
			}

			let base = window.location.pathname;

			base = base.substring(0, base.lastIndexOf('/'));

			if (
				typeof Joomla !== 'undefined'
				&& typeof Joomla.getOptions !== 'undefined'
				&& Joomla.getOptions('system.paths')
			) {
				base = Joomla.getOptions('system.paths').base;
			}

			// console.log(url);
			// console.log(`${base}/${url}`);

			this.loadUrl(
				`${base}/${url}`,
				null,
				(function(data) {
					if (success) {
						success = `data = data ? data : ''; ${success};`.replace(/;\s*;/g, ';');
						eval(success);
					}
				}),
				(function(data) {
					if (fail) {
						fail = `data = data ? data : ''; ${fail};`.replace(/;\s*;/g, ';');
						eval(fail);
					}
				})
			);
		},

		/**
		 * Loads a url with optional POST data and optionally calls a function on success or fail.
		 *
		 * @param url      String containing the url to load.
		 * @param data     Optional string representing the POST data to send along.
		 * @param success  Optional callback function to execute when the url loads successfully (status 200).
		 * @param fail     Optional callback function to execute when the url fails to load.
		 */
		loadUrl: function(url, data, success, fail) {
			return new Promise((resolve) => {
				const request = new XMLHttpRequest();

				request.open("POST", url, true);

				request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

				request.onreadystatechange = function() {
					if (this.readyState !== 4) {
						return;
					}

					if (this.status !== 200) {
						fail && fail.call(null, this.responseText, this.status, this);
						resolve(this);
						return;
					}

					success && success.call(null, this.responseText, this.status, this);
					resolve(this);
				};

				request.send(data);
			});
		},

		displayVersion: function(data, extension, version) {
			if ( ! data) {
				return;
			}

			const xml = this.getObjectFromXML(data);

			if ( ! xml) {
				return;
			}

			if (typeof xml[extension] === 'undefined') {
				return;
			}

			const dat = xml[extension];

			if ( ! dat || typeof dat.version === 'undefined' || ! dat.version) {
				return;
			}

			const new_version = dat.version;
			const compare     = this.compareVersions(version, new_version);

			if (compare != '<') {
				return;
			}

			let el = document.querySelector(`#regularlabs_newversionnumber_${extension}`);

			if (el) {
				el.textContent(new_version);
			}

			el = document.querySelector(`#regularlabs_version_${extension}`);

			if (el) {
				el.css.display = 'block';
				el.parentElement.classList.remove('hidden');
			}
		},

		addToLoadAjaxList: function(url, success, error) {
			// wrap inside the loadajax function (and escape string values)
			url     = url.replace(/'/g, "\\'");
			success = success.replace(/'/g, "\\'");
			error   = error.replace(/'/g, "\\'");

			const action = `RegularLabs.Scripts.loadAjax(
					'${url}',
					'${success};RegularLabs.Scripts.ajaxRun();',
					'${error};RegularLabs.Scripts.ajaxRun();'
				)`;

			this.addToAjaxList(action);
		},

		addToAjaxList: function(action) {
			this.ajax_list.push(action);

			if ( ! this.started_ajax_list) {
				this.ajaxRun();
			}
		},

		ajaxRun: function() {
			if ( ! this.ajax_list.length) {
				return;
			}

			clearTimeout(this.ajax_list_timer);

			this.started_ajax_list = true;

			const action = this.ajax_list.shift();

			eval(`${action};`);

			if ( ! this.ajax_list.length) {
				this.started_ajax_list = false;
				return;
			}

			// Re-trigger this ajaxRun function just in case it hangs somewhere
			this.ajax_list_timer = setTimeout(
				function() {
					RegularLabs.Scripts.ajaxRun();
				},
				5000
			);
		},

		in_array: function(needle, haystack, casesensitive) {
			if ({}.toString.call(needle).slice(8, -1) !== 'Array') {
				needle = [needle];
			}
			if ({}.toString.call(haystack).slice(8, -1) !== 'Array') {
				haystack = [haystack];
			}

			for (let h = 0; h < haystack.length; h++) {
				for (let n = 0; n < needle.length; n++) {
					if (casesensitive) {
						if (haystack[h] == needle[n]) {
							return true;
						}

						continue;
					}

					if (haystack[h].toLowerCase() == needle[n].toLowerCase()) {
						return true;
					}
				}
			}
			return false;
		},

		getObjectFromXML: function(xml) {
			if ( ! xml) {
				return;
			}

			console.log('----');
			console.log(xml);
			return;

			const obj = [];
			// $(xml).find('extension').each(function() {
			// 	const el = [];
			// 	$(this).children().each(function() {
			// 		el[this.nodeName.toLowerCase()] = String($(this).text()).trim();
			// 	});
			// 	if (typeof el.alias !== 'undefined') {
			// 		obj[el.alias] = el;
			// 	}
			// 	if (typeof el.extname !== 'undefined' && el.extname != el.alias) {
			// 		obj[el.extname] = el;
			// 	}
			// });

			return obj;
		},

		compareVersions: function(number1, number2) {
			number1 = number1.split('.');
			number2 = number2.split('.');

			let letter1 = '';
			let letter2 = '';

			const max = Math.max(number1.length, number2.length);
			for (let i = 0; i < max; i++) {
				if (typeof number1[i] === 'undefined') {
					number1[i] = '0';
				}
				if (typeof number2[i] === 'undefined') {
					number2[i] = '0';
				}

				letter1    = number1[i].replace(/^[0-9]*(.*)/, '$1');
				number1[i] = parseInt(number1[i]);
				letter2    = number2[i].replace(/^[0-9]*(.*)/, '$1');
				number2[i] = parseInt(number2[i]);

				if (number1[i] < number2[i]) {
					return '<';
				}

				if (number1[i] > number2[i]) {
					return '>';
				}
			}

			// numbers are same, so compare trailing letters
			if (letter2 && ( ! letter1 || letter1 > letter2)) {
				return '>';
			}

			if (letter1 && ( ! letter2 || letter1 < letter2)) {
				return '<';
			}

			return '=';
		},

		getEditorSelection: function(editorID) {
			const editor_textarea = document.getElementById(editorID);

			if ( ! editor_textarea) {
				return '';
			}

			const editorIFrame = editor_textarea.parentNode.querySelector('iframe');

			if ( ! editorIFrame) {
				return '';
			}

			const contentWindow = editorIFrame.contentWindow;

			if (typeof contentWindow.getSelection !== 'undefined') {
				const sel = contentWindow.getSelection();

				if (sel.rangeCount) {
					const container = contentWindow.document.createElement('div');
					const len       = sel.rangeCount;

					for (let i = 0; i < len; ++i) {
						container.appendChild(sel.getRangeAt(i).cloneContents());
					}

					return container.innerHTML;
				}

				return '';
			}

			if (typeof contentWindow.document.selection !== 'undefined'
				&& contentWindow.document.selection.type === 'Text') {
				return contentWindow.document.selection.createRange().htmlText;
			}

			return '';
		}
	};
})();
