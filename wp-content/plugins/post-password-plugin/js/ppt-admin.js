(function() {
	function cleanUrlParams() {
		var urlSearch = new URLSearchParams(window.location.search);
		["options-success", "salt-success", "algo-success"].forEach((param) => urlSearch.delete(param));
		window.history.replaceState({}, document.title, window.location.pathname + "?" + urlSearch.toString());
	}

	document.addEventListener("DOMContentLoaded", () => {
		cleanUrlParams();
		
		var lock_cancels = document.getElementsByClassName("lock-cancel");
		var ppt_unlocks = document.getElementsByClassName("ppt-unlock");
	
		for (advanced_input of document.getElementsByClassName("advanced-option-input")) {
			advanced_input.setAttribute("readonly", true);
			advanced_input.setAttribute("disabled", true);
		}
	
		for (let unlock of ppt_unlocks) {
			unlock.addEventListener("click", (e) => {
				e.preventDefault();
				e.currentTarget.classList.add("ppt-hide");
	
				var target_form = document.getElementById(
					e.currentTarget.getAttribute("href").replace("#","")
				);
				
				for (let element of target_form.elements) {
					if (element.classList.contains("advanced-option-input")) {
						element.removeAttribute("readonly");
						element.removeAttribute("disabled");
					}
				}
	
				target_form.getElementsByClassName("advanced-option-control")[0].classList.remove("ppt-hide");
			});
		}
	
		for (let lock_cancel of lock_cancels) {
			lock_cancel.addEventListener("click", (e) => {
				e.preventDefault();
	
				var target_form = document.getElementById(
					e.currentTarget.getAttribute("href").replace("#","")
				);
	
				target_form.getElementsByClassName("advanced-option-control")[0].classList.add("ppt-hide");
				target_form.getElementsByClassName("ppt-unlock")[0].classList.remove("ppt-hide");
	
				for (let element of target_form.elements) {
					if (element.classList.contains("advanced-option-input")) {
						element.setAttribute("readonly", true);
						element.setAttribute("disabled", true);
					}
				}
			});
		}
	});	
})();
