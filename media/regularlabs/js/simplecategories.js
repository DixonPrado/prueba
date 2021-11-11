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

	const simplecategories = document.querySelectorAll('div.rl_simplecategory');

	simplecategories.forEach((simplecategory) => {
		const select      = simplecategory.querySelector('.rl_simplecategory_select select');
		const input       = simplecategory.querySelector('.rl_simplecategory_new input');
		const value_field = simplecategory.querySelector('.rl_simplecategory_value');

		var func = function() {
			if (select.value !== '-1') {
				return;
			}

			value_field.value = input.value;
		};

		select.addEventListener('change', func);
		select.addEventListener('keyup', func);
		input.addEventListener('change', func);
		input.addEventListener('keyup', func);
	});
})();
