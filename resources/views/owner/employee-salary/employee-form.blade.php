@extends('owner.layouts.app')

@section('title', ($employee->id??false)?'Edit Karyawan':'Tambah Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ ($employee->id??false)?'Edit':'Tambah' }} Karyawan</h1>
        <a href="{{ route('owner.employee-salary.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ ($employee->id??false)?route('owner.employee-salary.updateEmployee',$employee->id):route('owner.employee-salary.storeEmployee') }}">
                @csrf
                @if(($employee->id??false)) @method('PUT') @endif

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name ?? '') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email ?? '') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>No. HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Jabatan / Posisi</label>
                        <input type="text" name="role" class="form-control" value="{{ old('role', $employee->role ?? '') }}" placeholder="Contoh: Staff, Marketing" required>
                    </div>
                    @if($employee->id)
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select name="active" class="form-control">
                            <option value="1" {{ old('active', ($employee->active ?? 1))? 'selected':'' }}>Aktif</option>
                            <option value="0" {{ old('active', ($employee->active ?? 1))? '':'selected' }}>Nonaktif</option>
                        </select>
                    </div>
                    @else
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <input type="text" class="form-control" value="Aktif" disabled>
                        <input type="hidden" name="active" value="1">
                    </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Gaji Pokok (Rp)</label>
                        <input type="text" name="base_salary" class="form-control rupiah-input" value="{{ number_format((int)old('base_salary', $employee->base_salary ?? 0),0,',','.') }}" inputmode="numeric" placeholder="0" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($employee->start_date ?? null)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Tanggal Keluar</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', optional($employee->end_date ?? null)->format('Y-m-d')) }}" {{ old('is_permanent', $employee->is_permanent ?? false) ? 'disabled' : '' }}>
                        <div class="mt-2">
                            <label class="small mb-0"><input type="checkbox" name="is_permanent" id="is_permanent" value="1" {{ old('is_permanent', $employee->is_permanent ?? false) ? 'checked' : '' }}> Pegawai tetap / Tidak ditentukan</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $employee->notes ?? '') }}</textarea>
                </div>

                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        var chk = document.getElementById('is_permanent');
        var end = document.getElementById('end_date');
        if(chk && end){
            chk.addEventListener('change', function(){
                if(this.checked){ end.value=''; end.setAttribute('disabled','disabled'); }
                else { end.removeAttribute('disabled'); }
            });
        }
    });
</script>
@endpush
