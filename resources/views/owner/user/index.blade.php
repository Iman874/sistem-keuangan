@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">User Management</h1>
        @if(auth()->user() && method_exists(auth()->user(), 'hasPermission') && auth()->user()->hasPermission('users.create'))
        <a href="{{ route('owner.users.create') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New User
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Role Access & Permissions (Owner only) -->
    @if(auth()->check() && auth()->user()->role === 'owner')
    <div class="mb-4 shadow card">
        <div class="py-3 card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Role Access & Permissions</h6>
            <small class="text-muted">Hanya Owner yang dapat mengatur izin. Role Owner tidak dapat diubah.</small>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- OWNER (editable switches hidden: owner permissions fixed, but include as role target for bulk assign) -->
                <div class="mb-4 col-md-4">
                    <div class="h-100 border-left-success card">
                        <div class="text-white card-header bg-success d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Owner</span>
                            <span class="badge badge-light">Tingkat tertinggi</span>
                        </div>
                        <div class="p-3 card-body">
                            <p class="small text-muted mb-2">Semua izin aktif. Perubahan izin Owner tidak dapat dilakukan.</p>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fitur</th>
                                            <th class="text-center">Baca</th>
                                            <th class="text-center">Tambah</th>
                                            <th class="text-center">Ubah</th>
                                            <th class="text-center">Hapus</th>
                                            <th class="text-center">Export</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach([
                                            'Pemasukkan (Kasir)',
                                            'Pengeluaran (Kasir)',
                                            'Pemasukkan (Owner)',
                                            'Modal',
                                            'Gaji Karyawan',
                                            'Saldo Management',
                                            'User Management'
                                        ] as $feat)
                                        <tr>
                                            <td>{{ $feat }}</td>
                                            @for($i=0;$i<5;$i++)
                                            <td class="text-center">
                                                <input type="checkbox" checked disabled>
                                            </td>
                                            @endfor
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- No Save button for Owner: immutable full access -->
                        </div>
                    </div>
                </div>

                <!-- ADMIN -->
                <div class="mb-4 col-md-4">
                    <div class="h-100 border-left-primary card">
                        <div class="text-white card-header bg-primary d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Admin</span>
                            <div>
                                <label class="small mb-0 mr-2"><input type="checkbox" class="align-middle mr-1" id="mgr-select-all"> Pilih Semua</label>
                            </div>
                        </div>
                        <div class="p-3 card-body">
                            <div class="table-responsive">
                                <table class="table table-sm mb-3" id="admin-perms">
                                    <thead>
                                        <tr>
                                            <th>Fitur</th>
                                            <th class="text-center">Baca</th>
                                            <th class="text-center">Tambah</th>
                                            <th class="text-center">Ubah</th>
                                            <th class="text-center">Hapus</th>
                                            <th class="text-center">Export</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach([
                                            'income' => 'Pemasukkan (Kasir)',
                                            'expense' => 'Pengeluaran (Kasir)',
                                            'pemasukkan' => 'Pemasukkan (Owner)',
                                            'modal' => 'Modal',
                                            'salary' => 'Gaji Karyawan',
                                            'saldo' => 'Saldo Management',
                                            'users' => 'User Management'
                                        ] as $k=>$feat)
                                        <tr>
                                            <td>{{ $feat }}</td>
                                            <td class="text-center"><input type="checkbox" data-role="admin" data-key="{{ $k }}.read"></td>
                                            <td class="text-center"><input type="checkbox" data-role="admin" data-key="{{ $k }}.create"></td>
                                            <td class="text-center"><input type="checkbox" data-role="admin" data-key="{{ $k }}.update"></td>
                                            <td class="text-center"><input type="checkbox" data-role="admin" data-key="{{ $k }}.delete"></td>
                                            <td class="text-center"><input type="checkbox" data-role="admin" data-key="{{ $k }}.export"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-sm btn-primary" type="button" id="save-admin-perms"><i class="fas fa-save"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KASIR -->
                <div class="mb-4 col-md-4">
                    <div class="h-100 border-left-info card">
                        <div class="text-white card-header bg-info d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Kasir</span>
                            <div>
                                <label class="small mb-0 mr-2"><input type="checkbox" class="align-middle mr-1" id="cashier-select-all"> Pilih Semua</label>
                            </div>
                        </div>
                        <div class="p-3 card-body">
                            <div class="table-responsive">
                                <table class="table table-sm mb-3" id="cashier-perms">
                                    <thead>
                                        <tr>
                                            <th>Fitur</th>
                                            <th class="text-center">Baca</th>
                                            <th class="text-center">Tambah</th>
                                            <th class="text-center">Ubah</th>
                                            <th class="text-center">Hapus</th>
                                            <th class="text-center">Export</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['income'=>'Pemasukkan','expense'=>'Pengeluaran'] as $k=>$feat)
                                        <tr>
                                            <td>{{ $feat }}</td>
                                            <td class="text-center"><input type="checkbox" data-role="cashier" data-key="{{ $k }}.read"></td>
                                            <td class="text-center"><input type="checkbox" data-role="cashier" data-key="{{ $k }}.create"></td>
                                            <td class="text-center"><input type="checkbox" data-role="cashier" data-key="{{ $k }}.update"></td>
                                            <td class="text-center"><input type="checkbox" data-role="cashier" data-key="{{ $k }}.delete"></td>
                                            <td class="text-center"><input type="checkbox" data-role="cashier" data-key="{{ $k }}.export"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-sm btn-info" type="button" id="save-cashier-perms"><i class="fas fa-save"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="small text-muted"><i class="fas fa-info-circle"></i> Izin dimuat dari backend dan diterapkan ke semua user dalam role terkait.</div>
        </div>
    </div>
    @else
    <div class="mb-4 alert alert-info" role="alert">
        Pengaturan Role & Permissions hanya dapat diubah oleh Owner. Anda dapat melihat daftar pengguna di bawah ini sesuai izin Anda.
    </div>
    @endif

    <!-- DataTales Example -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->role == 'owner' ? 'success' : 'info' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                @if(auth()->user() && method_exists(auth()->user(), 'hasPermission') && auth()->user()->hasPermission('users.update'))
                                <a href="{{ route('owner.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
                                @if(auth()->user() && method_exists(auth()->user(), 'hasPermission') && auth()->user()->hasPermission('users.delete'))
                                <form action="{{ route('owner.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        function toggleAll(selectorTable, master) {
            const checked = $(master).is(':checked');
            $(selectorTable + ' input[type="checkbox"]').prop('checked', checked);
        }

        @if(auth()->check() && auth()->user()->role === 'owner')
        $('#mgr-select-all').on('change', function(){ toggleAll('#admin-perms', this); });
        $('#cashier-select-all').on('change', function(){ toggleAll('#cashier-perms', this); });

        // Load permissions strictly from backend for admin and kasir
        function applyPermsToTable(data, tableSelector){
            const perms = data && data.permissions ? data.permissions : {};
            $(tableSelector + ' input[type="checkbox"]').each(function(){
                const key = $(this).data('key');
                if(!key) return;
                const raw = perms[key];
                const isTrue = raw === true || raw === 'true';
                $(this).prop('checked', isTrue);
            });
        }

        $.get("{{ route('owner.permissions.show') }}", { role: 'admin' })
            .done(function(resp){ applyPermsToTable(resp, '#admin-perms'); })
            .fail(function(){ console.error('Gagal memuat izin admin dari backend'); });

        $.get("{{ route('owner.permissions.show') }}", { role: 'kasir' })
            .done(function(resp){ applyPermsToTable(resp, '#cashier-perms'); })
            .fail(function(){ console.error('Gagal memuat izin kasir dari backend'); });

        function openConfirm(role){
            $('#confirmPermissionRole').text(role === 'admin' ? 'Admin' : 'Kasir');
                        $('#confirmPermissionModal').data('role', role).modal('show');
                }

                $('#save-admin-perms').on('click', function(){ openConfirm('admin'); });
                $('#save-cashier-perms').on('click', function(){ openConfirm('kasir'); });

                $('#btnConfirmPermissionSave').on('click', function(){
                        const role = $('#confirmPermissionModal').data('role');
                        const table = role === 'admin' ? '#admin-perms' : '#cashier-perms';
                        const payload = {};
                        $(table + ' input[type="checkbox"]').each(function(){
                                const key = $(this).data('key');
                                if(!key) return; payload[key] = $(this).is(':checked');
                        });

                        $.ajax({
                                url: '{{ route('owner.permissions.update') }}',
                                method: 'POST',
                                data: {
                                        _token: '{{ csrf_token() }}',
                                        role: role === 'admin' ? 'admin' : 'kasir',
                                        permissions: payload
                                },
                success: function(){
                    $('#confirmPermissionModal').modal('hide');
                    // Reload from backend after save to ensure state reflects DB
                    $.get("{{ route('owner.permissions.show') }}", { role: role })
                        .done(function(resp){ applyPermsToTable(resp, table); });
                    toastr && toastr.success ? toastr.success('Izin '+ (role==='admin'?'Admin':'Kasir') +' berhasil disimpan.') : alert('Izin tersimpan.');
                },
                                error: function(xhr){
                                        $('#confirmPermissionModal').modal('hide');
                                        toastr && toastr.error ? toastr.error('Gagal menyimpan izin: '+xhr.status) : alert('Gagal menyimpan izin.');
                                }
                        });
        });
    @endif
    });
</script>
<!-- Confirm Permission Save Modal (reusing style of logout modal) -->
<div class="modal fade" id="confirmPermissionModal" tabindex="-1" role="dialog" aria-labelledby="confirmPermissionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPermissionLabel">Konfirmasi Penyimpanan Izin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Anda akan memperbarui konfigurasi izin untuk role <strong id="confirmPermissionRole"></strong>.<br>
                Tindakan ini akan diterapkan ke semua pengguna dengan role tersebut. Lanjutkan?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnConfirmPermissionSave">Ya, Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection