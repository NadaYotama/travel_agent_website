@extends('layouts.manager')

@section('title', 'Tambah Layanan Baru')
@section('header', 'Tambah Layanan')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('manager.services.store') }}" method="POST" class="form-horizontal">
                @csrf
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Form Tambah Layanan Mitra</h4>
                            <small class="text-muted">Isi rincian layanan Anda untuk dapat dimasukkan ke paket wisata.</small>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('manager.services.index') }}" class="btn btn-secondary btn-sm waves-effect">
                                <i class="mdi mdi-arrow-left m-r-5"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Mohon periksa kembali isian form berikut:
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
                        <label for="type" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Jenis Layanan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Layanan --</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Nama Layanan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Contoh: Hotel Grand Bali / Bus Pariwisata 35 Seat / Local Guide Kuta" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="price" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Harga Dasar (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold">Rp</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="price" id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price') }}" placeholder="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Biaya dasar atau rate per satuan unit layanan.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Deskripsi / Fasilitas
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Penjelasan detail mengenai fasilitas, kapasitas, syarat ketentuan layanan...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('manager.services.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-content-save m-r-5"></i> Simpan Layanan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
