<!-- Bootstrap core JavaScript-->
<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

<!-- Page level plugins -->
<script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
// Global Rupiah input formatter: add thousand separators while typing
(function(){
	function formatNumberId(numStr){
		if(!numStr) return '';
		var s = numStr.replace(/\D/g,'');
		if(!s) return '';
		return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
	}
	function onInput(e){
		var el = e.target;
		if(!el.classList || !el.classList.contains('rupiah-input')) return;
		var selStart = el.selectionStart;
		var beforeLen = (el.value||'').length;
		var formatted = formatNumberId(el.value||'');
		el.value = formatted;
		// best-effort caret: move to end when formatting changes
		try {
			var afterLen = formatted.length;
			var delta = afterLen - beforeLen;
			el.setSelectionRange((selStart||afterLen)+delta, (selStart||afterLen)+delta);
		} catch(_) { /* ignore */ }
	}
	function onSubmit(e){
		try {
			var form = e.target;
			var els = form.querySelectorAll('.rupiah-input');
			els.forEach(function(input){
				if(input.disabled) return;
				input.value = (input.value||'').replace(/\D/g,'');
			});
		} catch(_) { /* ignore */ }
	}
	document.addEventListener('input', onInput, true);
	document.addEventListener('submit', onSubmit, true);
})();
</script>

@yield('scripts')