@extends('layouts.admin')

@section('title', 'Edit Paket Wisata: ' . $package->title)
@section('header', 'Edit Paket Wisata')

@section('content')
<div class="row">
    <div class="col-lg-10 col-md-12 mx-auto">
        <div class="card">
            <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                        <div>
                            <h4 class="card-title mb-0">Edit Paket: {{ $package->title }}</h4>
                            <small class="text-muted">Perbarui informasi paket, kuota, tanggal keberangkatan, galeri, dan itinerary.</small>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm waves-effect">
                                <i class="mdi mdi-arrow-left m-r-5"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Terdapat kesalahan pengisian formulir:
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

                    <h5 class="font-weight-bold text-dark m-b-20"><i class="mdi mdi-information-outline m-r-5"></i> Informasi Umum</h5>

                    <div class="form-group row">
                        <label for="title" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Judul Paket Wisata <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $package->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="destination_id" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Destinasi Wisata <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="destination_id" id="destination_id" class="form-control @error('destination_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Destinasi --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id', $package->destination_id) == $dest->id ? 'selected' : '' }}>
                                        {{ $dest->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="price" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Harga per Orang (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold">Rp</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="price" id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price', $package->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="duration_days" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Durasi (Hari) <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" min="1" max="60" name="duration_days" id="duration_days" 
                                   class="form-control @error('duration_days') is-invalid @enderror" 
                                   value="{{ old('duration_days', $package->duration_days) }}" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="quota" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Total Kuota (Kursi) <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" min="0" name="quota" id="quota" 
                                   class="form-control @error('quota') is-invalid @enderror" 
                                   value="{{ old('quota', $package->quota) }}" required>
                            @error('quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Jadwal Keberangkatan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Mulai</span></div>
                                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                               value="{{ old('start_date', optional($package->start_date)->format('Y-m-d')) }}" required>
                                    </div>
                                    @error('start_date') <div class="text-danger font-12 mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Selesai</span></div>
                                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date', optional($package->end_date)->format('Y-m-d')) }}" required>
                                    </div>
                                    @error('end_date') <div class="text-danger font-12 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 text-right control-label col-form-label font-weight-bold">
                            Deskripsi Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 text-right control-label col-form-label font-weight-bold">Status Publikasi</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="is_published" {{ old('is_published', $package->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Publikasikan paket ini ke katalog customer</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kelola Foto Galeri -->
                    <h5 class="font-weight-bold text-dark m-t-30 m-b-20 border-top p-t-20"><i class="mdi mdi-image-multiple m-r-5"></i> Foto Galeri Saat Ini</h5>
                    @if($package->images->count() > 0)
                        <div class="row m-b-20">
                            @foreach($package->images as $img)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border mb-0">
                                        <img src="{{ Storage::url($img->path) }}" class="card-img-top" style="height: 120px; object-fit: cover;" onerror="this.src='{{ asset('images/Balii.jpg') }}'">
                                        <div class="card-body p-2 text-center bg-light">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="custom-control-input" id="del_img_{{ $img->id }}">
                                                <label class="custom-control-label text-danger font-12" for="del_img_{{ $img->id }}">Hapus Foto</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted font-13">Belum ada foto yang diunggah untuk paket ini.</p>
                    @endif

                    <div class="form-group row">
                        <label for="images" class="col-sm-3 text-right control-label col-form-label font-weight-bold">Upload Foto Baru</label>
                        <div class="col-sm-9">
                            <input type="file" name="images[]" id="images" class="form-control-file @error('images.*') is-invalid @enderror" multiple accept="image/*">
                            <small class="form-text text-muted">Pilih foto tambahan jika ingin menambah koleksi galeri.</small>
                        </div>
                    </div>

                    <!-- Itinerary Builder -->
                    <h5 class="font-weight-bold text-dark m-t-30 m-b-20 border-top p-t-20"><i class="mdi mdi-calendar-text m-r-5"></i> Rencana Perjalanan (Itinerary)</h5>
                    <div id="itinerary-container">
                        @forelse($package->itineraries as $idx => $it)
                            <div class="itinerary-row p-3 bg-light rounded m-b-15 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-cyan itinerary-title">Hari {{ $it->day }}</strong>
                                    @if($idx > 0)
                                        <button type="button" class="btn btn-danger btn-sm p-1 px-2 btn-remove-itinerary"><i class="mdi mdi-delete"></i> Hapus</button>
                                    @endif
                                </div>
                                <div class="form-group mb-2">
                                    <input type="text" name="itineraries[{{ $idx }}][title]" value="{{ old("itineraries.{$idx}.title", $it->title) }}" class="form-control" placeholder="Judul Kegiatan" required>
                                </div>
                                <div class="form-group mb-0">
                                    <textarea name="itineraries[{{ $idx }}][description]" rows="2" class="form-control" placeholder="Rincian aktivitas...">{{ old("itineraries.{$idx}.description", $it->description) }}</textarea>
                                </div>
                            </div>
                        @empty
                            <div class="itinerary-row p-3 bg-light rounded m-b-15 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-cyan itinerary-title">Hari 1</strong>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="text" name="itineraries[0][title]" class="form-control" placeholder="Judul Kegiatan Hari 1" required>
                                </div>
                                <div class="form-group mb-0">
                                    <textarea name="itineraries[0][description]" rows="2" class="form-control" placeholder="Rincian aktivitas..."></textarea>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="btn-add-itinerary" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-plus m-r-5"></i> Tambah Hari Kegiatan
                    </button>
                </div>

                <div class="border-top">
                    <div class="card-body d-flex justify-content-end">
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary waves-effect m-r-10">Batal</a>
                        <button type="submit" class="btn btn-cyan waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-content-save m-r-5"></i> Perbarui Paket Wisata
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-remove-itinerary').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.itinerary-row').remove();
            updateDayLabels();
        });
    });

    document.getElementById('btn-add-itinerary').addEventListener('click', function() {
        const container = document.getElementById('itinerary-container');
        const count = container.querySelectorAll('.itinerary-row').length;
        const nextDay = count + 1;

        const row = document.createElement('div');
        row.className = 'itinerary-row p-3 bg-light rounded m-b-15 border';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-cyan itinerary-title">Hari ${nextDay}</strong>
                <button type="button" class="btn btn-danger btn-sm p-1 px-2 btn-remove-itinerary"><i class="mdi mdi-delete"></i> Hapus</button>
            </div>
            <div class="form-group mb-2">
                <input type="text" name="itineraries[${count}][title]" class="form-control" placeholder="Judul Kegiatan Hari ${nextDay}" required>
            </div>
            <div class="form-group mb-0">
                <textarea name="itineraries[${count}][description]" rows="2" class="form-control" placeholder="Rincian aktivitas hari ${nextDay}..."></textarea>
            </div>
        `;
        container.appendChild(row);

        row.querySelector('.btn-remove-itinerary').addEventListener('click', function() {
            row.remove();
            updateDayLabels();
        });
    });

    function updateDayLabels() {
        const rows = document.querySelectorAll('#itinerary-container .itinerary-row');
        rows.forEach((r, idx) => {
            r.querySelector('.itinerary-title').textContent = `Hari ${idx + 1}`;
        });
    }
</script>
@endpush

