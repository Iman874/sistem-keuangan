@extends(auth()->user()->role==='admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title','Recurring Expenses')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between mb-3">
    <h1 class="h4 mb-0">Recurring Expenses</h1>
    <a href="{{ route('owner.recurring-expenses.create') }}" class="btn btn-primary btn-sm">Tambah</a>
  </div>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  <div class="card shadow">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>Nama</th><th>Jumlah</th><th>Jatuh Tempo Berikut</th><th>Terakhir Dibayar</th><th>Status</th><th>Reminders</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $e)
            <tr>
              <td>{{ $e->name }}</td>
              <td>Rp {{ number_format($e->amount,0,',','.') }}</td>
              <td>{{ $e->next_due_date->format('d/m/Y') }}</td>
              <td>{{ $e->last_paid_date ? $e->last_paid_date->format('d/m/Y') : '-' }}</td>
              <td><span class="badge badge-{{ $e->active ? 'success':'secondary' }}">{{ $e->active ? 'Aktif':'Nonaktif' }}</span></td>
              <td>{{ $e->reminders_sent }}/3</td>
              <td>
                <a href="{{ route('owner.recurring-expenses.edit',$e->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('owner.recurring-expenses.destroy',$e->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
