@extends('layouts.admin')

@section('title', 'Edit Kode Promo: ' . $promo->code)
@section('header', 'Edit Promo')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('admin.promos.update', $promo) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Edit Promo: {{ $promo->code }}</h4>
                            <small class="text-muted">Perbarui aturan potongan harga, kuota, atau masa berlaku promo.</small>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary btn-sm waves-effect">
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
                        <label for="code" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Kode Promo <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="code" id="code" class="form-control text-uppercase font-weight-bold @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $promo->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="discount_type" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Jenis Diskon <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
                                <option value="percent" {{ old('discount_type', $promo->discount_type) === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed" {{ old('discount_type', $promo->discount_type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="discount_value" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Nilai Diskon <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" min="1" name="discount_value" id="discount_value" 
                                   class="form-control @error('discount_value') is-invalid @enderror" 
                                   value="{{ old('discount_value', $promo->discount_value) }}" required>
                            @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="quota" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Kuota Penggunaan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" min="0" name="quota" id="quota" 
                                   class="form-control @error('quota') is-invalid @enderror" 
                                   value="{{ old('quota', $promo->quota) }}" required>
                            @error('quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="valid_until" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Berlaku Hingga <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="date" name="valid_until" id="valid_until" 
                                   class="form-control @error('valid_until') is-invalid @enderror" 
                                   value="{{ old('valid_until', optional($promo->valid_until)->format('Y-m-d')) }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-cyan waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Perbarui Promo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

