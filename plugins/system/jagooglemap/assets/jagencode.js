/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
(function($) {
	window.JAElementGenCode = function(){
		this.initialize = function () {
			this.code = '{jamap ';
			this.prefix = 'jform[params]';
			this.objText = 'jform_params_code_container';
			this.objCheckboxes = this.prefix + '[list_params][]';
			this.mapPreviewId = 'jaMapPreview';
			this.form = document.adminForm;
			
			this.mapHolder = 'map-preview-container';
			this.mapId = 'ja-widget-map';
			this.objMap = null;
			this.aUserSetting = {};
			//
			this.scanItem();
			this.getUserSetting();
		};
		
		this.getUserSetting = function() {
			this.aUserSetting = {};
			
			//get user setting
			var sConfig = $('#'+this.objText).val();
			settings = sConfig.trim();

			settings = settings.replace('{jamap ', '{');
			settings = settings.replace('{/jamap}', '');
			settings = settings.replace(/"/g,'\\"');
			settings = settings.replace(/'/g, '"');
			settings = settings.replace(/([a-z0-9_]+)=/g, ', "$1":');
			settings = settings.replace(/^\{,/, '{');

			this.aUserSetting = JSON.parse(settings);
		};

		this.getFormData = function() {
			var frmData = $(this.form).serializeObject();

			var data = {};
			for(var property in frmData) {
				var prop = property;
				if(prop.indexOf(this.prefix) == 0) {
					prop = prop.substr(this.prefix.length);
					prop = prop.split(/\]\[/i);//E.g:jform[params][locations][location][0]

					var cdata = data;
					for(var i=0; i<prop.length; i++) {
						var sp = prop[i].replace(/[\[\]]+/g, '');

						if(i<prop.length - 1) {
							if(typeof(cdata[sp]) == 'undefined') {
								cdata[sp] = {};
							}

							cdata = cdata[sp];
						} else {
							cdata[sp] = frmData[property];
						}
					}

				}
			}
			return data;
		};
		
		this.genCode = function() {
			this.scanItem();
			this.getUserSetting();
			//
			var str = this.code,
				data = this.getFormData();
			for(var i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
				var item = this.form.elements[this.objCheckboxes][i];
				if(item.checked && !item.disabled) {
					var e = item.value,
						value = '';

					if(typeof(data[e]) != 'undefined') {
						value = data[e];
						if(typeof(value) == 'object') {
							value = JSON.stringify(value);
						}
					}

					//check user setting
					if(this.aUserSetting[item.value]) {
						value = this.aUserSetting[item.value];
					}
					
					str += item.value + "='" + (String(value)) + "' ";
				}
			}
			str += '}{/jamap}';
			
			$('#'+this.objText).val(str);
			
			//reset user setting
			this.getUserSetting();
		};
		/**
		 * Scan for check item is enable or diabled
		*/
		this.scanItem = function() {
			var i;
			for(i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
				var item = this.form.elements[this.objCheckboxes][i];
				if(item.alt) {
					var disabled = (!item.checked || item.disabled) ? true : false;
					this.setChildren(item.alt, disabled);
				}
			}
		};
		
		this.setChildren = function(children, disabled) {
			aChild = children.split(',');
			var i;
			var j;
			for(j=0; j<aChild.length; j++) {
				for(i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
					var item = this.form.elements[this.objCheckboxes][i];
					if(item.value == aChild[j]) {
						item.disabled = disabled;
						var label = item.id + '-label';
						if($('#'+label)) {
							if(disabled)
								$('#'+label).addClass('item_disable');
							else
								$('#'+label).removeClass('item_disable');
						}
						break;
					}
				}
				
			}
		};
		
		this.previewMap = function() {
			var aParams = this.getFormData();
			this.getUserSetting();
			
			for(key in this.aUserSetting) {
				aParams[key] = this.aUserSetting[key];
			}
			
			aParams['context_menu'] = 0;
			aParams["map_width"] = parseInt(aParams["map_width"]);
			aParams["map_height"] = parseInt(aParams["map_height"]);
			aParams["maptype_control_display"] = parseInt(aParams["maptype_control_display"]);
			aParams["toolbar_control_display"] = parseInt(aParams["toolbar_control_display"]);
			aParams["display_scale"] = parseInt(aParams["display_scale"]);
			aParams["display_overview"] = parseInt(aParams["display_overview"]);
			aParams["zoom"] = parseInt(aParams["zoom"]);
		
			this.createMap(aParams);
			//
			if(this.objMap == null) {
				this.objMap = new JAWidgetMap(this.mapId, aParams);
				this.objMap.displayMap();
			} else {
				this.objMap.setMap(aParams);
				this.objMap.displayMap();
			}
		};
		
		
		this.createMap = function(aParams){
			var map_container = this.mapId + '-container';
			
			if(!$('#'+this.mapId).length) {
				
				var container = jQuery('<div/>', {id: map_container, class: 'map-container'}),
					map = jQuery('<div/>', {id: this.mapId}).css({ 'width': aParams.map_width, 'height':  aParams.map_height }),
					route = jQuery('<div/>', {id: this.mapId + '-route', class: 'map-route'});

				container.appendTo($('#previewBody'));
				map.appendTo($('#'+map_container));
				route.appendTo($('#'+map_container));
			} else {
				$('#'+this.mapId).css({ width: aParams.map_width, height:  aParams.map_height });
				$('#'+map_container).appendTo($('#previewBody'));
			}
			
			$('#'+map_container).parents('.modal-dialog').css({ 'max-width': parseInt(aParams.map_width+2), 'max-height':  parseInt(aParams.map_height) });

			if(aParams.display_popup == 1) {
				var a = new Element('a', {
					id: 'open_new_window',
					events: {
						'click': function(){
							alert('Only work on Front-End!');
						}
					},
					href: '#mapPreview'
				});
				a.text('OPEN IN NEW WINDOW');
				
				a.append($('#sbox-content'), 'top');
			} else {
				if($('#open_new_window')) $('#open_new_window').remove();
			}
		};
		
		this.addslashes = function(str) {
			//str=str.replace(/\\/g,'\\\\');
			str=str.replace(/\'/g,'\\\'');
			//str=str.replace(/\"/g,'\\"');
			//str=str.replace(/\0/g,'\\0');
			return str;
		};
		
		this.stripslashes = function(str) {
			str=str.replace(/\\'/g,'\'');
			//str=str.replace(/\\"/g,'"');
			//str=str.replace(/\\0/g,'\0');
			//str=str.replace(/\\\\/g,'\\');
			return str;
		};
		this.initialize();
	};


	window.CopyToClipboard = function(obj)
	{
		$('#'+obj).focus();
		$('#'+obj).select();
		var CopiedTxt = '';
		if(document.selection) {
			CopiedTxt = document.selection.createRange();
			CopiedTxt.execCommand("Copy");
		}
	}

})(jQuery);

(function($){
  $.fn.serializeObject = function(){
    var obj = {};

    $.each( this.serializeArray(), function(i,o){
      var n = o.name,
        v = o.value;

        obj[n] = obj[n] === undefined ? v
          : $.isArray( obj[n] ) ? obj[n].concat( v )
          : [ obj[n], v ];
    });

    return obj;
  };

})(jQuery);

jQuery(document).ready(function($){
	var objGencode = new JAElementGenCode();
	var i;
	for(i=0; i < objGencode.form.elements[objGencode.objCheckboxes].length; i++) {
		$(objGencode.form.elements[objGencode.objCheckboxes][i]).on('click', function() {
			objGencode.genCode();
		});
	}

	$('#'+objGencode.mapPreviewId).on('click', function(e) {
		e.preventDefault();
		
		$('.tingle-modal').remove();
		
		var modal = new tingle.modal({
			stickyFooter: false,
			closeMethods: ['overlay', 'button', 'escape']
		});

		modal.setContent('<div id="ja-widget-map-container"></div>');
		modal.open();
		
		objGencode.previewMap();
	});
});