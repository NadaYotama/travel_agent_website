@extends('layouts.app')

@section('title', 'Pesan ' . $package->title . ' - ' . config('app.name', 'Nusa Jelajah'))

@section('content')
<section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('user.packages.show', $package->slug) }}" class="text-sm font-semibold text-[var(--lagoon)] hover:underline">
        &larr; Kembali ke detail paket
    </a>

    <div class="mt-5 grid gap-8 lg:grid-cols-[1fr_300px]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="font-display text-2xl font-extrabold text-[var(--ink)] sm:text-3xl">Data pemesanan</h1>
            <p class="mt-2 text-sm text-slate-600">Lengkapi data setiap peserta untuk melanjutkan pemesanan.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <p class="font-semibold">Periksa kembali data pemesanan:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user.bookings.store', $package->slug) }}" class="mt-7 space-y-6" id="booking-form">
                @csrf
                <div>
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-display text-lg font-bold text-slate-800">Peserta</h2>
                        <button type="button" id="add-participant" class="rounded-full border border-[var(--lagoon)] px-4 py-2 text-sm font-semibold text-[var(--lagoon)] hover:bg-teal-50">
                            + Tambah peserta
                        </button>
                    </div>

                    <div id="participants" class="space-y-4">
                        @php($formParticipants = old('participants'))
                        @if (! is_array($formParticipants) || empty($formParticipants))
                            @php($formParticipants = [['name' => auth()->user()->name, 'id_number' => '', 'phone' => '']])
                        @endif
                        @foreach ($formParticipants as $index => $participant)
                            @php($participant = is_array($participant) ? $participant : [])
                            <fieldset class="participant-card rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                                <div class="mb-4 flex items-center justify-between">
                                    <legend class="participant-title font-semibold text-slate-800">Peserta {{ $index + 1 }}</legend>
                                    @if ($index > 0)
                                        <button type="button" class="remove-participant text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
                                    @endif
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="block text-sm font-medium text-slate-700 sm:col-span-2">
                                        Nama lengkap <span class="text-red-600">*</span>
                                        <input type="text" data-field="name" name="participants[{{ $index }}][name]" value="{{ $participant['name'] ?? '' }}" required maxlength="255" autocomplete="name"
                                               class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
                                    </label>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Nomor identitas
                                        <input type="text" data-field="id_number" name="participants[{{ $index }}][id_number]" value="{{ $participant['id_number'] ?? '' }}" maxlength="30"
                                               class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
                                    </label>
                                    <label class="block text-sm font-medium text-slate-700">
                                        Nomor telepon
                                        <input type="tel" data-field="phone" name="participants[{{ $index }}][phone]" value="{{ $participant['phone'] ?? '' }}" maxlength="20" autocomplete="tel"
                                               class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
                                    </label>
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </div>

                <label class="block text-sm font-medium text-slate-700">
                    Catatan untuk agen <span class="font-normal text-slate-500">(opsional)</span>
                    <textarea name="notes" rows="4" maxlength="1000" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('notes') }}</textarea>
                </label>

                <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[var(--lagoon)] px-6 py-3.5 font-bold text-white shadow-md transition hover:bg-[var(--lagoon-dark)]">
                    Lanjutkan pemesanan
                </button>
            </form>
        </div>

        <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-6">
            <h2 class="font-display text-lg font-bold text-slate-800">Ringkasan paket</h2>
            <p class="mt-3 font-semibold text-[var(--ink)]">{{ $package->title }}</p>
            <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Harga per peserta</dt>
                    <dd class="font-semibold text-slate-800">Rp {{ number_format($package->price, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Keberangkatan</dt>
                    <dd class="font-semibold text-slate-800">{{ optional($package->start_date)->format('d M Y') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Sisa kuota</dt>
                    <dd class="font-semibold text-slate-800">{{ $package->quota }} peserta</dd>
                </div>
            </dl>
            <p class="mt-4 text-xs leading-relaxed text-slate-500">Total biaya akan dihitung otomatis berdasarkan jumlah peserta dan kuota yang tersedia.</p>
        </aside>
    </div>
</section>

<template id="participant-template">
    <fieldset class="participant-card rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
        <div class="mb-4 flex items-center justify-between">
            <legend class="participant-title font-semibold text-slate-800"></legend>
            <button type="button" class="remove-participant text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-medium text-slate-700 sm:col-span-2">
                Nama lengkap <span class="text-red-600">*</span>
                <input type="text" data-field="name" required maxlength="255" autocomplete="name" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
            </label>
            <label class="block text-sm font-medium text-slate-700">
                Nomor identitas
                <input type="text" data-field="id_number" maxlength="30" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
            </label>
            <label class="block text-sm font-medium text-slate-700">
                Nomor telepon
                <input type="tel" data-field="phone" maxlength="20" autocomplete="tel" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[var(--lagoon)] focus:outline-none focus:ring-2 focus:ring-teal-100">
            </label>
        </div>
    </fieldset>
</template>

@push('scripts')
<script>
    (() => {
        const participants = document.getElementById('participants');
        const addButton = document.getElementById('add-participant');
        const template = document.getElementById('participant-template');
        const maximum = 20;

        const updateParticipants = () => {
            participants.querySelectorAll('.participant-card').forEach((card, index) => {
                card.querySelector('.participant-title').textContent = `Peserta ${index + 1}`;
                card.querySelectorAll('[data-field]').forEach((input) => {
                    input.name = `participants[${index}][${input.dataset.field}]`;
                });
                const removeButton = card.querySelector('.remove-participant');
                if (removeButton) {
                    removeButton.hidden = participants.children.length === 1;
                }
            });
            addButton.disabled = participants.children.length >= maximum;
            addButton.classList.toggle('cursor-not-allowed', addButton.disabled);
            addButton.classList.toggle('opacity-50', addButton.disabled);
        };

        addButton.addEventListener('click', () => {
            if (participants.children.length >= maximum) return;
            participants.append(template.content.cloneNode(true));
            updateParticipants();
            participants.lastElementChild.querySelector('input').focus();
        });

        participants.addEventListener('click', (event) => {
            if (event.target.closest('.remove-participant')) {
                event.target.closest('.participant-card').remove();
                updateParticipants();
            }
        });

        updateParticipants();
    })();
</script>
@endpush
@endsection
