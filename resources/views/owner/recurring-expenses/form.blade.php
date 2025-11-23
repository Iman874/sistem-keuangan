@extends(auth()->user()->role==='admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title','Recurring Expense Form')
@section('content')
<div class="container-fluid">
  <h1 class="h4 mb-3">{{ isset($expense) ? 'Edit' : 'Tambah' }} Recurring Expense</h1>
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($expense) ? route('owner.recurring-expenses.update',$expense->id) : route('owner.recurring-expenses.store') }}">
    @csrf
    @if(isset($expense)) @method('PUT') @endif
    <div class="form-group">
      <label>Nama</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$expense->name ?? '') }}" required>
    </div>
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description',$expense->description ?? '') }}</textarea>
    </div>
    <div class="form-group">
      <label>Jumlah (Rp)</label>
      <input type="text" inputmode="numeric" name="amount" class="form-control rupiah-input" value="{{ old('amount',$expense->amount ?? '') ? number_format((int)old('amount',$expense->amount ?? ''),0,',','.') : '' }}" placeholder="0" required>
    </div>
    <div class="form-group">
      <label>Jatuh Tempo Berikut</label>
      <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date', isset($expense)? $expense->next_due_date->format('Y-m-d'): now()->addMonth()->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group form-check">
      <input type="checkbox" name="active" class="form-check-input" id="activeCheck" {{ old('active',$expense->active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="activeCheck">Aktif</label>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('owner.recurring-expenses.index') }}" class="btn btn-secondary">Batal</a>
  </form>
</div>
@endsection
