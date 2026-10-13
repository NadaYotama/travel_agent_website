@extends('layouts.admin')

@section('title', 'Edit Pengguna: ' . $user->name)
@section('header', 'Edit Pengguna')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Edit Akun: {{ $user->name }}</h4>
                            <small class="text-muted">Perbarui data profil, peran sistem, dan status aktif akun.</small>
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
                                   value="{{ old('name', $user->name) }}" required>
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
                                   value="{{ old('email', $user->email) }}" required>
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
                                   value="{{ old('phone', $user->phone) }}">
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
                            @php
                                $currentRole = is_object($user->role) ? $user->role->value : $user->role;
                            @endphp
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" {{ $user->id === auth()->id() ? 'disabled' : '' }} required>
                                @foreach($roles as $r)
                                    <option value="{{ $r->value }}" {{ old('role', $currentRole) === $r->value ? 'selected' : '' }}>
                                        {{ ucfirst($r->value) }}
                                    </option>
                                @endforeach
                            </select>
                            @if($user->id === auth()->id())
                                <small class="text-muted">Anda tidak dapat mengubah peran akun Anda sendiri.</small>
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Password Baru
                        </label>
                        <div class="col-sm-9">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Kosongkan jika tidak ingin mengubah password...">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Konfirmasi Password
                        </label>
                        <div class="col-sm-9">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                   placeholder="Ulangi password baru...">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 text-right control-label col-form-label font-weight-bold">Status Aktif</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="is_active">Aktifkan akun ini (dapat login ke sistem)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-cyan waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Perbarui Pengguna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

