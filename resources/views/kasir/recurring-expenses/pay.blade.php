@extends('kasir.layouts.app')
@section('title','Bayar Rutin')
@section('content')
<div class="container-fluid">
  <h1 class="h4 mb-3">Bayar Rutin: {{ $expense->name }}</h1>
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ route('kasir.recurring-expenses.store',$expense->id) }}">
    @csrf
    <div class="form-group">
      <label>Jumlah (Rp)</label>
      <input type="text" inputmode="numeric" name="amount" class="form-control rupiah-input" value="{{ old('amount',$expense->amount) ? number_format((int)old('amount',$expense->amount),0,',','.') : '' }}" placeholder="0" required>
    </div>
    <div class="form-group">
      <label>Sesi</label>
      <select name="session" class="form-control" required>
        <option value="pagi">Pagi</option>
        <option value="sore">Sore</option>
      </select>
    </div>
    <div class="form-group">
      <label>Deskripsi (Opsional)</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Catatan tambahan"></textarea>
    </div>
    <button class="btn btn-primary">Bayar & Buat Invoice</button>
    <a href="{{ route('kasir.recurring-expenses.index') }}" class="btn btn-secondary">Batal</a>
  </form>
</div>
@endsection
