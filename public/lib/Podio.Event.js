(function (window, $, undefined) {

	var eventNamespace = '.podio';

	function onDomReady() {
		$('body').trigger(Podio.Event.Types.init + eventNamespace);
	}

	function bind(type, fn) {
		$('body').bind(type + eventNamespace, fn);
	}

	function unbind(type, fn) {
		$('body').unbind(type + eventNamespace, fn);
	}

	function trigger(type, data) {
		$('body').trigger(type + eventNamespace, data);
	}

	Podio.Event = {
		bind: bind,
		unbind: unbind,
		trigger: trigger,
		Types: {
			init: 'init'
		}
	};

	$(onDomReady);

})(window, jQuery);



