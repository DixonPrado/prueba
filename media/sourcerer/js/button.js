/**
 * @package         Sourcerer
 * @version         9.0.3
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function() {
	'use strict';

	window.RegularLabs           = window.RegularLabs || {};
	window.RegularLabs.Sourcerer = window.RegularLabs.Sourcerer || {};

	window.RegularLabs.Sourcerer.Button = window.RegularLabs.Sourcerer.Button || {

		form   : null,
		options: {},

		insertText: function(editor_name) {
			this.options = Joomla.getOptions ? Joomla.getOptions('rl_sourcerer', {}) : Joomla.optionsStorage.rl_sourcerer || {};

			const tag_word  = this.options.syntax_word;
			const tag_start = this.options.tag_characters[0];
			const tag_end   = this.options.tag_characters[1];

			const source_editor = Joomla.editors.instances[editor_name];

			let pre_php = [];
			const codes = [];


			const code = this.form.editors['code'].getValue().trim();
			if (code.length) {
				codes.push(code);
			}

			let string = codes.join('\n');


			// convert to html entities
			string = this.htmlentities(string, 'ENT_NOQUOTES');

			// replace indentation with tab images
			string = this.indent2Images(string);

			// replace linebreaks with br tags
			string = string.nl2br();

			const attributes = [];

			if (this.form['raw'].value === '1') {
				attributes.push('raw="true"');
			}

			if (this.form['trim'].value === '1') {
				attributes.push('trim="true"');
			}


			string = this.styleCode(string);

			string = tag_start + (tag_word + ' ' + attributes.join(' ')).trim() + tag_end
				+ string
				+ tag_start + '/' + tag_word + tag_end;

			source_editor.replaceSelection(string);
		},

		joinPrePhp: function(string, pre_php) {
		},

		preparePrePhp: function(string, pre_php) {
		},

		styleCode: function(string) {
			if ( ! string.length) {
				return '';
			}

			const color_code        = '#555555';
			const color_php         = '#0000cc';
			const color_tags        = '#117700';
			const color_plugin_tags = '#770088';

			if (this.options.color_code) {
				// Style entire php block
				string = string.replace(
					/&lt;\?php(.*?)\?&gt;/gim,
					`<span style="color:${color_php};">[-php-start-]$1[-php-end-]</span>`
				);

				// Style tags
				string = string.replace(
					/(&lt;\/?[a-z][a-z0-9-_]*( .*?)?&gt;)/gim,
					`<span style="color:${color_tags};">$1</span>`
				);

				// Style plugin tags
				string = string.replace(
					/(\{\/?[a-z].*?\})/gim,
					`<span style="color:${color_plugin_tags};">$1</span>`
				);

				// Remove temporary php start/end tags
				string = string.replace(
					/\[-php-start-\]/gim,
					'&lt;?php'
				).replace(
					/\[-php-end-\]/gim,
					'?&gt;'
				);
			}

			string = `<span style="font-family:monospace;color:${color_code};">${string}</span>`;

			return string;
		},

		removeSourceTags: function(string) {
			const tag_word  = this.options.syntax_word;
			const tag_start = this.options.tag_characters[0];
			const tag_end   = this.options.tag_characters[1];

			let start_tag = this.preg_quote(tag_start + tag_word) + '.*?' + this.preg_quote(tag_end);
			let end_tag   = this.preg_quote(tag_start + '/' + tag_word + tag_end);

			let regex = new RegExp('(' + start_tag + ')\\s*', 'gim');
			if (string.match(regex)) {
				string = string.replace(regex, '');
			}

			regex  = new RegExp('\\s*' + end_tag, 'gim');
			string = string.replace(regex, '');

			return string.trim();
		},

		preg_quote: function(str) {
			return (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!<>\|\:])/g, '\\$1');
		},

		indent2Images: function(string) {
			const regex = new RegExp('((^|\n)(    |\t)*)(   ? ?|\t)', 'gm');

			while (regex.test(string)) {
				string = string.replace(regex, '$1<img src="' + this.options.root + '/media/sourcerer/images/tab.svg">');
			}

			return string.replace(regex, '<br>');
		},

		htmlentities: function(string, quote_style) {
			let tmp_str = string.toString();

			const histogram = this.get_html_translation_table('HTML_ENTITIES', quote_style);

			if (histogram === false) {
				return false;
			}

			for (let symbol in histogram) {
				const entity = histogram[symbol];
				tmp_str      = tmp_str.split(symbol).join(entity);
			}

			return tmp_str;
		},

		get_html_translation_table: function(table, quote_style) {
			var entities          = {}, histogram = {}, decimal = 0, symbol = '';
			var constMappingTable = {}, constMappingQuoteStyle = {};
			var useTable          = {}, useQuoteStyle = {};

			// Translate arguments
			constMappingTable[0]      = 'HTML_SPECIALCHARS';
			constMappingTable[1]      = 'HTML_ENTITIES';
			constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
			constMappingQuoteStyle[2] = 'ENT_COMPAT';
			constMappingQuoteStyle[3] = 'ENT_QUOTES';

			useTable      = ! isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
			useQuoteStyle = ! isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

			if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
				throw Error('Table: ' + useTable + ' not supported');
				// return false;
			}

			// ascii decimals for better compatibility
			entities['38'] = '&amp;';
			if (useQuoteStyle !== 'ENT_NOQUOTES') {
				entities['34'] = '&quot;';
			}
			if (useQuoteStyle === 'ENT_QUOTES') {
				entities['39'] = '&#039;';
			}
			entities['60'] = '&lt;';
			entities['62'] = '&gt;';

			if (useTable === 'HTML_ENTITIES') {
				entities['160'] = '&nbsp;';
				entities['161'] = '&iexcl;';
				entities['162'] = '&cent;';
				entities['163'] = '&pound;';
				entities['164'] = '&curren;';
				entities['165'] = '&yen;';
				entities['166'] = '&brvbar;';
				entities['167'] = '&sect;';
				entities['168'] = '&uml;';
				entities['169'] = '&copy;';
				entities['170'] = '&ordf;';
				entities['171'] = '&laquo;';
				entities['172'] = '&not;';
				entities['173'] = '&shy;';
				entities['174'] = '&reg;';
				entities['175'] = '&macr;';
				entities['176'] = '&deg;';
				entities['177'] = '&plusmn;';
				entities['178'] = '&sup2;';
				entities['179'] = '&sup3;';
				entities['180'] = '&acute;';
				entities['181'] = '&micro;';
				entities['182'] = '&para;';
				entities['183'] = '&middot;';
				entities['184'] = '&cedil;';
				entities['185'] = '&sup1;';
				entities['186'] = '&ordm;';
				entities['187'] = '&raquo;';
				entities['188'] = '&frac14;';
				entities['189'] = '&frac12;';
				entities['190'] = '&frac34;';
				entities['191'] = '&iquest;';
				entities['192'] = '&Agrave;';
				entities['193'] = '&Aacute;';
				entities['194'] = '&Acirc;';
				entities['195'] = '&Atilde;';
				entities['196'] = '&Auml;';
				entities['197'] = '&Aring;';
				entities['198'] = '&AElig;';
				entities['199'] = '&Ccedil;';
				entities['200'] = '&Egrave;';
				entities['201'] = '&Eacute;';
				entities['202'] = '&Ecirc;';
				entities['203'] = '&Euml;';
				entities['204'] = '&Igrave;';
				entities['205'] = '&Iacute;';
				entities['206'] = '&Icirc;';
				entities['207'] = '&Iuml;';
				entities['208'] = '&ETH;';
				entities['209'] = '&Ntilde;';
				entities['210'] = '&Ograve;';
				entities['211'] = '&Oacute;';
				entities['212'] = '&Ocirc;';
				entities['213'] = '&Otilde;';
				entities['214'] = '&Ouml;';
				entities['215'] = '&times;';
				entities['216'] = '&Oslash;';
				entities['217'] = '&Ugrave;';
				entities['218'] = '&Uacute;';
				entities['219'] = '&Ucirc;';
				entities['220'] = '&Uuml;';
				entities['221'] = '&Yacute;';
				entities['222'] = '&THORN;';
				entities['223'] = '&szlig;';
				entities['224'] = '&agrave;';
				entities['225'] = '&aacute;';
				entities['226'] = '&acirc;';
				entities['227'] = '&atilde;';
				entities['228'] = '&auml;';
				entities['229'] = '&aring;';
				entities['230'] = '&aelig;';
				entities['231'] = '&ccedil;';
				entities['232'] = '&egrave;';
				entities['233'] = '&eacute;';
				entities['234'] = '&ecirc;';
				entities['235'] = '&euml;';
				entities['236'] = '&igrave;';
				entities['237'] = '&iacute;';
				entities['238'] = '&icirc;';
				entities['239'] = '&iuml;';
				entities['240'] = '&eth;';
				entities['241'] = '&ntilde;';
				entities['242'] = '&ograve;';
				entities['243'] = '&oacute;';
				entities['244'] = '&ocirc;';
				entities['245'] = '&otilde;';
				entities['246'] = '&ouml;';
				entities['247'] = '&divide;';
				entities['248'] = '&oslash;';
				entities['249'] = '&ugrave;';
				entities['250'] = '&uacute;';
				entities['251'] = '&ucirc;';
				entities['252'] = '&uuml;';
				entities['253'] = '&yacute;';
				entities['254'] = '&thorn;';
				entities['255'] = '&yuml;';
			}

			// ascii decimals to real symbols
			for (decimal in entities) {
				symbol            = String.fromCharCode(decimal);
				histogram[symbol] = entities[decimal];
			}

			return histogram;
		},
	};

	String.prototype.escapeQuotes = function() {
		return this.replace(/'/g, '\\\'');
	};

	String.prototype.hasLineBreaks = function() {
		const regex = new RegExp('\n', 'gm');
		return regex.test(this);
	};

	String.prototype.indent = function() {
		const regex = new RegExp('\n', 'gm');

		return '\n    ' + this.replace(regex, '\n    ') + '\n';
	};

	String.prototype.nl2br = function() {
		const regex = new RegExp('\n', 'gm');

		return this.replace(regex, '<br>');
	};
})();
