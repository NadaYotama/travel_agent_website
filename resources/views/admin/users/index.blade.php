@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('header', 'Pengguna')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Daftar Akun Pengguna</h4>
                        <p class="text-muted font-13 mb-0">Kelola akses akun admin, agen wisata, mitra vendor, dan pelanggan.</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-account-plus m-r-5"></i> Tambah Pengguna
                        </a>
                    </div>
                </div>

                <!-- Form Filter & Pencarian -->
                <form action="{{ route('admin.users.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <div class="row align-items-center">
                        <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Cari Nama / Email:</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau email..." class="form-control">
                        </div>

                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Filter Peran (Role):</label>
                            <select name="role" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Peran --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>
                                        {{ ucfirst($r->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-12 text-md-right mt-2 mt-md-0">
                            <button type="submit" class="btn btn-cyan waves-effect m-r-5">
                                <i class="mdi mdi-magnify"></i> Cari
                            </button>
                            @if(request()->hasAny(['q', 'role']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Tabel Pengguna -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th class="text-center">Peran (Role)</th>
                                <th class="text-center">Status Akun</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $idx => $u)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $users->firstItem() + $idx }}
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark font-14">
                                        {{ $u->name }}
                                        @if($u->id === auth()->id())
                                            <span class="badge badge-light border text-cyan font-11 ml-1">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted">{{ $u->email }}</td>
                                    <td class="align-middle">{{ $u->phone ?: '-' }}</td>
                                    <td class="text-center align-middle">
                                        @php
                                            $roleVal = is_object($u->role) ? $u->role->value : $u->role;
                                        @endphp
                                        @if($roleVal === 'admin')
                                            <span class="badge badge-danger p-2 font-12"><i class="mdi mdi-shield-account m-r-5"></i>Admin</span>
                                        @elseif($roleVal === 'agent')
                                            <span class="badge badge-warning text-dark p-2 font-12"><i class="mdi mdi-account-tie m-r-5"></i>Agen</span>
                                        @elseif($roleVal === 'vendor')
                                            <span class="badge badge-info p-2 font-12"><i class="mdi mdi-domain m-r-5"></i>Vendor</span>
                                        @else
                                            <span class="badge badge-secondary p-2 font-12"><i class="mdi mdi-account m-r-5"></i>Customer</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($u->is_active)
                                            <span class="badge badge-success p-2">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary p-2">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-cyan btn-sm" title="Edit Pengguna">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if($u->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengguna">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-off font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada pengguna ditemukan</h5>
                                            <p class="mb-3">Tidak ada akun yang sesuai dengan kriteria pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center m-t-20">
                    <div class="text-muted font-13">
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

