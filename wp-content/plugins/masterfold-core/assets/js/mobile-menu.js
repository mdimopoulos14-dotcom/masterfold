/* Masterfold mobile menu: sliding multi-level panels. */
(function () {
	var EASE = 'transform 0.2s cubic-bezier(0.77, 0, 0.175, 1)';

	function init(root) {
		var toggle = root.querySelector('.mobile-menu-toggle');
		var menu = root.querySelector('.mobile-menu');
		var overlay = root.querySelector('.mobile-menu-overlay');
		var mainId = root.getAttribute('data-main');
		var stack = [mainId];

		// Move the panel to <body>: a CSS filter/transform on a header container
		// would otherwise make "position: fixed" relative to that container and
		// park the closed panel on screen.
		var portal = document.createElement('div');
		portal.className = 'mf-mobile-menu-root mf-mobile-menu-portal';
		portal.appendChild(overlay);
		portal.appendChild(menu);

		// Header styles (e.g. a colour filter) can change after load, so the
		// panel takes them over each time it opens.
		function syncFilter() {
			var filter = '';
			for (var el = root.parentElement; el && el !== document.body; el = el.parentElement) {
				var f = getComputedStyle(el).filter;
				if (f && f !== 'none') { filter = filter ? f + ' ' + filter : f; }
			}
			overlay.style.filter = filter;
			menu.style.filter = filter;
		}
		syncFilter();
		// Keep it first in the document, like the original header menu, so its
		// panel IDs (#reception, …) are found before anything else on the page.
		document.body.insertBefore(portal, document.body.firstChild);

		function panel(id) { return document.getElementById(id); }

		function slideIn(el, from) {
			el.classList.remove('hidden');
			el.classList.add('active');
			el.style.transform = from === 'right' ? 'translateX(100%)' : 'translateX(-100%)';
			requestAnimationFrame(function () { el.style.transform = 'translateX(0)'; });
		}

		function slideOut(el, to) {
			if (!el) { return; }
			el.classList.remove('hidden');
			el.offsetHeight; // reflow
			el.style.transition = EASE;
			el.style.transform = to === 'left' ? 'translateX(-100%)' : 'translateX(100%)';
			var done = function () {
				el.classList.add('hidden');
				el.classList.remove('active');
				el.removeEventListener('transitionend', done);
				clearTimeout(timer);
			};
			var timer = setTimeout(done, 600);
			el.addEventListener('transitionend', done);
		}

		function open() {
			syncFilter();
			menu.classList.add('open');
			overlay.classList.add('visible');
			slideIn(panel(mainId), 'right');
			stack = [mainId];
			menu.setAttribute('aria-hidden', 'false');
			toggle.setAttribute('aria-expanded', 'true');
		}

		function close() {
			menu.classList.remove('open');
			overlay.classList.remove('visible');
			menu.querySelectorAll('.menu-slide').forEach(function (el) {
				el.classList.add('hidden');
				el.classList.remove('active');
				el.style.transform = 'translateX(100%)';
			});
			stack = [mainId];
			menu.setAttribute('aria-hidden', 'true');
			toggle.setAttribute('aria-expanded', 'false');
		}

		function forward(id) {
			var current = menu.querySelector('.menu-slide.active');
			var target = panel(id);
			if (!target || current === target) { return; }
			slideOut(current, 'left');
			slideIn(target, 'right');
			stack.push(id);
		}

		function back() {
			if (stack.length <= 1) { return; }
			var current = panel(stack.pop());
			slideOut(current, 'right');
			slideIn(panel(stack[stack.length - 1]), 'left');
		}

		toggle.addEventListener('click', open);
		toggle.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
		});
		overlay.addEventListener('click', close);

		menu.addEventListener('click', function (e) {
			var link = e.target.closest('a[data-submenu]');
			if (link) { e.preventDefault(); forward(link.getAttribute('data-submenu')); return; }
			if (e.target.closest('.menu-back')) { back(); return; }
			if (e.target.closest('.menu-close')) { close(); }
		});
		menu.addEventListener('keydown', function (e) {
			if ((e.key === 'Enter' || e.key === ' ') && e.target.closest('.menu-back')) { e.preventDefault(); back(); }
		});

		document.addEventListener('click', function (e) {
			if (menu.classList.contains('open') && !menu.contains(e.target) && !toggle.contains(e.target)) { close(); }
		});
	}

	function boot() { document.querySelectorAll('.mf-mobile-menu-root').forEach(init); }
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
