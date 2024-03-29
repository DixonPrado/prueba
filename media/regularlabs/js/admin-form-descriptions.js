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

	window.RegularLabs.AdminFormDescriptions = window.RegularLabs.AdminFormDescriptions || {
		move: function() {
			document.querySelectorAll('div[id$="-desc"]:not(.rl-moved)').forEach((description) => {
				const control_group = description.closest('.control-group');

				if ( ! control_group) {
					return;
				}

				const label = control_group.querySelector('label');

				if ( ! label) {
					return;
				}

				description.classList.add('hidden');
				description.classList.add('rl-moved');

				const controls = control_group.querySelector('.controls');

				const popover       = document.createElement('div');
				const popover_inner = document.createElement('div');

				popover.classList.add('rl-admin-popover-container');
				popover_inner.classList.add('rl-admin-popover');
				popover_inner.innerHTML = description.querySelector('small').innerHTML;

				popover.append(popover_inner);

				const button = document.createElement('span');
				button.classList.add('icon-info-circle', 'text-muted', 'fs-6', 'ms-1', 'align-text-top');

				label.setAttribute('role', 'button');
				label.setAttribute('tabindex', '0');

				const action_show = function() {
					popover.classList.add('show');
				};
				const action_hide = function() {
					popover.classList.remove('show');
				};

				label.addEventListener('mouseenter', action_show);
				label.addEventListener('mouseleave', action_hide);
				label.addEventListener('focus', action_show);
				label.addEventListener('blur', action_hide);

				label.append(button);
				controls.insertBefore(popover, controls.firstChild);
			});
		}
	}

	RegularLabs.AdminFormDescriptions.move();

	document.addEventListener('subform-row-add', () => {
		RegularLabs.AdminFormDescriptions.move();
	});
})();

