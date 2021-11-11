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

	window.RegularLabs.DownloadKey = window.RegularLabs.DownloadKey || {
		init: function() {
			const downloadKeys = document.querySelectorAll('div.rl-download-key');

			downloadKeys.forEach((container) => {
				const spinner      = container.querySelector('span.rl-spinner');
				const inputField   = container.querySelector('input.rl-code-field');
				const editButton   = container.querySelector('.button-edit');
				const applyButton  = container.querySelector('.button-apply');
				const cancelButton = container.querySelector('.button-cancel');
				const emptyError   = container.querySelector('.key-error-empty');
				const localError   = container.querySelector('.key-error-local');
				const modal        = container.querySelector(`#downloadKeyModal_${inputField.id}`);
				const extension    = inputField.dataset['keyExtension'];

				let key = '';

				if (modal) {
					// Move modal to end of body, to prevent it getting hidden if inside hidden tab
					document.body.appendChild(modal);

					modal.addEventListener('shown.bs.modal', () => {
						const modalInputField = modal.querySelector('input.rl-download-key-field');
						modalInputField.focus();
					});
				}

				const getKey = async function() {
					const url = 'index.php?option=com_ajax&plugin=regularlabs&format=raw&getDownloadKey=1';

					const response = await RegularLabs.Scripts.loadUrl(url);

					if (response.status !== 200) {
						handleGetKeyFail();
						return;
					}

					handleGetKeySuccess(response.responseText);
				};

				const handleGetKeySuccess = async function(data) {
					if ( ! data.match(/^[a-z0-9]*$/i)) {
						handleGetKeyFail();
						return;
					}

					key = data;

					await checkDownloadKey();

					reset();

					addListeners();
				};

				const handleGetKeyFail = function() {
					localError.classList.remove('hidden');

					showModal();
				};

				const checkDownloadKey = async function() {
					if ( ! key.length) {
						emptyError.classList.remove('hidden');

						showModal();
						return;
					}

					const result = await RegularLabs.DownloadKey.check(extension, key, container, false);

					if (['empty', 'invalid'].indexOf(result.error) > -1) {
						showModal();
					}
				};

				const showModal = function() {
					if ( ! modal) {
						return;
					}

					RegularLabs.DownloadKey.check(extension, key, modal);

					if (window.bootstrap && window.bootstrap.Modal && ! window.bootstrap.Modal.getInstance(modal)) {
						Joomla.initialiseModal(modal, {
							isJoomla: true
						});
					}

					window.bootstrap.Modal.getInstance(modal).show();
				};

				const addListeners = function() {
					inputField.addEventListener('focus', selectField);
					inputField.addEventListener('keydown', handleKeyPressField);
					document.addEventListener('mousedown', deselectField);
					editButton.addEventListener('click', () => {
						inputField.focus();
					});
					applyButton.addEventListener('click', clickSave);
					cancelButton.addEventListener('click', clickCancel);
				};

				const selectField = function() {
					inputField.value = '';
					inputField.classList.remove('inactive');
					editButton.classList.add('hidden');
					applyButton.classList.remove('hidden');
					cancelButton.classList.remove('hidden');
				};

				const handleKeyPressField = function(event) {
					switch (event.keyCode) {
						case 13: // ENTER
							save();
							break;

						case 27: // ESC
							cancel();
							break;

						default:
							break;
					}
				};

				const deselectField = function(event) {
					if (event.target.closest('div.rl-download-key') === container) {
						return;
					}

					if (inputField.classList.contains('inactive')) {
						return;
					}

					cancel();
				};

				const clickSave = function(event) {
					event.preventDefault();
					save();
				};

				const clickCancel = function(event) {
					event.preventDefault();
					cancel();
				};

				const save = async function() {
					const saved = await RegularLabs.DownloadKey.save(extension, inputField.value, container);

					if ( ! saved) {
						return;
					}

					key = inputField.value;

					reset();
				};

				const cancel = async function() {
					reset();

					RegularLabs.DownloadKey.resetErrors(container);
					RegularLabs.DownloadKey.check(extension, key, container);
				};

				const reset = function() {
					inputField.value = cloak(key);

					inputField.blur();
					spinner.classList.add('hidden');
					inputField.classList.remove('hidden');
					inputField.classList.add('inactive');
					editButton.classList.remove('hidden');
					applyButton.classList.add('hidden');
					cancelButton.classList.add('hidden');
				};

				const cloak = function(string) {
					return RegularLabs.DownloadKey.cloak(string, inputField.dataset['keyCloakLength']);
				};

				getKey();
			});
		},

		cloak: function(string, cloakLength = 4) {
			if (string.length <= cloakLength) {
				return string;
			}

			return "*".repeat(string.length - cloakLength) + string.substr(-cloakLength);
		},

		showError: function(type, element, focus = true) {
			element.querySelector(`.key-error-${type}`) && element.querySelector(`.key-error-${type}`).classList.remove('hidden');

			if ( ! focus) {
				return;
			}

			const inputField = element.querySelector('input.rl-download-key-field');

			inputField.classList.add('invalid');
			inputField.click();
		},

		resetErrors: function(element) {
			const inputField = element.querySelector('input.rl-download-key-field');

			inputField.classList.remove('invalid');

			element.querySelectorAll('[class*="key-error-"]').forEach((error) => {
				error.classList.add('hidden');
			});
		},

		save: function(extension, key, element) {
			return new Promise(async (resolve) => {

				const result = await RegularLabs.DownloadKey.check(extension, key, element);

				if ( ! result.pass) {
					resolve(false);
					return;
				}

				await RegularLabs.DownloadKey.store(extension, key);

				if (window.bootstrap.Modal.getInstance(element)) {
					const mainId      = element.id.replace('downloadKeyModal_', 'downloadKeyWrapper_');
					const mainElement = document.querySelector(`#${mainId}`);
					RegularLabs.DownloadKey.resetErrors(mainElement);
					await RegularLabs.DownloadKey.check(extension, key, mainElement);
					window.bootstrap.Modal.getInstance(element).hide();
				} else {
					RegularLabs.DownloadKey.resetErrors(element);
					await RegularLabs.DownloadKey.check(extension, key, element);
				}

				resolve(true);
			});
		},

		check: function(extension, key, element, focus = true) {
			return new Promise(async (resolve) => {
				const url        = `index.php?option=com_ajax&plugin=regularlabs&format=raw&checkDownloadKey=1&extension=${extension}&key=${key}`;
				const inputField = element.querySelector('input.rl-download-key-field');

				RegularLabs.DownloadKey.resetErrors(element);

				const result = {pass: false, error: ''};

				if ( ! key) {
					result.error = 'empty';
					RegularLabs.DownloadKey.showError(result.error, element, focus);

					resolve(result);
					return;
				}

				const response = await RegularLabs.Scripts.loadUrl(url);

				if (response.status !== 200) {
					result.error = 'local';
					RegularLabs.DownloadKey.showError(result.error, element, false);

					resolve(result);
					return;
				}

				if ( ! response.responseText || response.responseText.charAt(0) !== '{') {
					result.error = 'external';
					RegularLabs.DownloadKey.showError(result.error, element, false);

					resolve(result);
					return;
				}

				const data = JSON.parse(response.responseText);

				if ( ! data.valid) {
					result.error = 'invalid';
					RegularLabs.DownloadKey.showError(result.error, element, focus);

					resolve(result);
					return;
				}

				const is_modal = element.id.indexOf('downloadKeyModal_') > -1;

				if ( ! data.active && is_modal) {
					result.error = 'expired';
					RegularLabs.DownloadKey.showError(result.error, element, focus);

					resolve(result);
					return;
				}

				if ( ! data.active) {
					RegularLabs.DownloadKey.showError('expired', element, false);
				}

				inputField.value = RegularLabs.DownloadKey.cloak(key);

				result.pass = true;
				resolve(result);
			});
		},

		store: function(extension, key) {
			const url = `index.php?option=com_ajax&plugin=regularlabs&format=raw&saveDownloadKey=1&key=${key}`;
			return RegularLabs.Scripts.loadUrl(url);
		},
	};

	RegularLabs.DownloadKey.init();
})();
