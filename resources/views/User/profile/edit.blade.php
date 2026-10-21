@extends(match (auth()->user()->role->value) {
    'admin' => 'layouts.admin',
    'manager' => 'layouts.manager',
    'tourguide' => 'layouts.tourguide',
    default => 'layouts.app',
})

@section('title', 'Profil Akun')
@section('header', 'Profil Akun')

@push('styles')
<style>
    .profile-page { max-width: 960px; margin: 0 auto 32px; }
    .profile-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 20px; padding: 24px; }
    .profile-card h2 { color: #1f2937; font-size: 18px; font-weight: 700; margin: 0 0 6px; }
    .profile-card p { color: #6b7280; margin: 0 0 20px; }
    .profile-field { margin-bottom: 16px; }
    .profile-field label { color: #374151; display: block; font-weight: 600; margin-bottom: 6px; }
    .profile-field input { border: 1px solid #d1d5db; border-radius: 6px; padding: 9px 12px; width: 100%; }
    .profile-field input:focus { border-color: #0891b2; outline: 2px solid rgba(8, 145, 178, .15); }
    .profile-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
    .profile-button { border: 0; border-radius: 6px; cursor: pointer; display: inline-block; font-weight: 600; padding: 10px 16px; text-decoration: none; }
    .profile-button-primary { background: #0891b2; color: #fff !important; }
    .profile-button-secondary { background: #e5e7eb; color: #374151 !important; }
    .profile-button-danger { background: #dc2626; color: #fff !important; }
    .profile-error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
    .profile-avatar { border-radius: 50%; height: 72px; object-fit: cover; width: 72px; }
</style>
@endpush

@section('content')
<div class="profile-page">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="profile-card">
        <h2>Informasi Profil</h2>
        <p>Perbarui nama, email, nomor telepon, dan foto profil akun Anda.</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="profile-field">
                <label for="name">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name">
                @error('name') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-field">
                <label for="email">Alamat email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email">
                @error('email') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-field">
                <label for="phone">Nomor telepon</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" maxlength="20" autocomplete="tel">
                @error('phone') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-field">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Foto profil {{ $user->name }}" class="profile-avatar mb-2">
                @endif
                <label for="avatar">Foto profil (JPG, PNG, atau WebP; maksimal 2 MB)</label>
                <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                @error('avatar') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-actions">
                <a href="{{ route($user->role->dashboardRoute()) }}" class="profile-button profile-button-secondary">Kembali ke Dashboard</a>
                <button type="submit" class="profile-button profile-button-primary">Simpan Profil</button>
            </div>
        </form>
    </section>

    <section class="profile-card">
        <h2>Ganti Kata Sandi</h2>
        <p>Masukkan kata sandi saat ini untuk mengatur kata sandi baru.</p>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="current_password">Kata sandi saat ini</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
                @error('current_password') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-field">
                <label for="password">Kata sandi baru</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                @error('password') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            <div class="profile-field">
                <label for="password_confirmation">Konfirmasi kata sandi baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
            </div>

            <div class="profile-actions">
                <button type="submit" class="profile-button profile-button-primary">Ganti Kata Sandi</button>
            </div>
        </form>
    </section>

    <section class="profile-card">
        <h2>Hapus Akun</h2>
        <p>Penghapusan akun tidak dapat dibatalkan. Akun dengan riwayat booking tidak dapat dihapus.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            @method('DELETE')

            <div class="profile-field">
                <label for="delete_password">Konfirmasi dengan kata sandi</label>
                <input id="delete_password" name="password" type="password" required autocomplete="current-password">
                @error('password') <div class="profile-error">{{ $message }}</div> @enderror
            </div>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="profile-actions">
                <button type="submit" class="profile-button profile-button-danger">Hapus Akun Saya</button>
            </div>
        </form>
    </section>
</div>
@endsection
