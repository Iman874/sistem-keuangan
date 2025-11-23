<!-- Kode untuk menampilkan waktu pengeluaran -->
<span class="badge badge-dark">{{ $expense->time ? \Carbon\Carbon::parse($expense->time)->format('H:i') : 'N/A' }}</span>
