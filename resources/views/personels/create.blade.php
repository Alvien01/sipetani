@extends('layouts.dashboard')
@section('title', 'Tambah Personel')
@section('content')
<div class="">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('personels.index') }}" class="text-slate-600 hover:text-slate-900 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('personels.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('name') border-green-600 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('email') border-green-600 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        placeholder="08123456789"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('phone') border-green-600 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Format: 08xxxxxxxxxx (untuk SMS notifikasi)</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('password') border-green-600 @enderror">
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3 flex items-center pb-5 text-slate-500 hover:text-green-600 cursor-pointer">
                            <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon-password" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter</p>
                </div>

                <!-- NRP -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">NRP <span class="text-red-500">*</span></label>
                    <input type="text" name="nrp" value="{{ old('nrp') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('nrp') border-green-600 @enderror">
                    @error('nrp')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rank -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pangkat <span class="text-red-500">*</span></label>
                    <input type="text" name="rank" value="{{ old('rank') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('rank') border-green-600 @enderror">
                    @error('rank')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('position') border-green-600 @enderror">
                    @error('position')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('role_id') border-green-600 @enderror">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent @error('status') border-green-600 @enderror">
                        <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Siaga" {{ old('status') == 'Siaga' ? 'selected' : '' }}>Siaga</option>
                        <option value="Terkonfirmasi" {{ old('status') == 'Terkonfirmasi' ? 'selected' : '' }}>Terkonfirmasi</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-green-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('personels.index') }}" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="cursor-pointer px-6 py-2 bg-gradient-to-r from-green-700 to-green-800 text-white rounded-lg hover:from-green-800 hover:to-green-900 transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById('eye-icon-' + inputId);
        const eyeOffIcon = document.getElementById('eye-off-icon-' + inputId);

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
</script>
@endsection
