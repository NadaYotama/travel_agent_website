@extends('layouts.admin')

@section('title', 'Tambah Destinasi Baru')
@section('header', 'Tambah Destinasi')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Form Tambah Destinasi Wisata</h4>
                            <small class="text-muted">Isi nama destinasi, lokasi wilayah/kota, negara, dan foto thumbnail utama.</small>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary btn-sm waves-effect">
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
                            Nama Destinasi <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Contoh: Pulau Dewata Bali / Labuan Bajo" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="city" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Kota / Wilayah <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" 
                                   value="{{ old('city') }}" placeholder="Contoh: Denpasar / Manggarai Barat" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="country" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Negara <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" 
                                   value="{{ old('country', 'Indonesia') }}" placeholder="Contoh: Indonesia / Jepang" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="thumbnail" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Foto Thumbnail
                        </label>
                        <div class="col-sm-9">
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" accept="image/*">
                            <small class="form-text text-muted">Format JPG, PNG, atau WebP, maks. 2MB.</small>
                            @error('thumbnail')
                                <div class="text-danger font-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Deskripsi Destinasi
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Penjelasan keindahan dan daya tarik destinasi...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-success waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Simpan Destinasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

