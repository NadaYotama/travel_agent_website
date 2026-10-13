@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')
@section('header', 'Tambah Pengguna')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('admin.users.store') }}" method="POST" class="form-horizontal">
                @csrf
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Form Pembuatan Akun Baru</h4>
                            <small class="text-muted">Tambahkan akun baru dengan memilih peran (Admin, Agen, Vendor, Customer).</small>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm waves-effect">
                                <i class="mdi mdi-arrow-left m-r-5"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Periksa isian formulir:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="form-group row">
                        <label for="name" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Nama pengguna..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="email@contoh.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            No. Telepon / WhatsApp
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="08123456789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="role" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Peran Akun <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">-- Pilih Peran --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->value }}" {{ old('role') === $r->value ? 'selected' : '' }}>
                                        {{ ucfirst($r->value) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Minimal 8 karakter..." required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                   placeholder="Ulangi password..." required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 text-right control-label col-form-label font-weight-bold">Status Aktif</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Aktifkan akun ini (dapat login ke sistem)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-success waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Simpan Pengguna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

