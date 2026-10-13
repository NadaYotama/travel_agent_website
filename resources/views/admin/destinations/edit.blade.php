@extends('layouts.admin')

@section('title', 'Edit Destinasi: ' . $destination->name)
@section('header', 'Edit Destinasi')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Edit Destinasi: {{ $destination->name }}</h4>
                            <small class="text-muted">Perbarui data wilayah, negara, thumbnail, dan deskripsi destinasi wisata.</small>
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
                                   value="{{ old('name', $destination->name) }}" required>
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
                                   value="{{ old('city', $destination->city) }}" required>
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
                                   value="{{ old('country', $destination->country) }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Foto Saat Ini
                        </label>
                        <div class="col-sm-9">
                            @if($destination->thumbnail)
                                <div class="m-b-10">
                                    <img src="{{ Storage::url($destination->thumbnail) }}" alt="{{ $destination->name }}" 
                                         class="rounded border shadow-sm" style="max-height: 140px; object-fit: cover;" onerror="this.src='{{ asset('images/Balii.jpg') }}'">
                                </div>
                            @else
                                <p class="text-muted font-12 mb-2">Belum ada foto thumbnail.</p>
                            @endif
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" accept="image/*">
                            <small class="form-text text-muted">Unggah foto baru jika ingin mengganti thumbnail saat ini.</small>
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
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $destination->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-cyan waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Perbarui Destinasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

