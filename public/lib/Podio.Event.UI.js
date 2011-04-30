(function (window, $, undefined) {

	function proxy_callback(callback, shouldPreserveDefault, e){
		var target = $(e.currentTarget);
    if(!shouldPreserveDefault) {
		  e.preventDefault();
    }
		callback.apply(this, [target].concat(Array.prototype.slice.call(arguments).slice(2)));
	}

	function bind(eventType, selector, fn, shouldPreserveDefault) {
		$('body').delegate(selector, eventType, proxy_callback.curry(fn, shouldPreserveDefault));
	}

	function unbind(eventType, selector) {
		$('body').undelegate(selector, eventType);
	}

	/** expose external methods **/
	Podio.Event.UI = {
		bind: bind,
		unbind: unbind
	};

})(window, jQuery);