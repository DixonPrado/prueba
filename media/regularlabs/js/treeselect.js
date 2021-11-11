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

	window.RegularLabs.TreeSelect = window.RegularLabs.TreeSelect || {
		direction: (document.dir !== undefined) ? document.dir : document.getElementsByTagName("html")[0].getAttribute("dir"),

		init: function(id) {
			const menu = document.querySelector('div#rl-treeselect-' + id);

			if ( ! menu) {
				return;
			}

			const list             = menu.querySelector('ul');
			const top_level_items  = list.querySelectorAll(':scope > li');
			const items            = list.querySelectorAll('li');
			const search_field     = menu.querySelector('[name="treeselectfilter"]');
			const sub_tree_select  = menu.querySelector('div.sub-tree-select > *');
			const no_results_found = menu.querySelector('joomla-alert');

			items.forEach((item) => {
				// Store the innerText for filtering
				// because if done later, also the text from added menus and buttons is added
				item.text = item.innerText;
			});

			items.forEach((item) => {
				const child_list = item.querySelector('ul.treeselect-sub');

				if ( ! child_list) {
					return;
				}

				const label = item.querySelector('label');

				const sub_tree_select_el = sub_tree_select.cloneNode(true);

				const sub_tree_expand     = document.createElement('span');
				sub_tree_expand.className = 'treeselect-toggle icon-chevron-down';
				sub_tree_expand.collapsed = false;

				sub_tree_expand.addEventListener('click', () => {
					this.expand(child_list, sub_tree_expand);
				});

				sub_tree_select_el.querySelector('[data-action="checkNested"]').addEventListener('click', () => {
					this.check(child_list, true);
				});

				sub_tree_select_el.querySelector('[data-action="uncheckNested"]').addEventListener('click', () => {
					this.check(child_list, false);
				});

				sub_tree_select_el.querySelector('[data-action="toggleNested"]').addEventListener('click', () => {
					this.check(child_list, 'toggle');
				});

				item.insertBefore(sub_tree_expand, item.firstChild);
				label.append(sub_tree_select_el);
			});

			menu.querySelector('[data-action="checkAll"]').addEventListener('click', () => {
				this.check(menu, true);
			});
			menu.querySelector('[data-action="uncheckAll"]').addEventListener('click', () => {
				this.check(menu, false);
			});
			menu.querySelector('[data-action="toggleAll"]').addEventListener('click', () => {
				this.check(menu, 'toggle');
			});

			menu.querySelector('[data-action="expandAll"]').addEventListener('click', () => {
				top_level_items.forEach((item) => {
					const child_list      = item.querySelector('ul.treeselect-sub');
					const sub_tree_expand = item.querySelector('.treeselect-toggle');
					if ( ! child_list || ! sub_tree_expand) {
						return;
					}
					sub_tree_expand.collapsed = true;
					this.expand(child_list, sub_tree_expand);
				});
			});

			menu.querySelector('[data-action="collapseAll"]').addEventListener('click', () => {
				top_level_items.forEach((item) => {
					const child_list      = item.querySelector('ul.treeselect-sub');
					const sub_tree_expand = item.querySelector('.treeselect-toggle');
					if ( ! child_list || ! sub_tree_expand) {
						return;
					}
					sub_tree_expand.collapsed = false;
					this.expand(child_list, sub_tree_expand);
				});
			});
			menu.querySelector('[data-action="showAll"]').addEventListener('click', () => {
				this.resetSearch(items, search_field, no_results_found);
			});
			menu.querySelector('[data-action="showSelected"]').addEventListener('click', (e) => {
				this.resetSearch(items, search_field, no_results_found, true);
			});

			// Takes care of the filtering
			search_field.addEventListener('keyup', () => {
				this.doSearch(items, search_field, no_results_found);
			});
		},

		resetSearch: function(items, search_field, no_results_found, has_checked) {
			search_field.value = '';
			this.doSearch(items, search_field, no_results_found, has_checked);
		},

		doSearch: function(items, search_field, no_results_found, has_checked) {
			const text = search_field.value.toLowerCase();

			no_results_found.style.display = 'none';

			let results_found = 0;

			items.forEach((item) => {
				if (has_checked && ! item.querySelector('input:checked')) {
					item.style.display = 'none';
					return;
				}

				if (text !== '') {
					let item_text = item.text.toLowerCase();
					item_text     = item_text.replace(/\s+/g, ' ').trim();

					if (item_text.indexOf(text) == -1) {
						item.style.display = 'none';
						return;
					}
				}

				results_found++;
				item.style.display = 'block';
			});

			if ( ! results_found) {
				no_results_found.style.display = 'block';
			}
		},

		check: function(parent, checked) {
			const items = parent.querySelectorAll('li');

			items.forEach((item) => {
				if (item.style.display === 'none') {
					return;
				}

				const checkbox = item.querySelector(':scope > .treeselect-item input:enabled');

				if ( ! checkbox) {
					return;
				}

				checkbox.checked = checked === 'toggle' ? ! checkbox.checked : checked;
			});
		},

		expand: function(element, button) {
			const show = button.collapsed;

			element.style.display = show ? 'block' : 'none';

			button.classList.toggle('icon-chevron-down', show);
			button.classList.toggle(this.direction === 'rtl' ? 'icon-chevron-left' : 'icon-chevron-right', ! show);

			button.collapsed = ! button.collapsed;

			if ( ! show) {
				return;
			}

			const child_lists = element.querySelectorAll(':scope > li > ul.treeselect-sub');

			if ( ! child_lists.length) {
				return;
			}

			child_lists.forEach((child_list) => {
				const child_button     = child_list.closest('li').querySelector('.treeselect-toggle');
				child_button.collapsed = true;
				this.expand(child_list, child_button);
			});

		}
	};
})();
