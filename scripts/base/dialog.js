(function (window, document) {
	'use strict';

	var queue = Promise.resolve();
	var allowedClicks = new WeakSet();
	var allowedForms = new WeakSet();

	function button(label, className) {
		var element = document.createElement('button');
		element.type = 'button';
		element.className = className;
		element.textContent = label;
		return element;
	}

	function bindConfirmToForm(confirm, form) {
		// Native submit button: F5 can show the browser's own "resubmit form" warning.
		var id = form.id;
		if (!id) {
			do {
				id = 'rs-dialog-form-' + Math.random().toString(36).slice(2, 10);
			} while (document.getElementById(id));
			form.id = id;
		}
		confirm.type = 'submit';
		confirm.setAttribute('form', id);
	}

	function enqueue(options) {
		var result = queue.then(function () {
			return show(options);
		});
		queue = result.catch(function () {});
		return result;
	}

	function apiForElement(element) {
		var frameName = element.getAttribute('data-rs-dialog-frame');
		if (!frameName) {
			return RisistarDialog;
		}
		try {
			var frame = window.parent.frames[frameName];
			if (frame && frame.RisistarDialog) {
				return frame.RisistarDialog;
			}
		} catch (e) {}
		return RisistarDialog;
	}

	function show(options) {
		if (!document.body) {
			return new Promise(function (resolve) {
				document.addEventListener('DOMContentLoaded', function () {
					show(options).then(resolve);
				}, {once: true});
			});
		}
		return new Promise(function (resolve) {
			var previousFocus = document.activeElement;
			var root = document.createElement('div');
			var backdrop = document.createElement('div');
			var box = document.createElement('div');
			var message = document.createElement('p');
			var input = null;
			var checkbox = null;
			var cancel = null;
			var confirm;
			var actions = document.createElement('div');
			var closed = false;

			root.className = 'rs-dialog';
			backdrop.className = 'rs-dialog-backdrop';
			box.className = 'rs-dialog-box';
			box.setAttribute('role', 'dialog');
			box.setAttribute('aria-modal', 'true');
			message.className = 'rs-dialog-title';
			message.id = 'rsDialogTitle';
			message.textContent = String(options.message || '');
			box.setAttribute('aria-labelledby', message.id);
			box.appendChild(message);

			if (options.input) {
				input = document.createElement('input');
				input.className = 'rs-dialog-input';
				input.type = 'text';
				input.value = options.defaultValue == null ? '' : String(options.defaultValue);
				input.setAttribute('aria-label', String(options.message || ''));
				box.appendChild(input);
			}

			if (options.checkboxLabel) {
				var option = document.createElement('label');
				option.className = 'rs-dialog-opt';
				checkbox = document.createElement('input');
				checkbox.type = 'checkbox';
				option.appendChild(checkbox);
				option.appendChild(document.createTextNode(' ' + options.checkboxLabel));
				box.appendChild(option);
			}

			actions.className = 'rs-dialog-actions';
			if (options.cancelLabel) {
				cancel = button(options.cancelLabel, 'rs-dialog-cancel');
				actions.appendChild(cancel);
			}
			confirm = button(
				options.confirmLabel || 'OK',
				options.danger ? 'rs-dialog-confirm rs-dialog-danger' : 'rs-dialog-confirm'
			);
			if (options.submitForm) {
				bindConfirmToForm(confirm, options.submitForm);
			}
			actions.appendChild(confirm);
			box.appendChild(actions);
			root.appendChild(backdrop);
			root.appendChild(box);
			document.body.appendChild(root);
			document.body.classList.add('rs-dialog-open');

			function finish(confirmed) {
				if (closed) {
					return;
				}
				closed = true;
				document.removeEventListener('keydown', onKeyDown, true);
				if (root.parentNode) {
					root.parentNode.removeChild(root);
				}
				document.body.classList.remove('rs-dialog-open');
				if (previousFocus && typeof previousFocus.focus === 'function' && document.contains(previousFocus)) {
					previousFocus.focus();
				}
				var result = {
					confirmed: confirmed,
					checked: checkbox ? checkbox.checked : false,
					value: input ? input.value : null
				};
				resolve(result);
			}

			function onKeyDown(event) {
				if (event.key === 'Escape') {
					event.preventDefault();
					finish(false);
					return;
				}
				if (event.key === 'Enter' && input && event.target === input) {
					event.preventDefault();
					finish(true);
					return;
				}
				if (event.key !== 'Tab') {
					return;
				}
				var focusable = box.querySelectorAll('button, input');
				if (!focusable.length) {
					return;
				}
				var first = focusable[0];
				var last = focusable[focusable.length - 1];
				if (event.shiftKey && document.activeElement === first) {
					event.preventDefault();
					last.focus();
				} else if (!event.shiftKey && document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}

			confirm.addEventListener('click', function (event) {
				var form = options.submitForm;
				if (!form) {
					finish(true);
					return;
				}
				if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
					event.preventDefault();
					if (typeof form.reportValidity === 'function') {
						form.reportValidity();
					}
					return;
				}
				allowedForms.add(form);
				setTimeout(function () {
					finish(true);
				}, 0);
			});
			if (cancel) {
				cancel.addEventListener('click', function () {
					finish(false);
				});
			}
			backdrop.addEventListener('click', function () {
				finish(!options.cancelLabel);
			});
			document.addEventListener('keydown', onKeyDown, true);

			if (input) {
				input.focus();
				input.select();
			} else {
				confirm.focus();
			}
		});
	}

	var RisistarDialog = {
		alert: function (message, options) {
			options = options || {};
			return enqueue({
				message: message,
				confirmLabel: options.confirmLabel || 'OK',
				danger: false
			}).then(function () {
				return true;
			});
		},

		confirm: function (message, options) {
			options = options || {};
			return enqueue({
				message: message,
				confirmLabel: options.confirmLabel || 'Confirmer',
				cancelLabel: options.cancelLabel || 'Annuler',
				checkboxLabel: options.checkboxLabel || '',
				danger: options.danger !== false,
				submitForm: options.submitForm || null
			});
		},

		prompt: function (message, defaultValue, options) {
			options = options || {};
			return enqueue({
				message: message,
				defaultValue: defaultValue,
				input: true,
				confirmLabel: options.confirmLabel || 'Confirmer',
				cancelLabel: options.cancelLabel || 'Annuler',
				danger: false
			}).then(function (result) {
				return result.confirmed ? result.value : null;
			});
		}
	};

	document.addEventListener('click', function (event) {
		var target = event.target.closest ? event.target.closest('[data-rs-confirm]') : null;
		if (!target || allowedClicks.has(target)) {
			if (target) {
				allowedClicks.delete(target);
			}
			return;
		}
		if (target.tagName === 'FORM') {
			return;
		}
		if ((target.tagName === 'BUTTON' || target.tagName === 'INPUT') && target.form) {
			return;
		}
		event.preventDefault();
		event.stopImmediatePropagation();
		apiForElement(target).confirm(target.getAttribute('data-rs-confirm'), {
			confirmLabel: target.getAttribute('data-rs-confirm-label') || 'Confirmer',
			cancelLabel: target.getAttribute('data-rs-cancel-label') || 'Annuler'
		}).then(function (result) {
			if (!result.confirmed || !document.contains(target)) {
				return;
			}
			allowedClicks.add(target);
			target.click();
		});
	}, true);

	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (!form.matches || !form.matches('form[data-rs-confirm]')) {
			return;
		}
		if (allowedForms.has(form)) {
			allowedForms.delete(form);
			return;
		}
		event.preventDefault();
		event.stopImmediatePropagation();
		var submitter = event.submitter || null;
		var extraSubmitter = null;
		if (submitter && submitter.name && !submitter.disabled) {
			extraSubmitter = document.createElement('input');
			extraSubmitter.type = 'hidden';
			extraSubmitter.name = submitter.name;
			extraSubmitter.value = submitter.value;
			form.appendChild(extraSubmitter);
		}
		RisistarDialog.confirm(form.getAttribute('data-rs-confirm'), {
			confirmLabel: form.getAttribute('data-rs-confirm-label') || 'Confirmer',
			cancelLabel: form.getAttribute('data-rs-cancel-label') || 'Annuler',
			submitForm: form
		}).then(function (result) {
			if (!result.confirmed && extraSubmitter && extraSubmitter.parentNode) {
				extraSubmitter.parentNode.removeChild(extraSubmitter);
			}
		});
	}, true);

	window.RisistarDialog = RisistarDialog;
})(window, document);
