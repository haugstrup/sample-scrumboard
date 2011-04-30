Function.prototype.debounce = function debounce(threshold, execAsap) {
	/// From http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var func = this, timeout;

	return function debounced() {
		var obj = this, args = arguments;
		function delayed() {
			if (!execAsap) func.apply(obj, args);
			timeout = null;
		};

		if (timeout) clearTimeout(timeout);
		else if (execAsap) func.apply(obj, args);

		timeout = setTimeout(delayed, threshold || 100);
	};

};

Function.prototype.curry = function curry() {
	var fn = this, args = Array.prototype.slice.call(arguments);
	return function curryed() {
		return fn.apply(this, args.concat(Array.prototype.slice.call(arguments)));
	};
};

Function.prototype.curryAfter = function curryAfter() {
	var fn = this, args = Array.prototype.slice.call(arguments);
	return function curryedAfter() {
		return fn.apply(this, Array.prototype.slice.call(arguments).concat(args));
	};
};

Function.prototype.throttle = function throttle(delay) {
	var func = this, timeOfLastExec = 0, execWaiting = false;

	return function throttled() {
		var obj = this, args = arguments,	timeSinceLastExec = new Date().getTime() - timeOfLastExec;
		if (timeSinceLastExec > delay) {
			func.apply(obj, args);
			execWaiting = false;
			timeOfLastExec = new Date().getTime();
		}
		else if (!execWaiting) {
			execWaiting = setTimeout(function() {
				func.apply(obj, args);
				execWaiting = false;
				timeOfLastExec = new Date().getTime();
			}, delay - timeSinceLastExec);
		}
	};
};