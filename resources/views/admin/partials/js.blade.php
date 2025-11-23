<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
<script>
// Global Rupiah input formatter for admin context as well
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
		try {
			var afterLen = formatted.length;
			var delta = afterLen - beforeLen;
			el.setSelectionRange((selStart||afterLen)+delta, (selStart||afterLen)+delta);
		} catch(_) {}
	}
	function onSubmit(e){
		try {
			var form = e.target;
			var els = form.querySelectorAll('.rupiah-input');
			els.forEach(function(input){
				if(input.disabled) return;
				input.value = (input.value||'').replace(/\D/g,'');
			});
		} catch(_) {}
	}
	document.addEventListener('input', onInput, true);
	document.addEventListener('submit', onSubmit, true);
})();
</script>
@if(auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('income.approve'))))
<script>
// Fetch unread count for notifications (simple polling)
function refreshNotifCount(){
	fetch("{{ route('admin.notifications.index') }}", { headers: { 'X-Requested-With':'XMLHttpRequest' }})
		.then(r=>r.text())
		.then(html=>{
			// naive parse: look for 'badge-danger' occurrences for unread markers in the HTML
			const count = (html.match(/badge-danger\">Baru/g) || []).length;
			const el = document.getElementById('notif-count');
			if(el){
				if(count>0){ el.style.display='inline-block'; el.textContent = count; }
				else { el.style.display='none'; }
			}
		}).catch(()=>{});
}
setTimeout(refreshNotifCount, 800);
setInterval(refreshNotifCount, 30000);
</script>
@endif
<script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
@yield('scripts')