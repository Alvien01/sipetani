@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Profile Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-800 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Informasi Profil</h2>
            <p class="text-sm text-green-100 mt-1">Kelola informasi profil dan data pribadi Anda</p>
        </div>

        <form action="{{ route('settings.update-profile') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Avatar Section -->
            <div class="flex items-center gap-6 pb-6 border-b border-slate-200">
                <div class="w-20 h-20 bg-gradient-to-br from-green-700 to-green-800 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($personel->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ $personel->name }}</h3>
                    <p class="text-sm text-slate-600">{{ $personel->role->name ?? 'Personel' }}</p>
                    <p class="text-xs text-slate-500 mt-1">NRP: {{ $personel->nrp }}</p>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $personel->name) }}" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $personel->email) }}" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NRP -->
                <div>
                    <label for="nrp" class="block text-sm font-medium text-slate-700 mb-2">
                        NRP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nrp" id="nrp" value="{{ old('nrp', $personel->nrp) }}" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('nrp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">
                        No. Telepon
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $personel->phone) }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rank -->
                <div>
                    <label for="rank" class="block text-sm font-medium text-slate-700 mb-2">
                        Pangkat
                    </label>
                    <input type="text" name="rank" id="rank" value="{{ old('rank', $personel->rank) }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('rank')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-slate-700 mb-2">
                        Jabatan
                    </label>
                    <input type="text" name="position" id="position" value="{{ old('position', $personel->position) }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    @error('position')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role (Read Only) -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-slate-700">Role Saat Ini</p>
                        <p class="text-lg font-semibold text-green-700">{{ $personel->role->name ?? 'Tidak ada role' }}</p>
                        <p class="text-xs text-slate-500 mt-1">Role tidak dapat diubah sendiri. Hubungi administrator untuk perubahan role.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-800 hover:to-green-900 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Ubah Password</h2>
            <p class="text-sm text-slate-100 mt-1">Perbarui password Anda untuk keamanan akun</p>
        </div>

        <form action="{{ route('settings.update-password') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">
                    Password Saat Ini <span class="text-red-500">*</span>
                </label>
                <input type="password" name="current_password" id="current_password" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                @error('current_password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">
                    Password Baru <span class="text-red-500">*</span>
                </label>
                <input type="password" name="new_password" id="new_password" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                <p class="text-xs text-slate-500 mt-1">Minimal 8 karakter</p>
                @error('new_password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                </label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-800 hover:to-slate-900 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        const successAlert = document.querySelector('.bg-green-50');
        if (successAlert) {
            successAlert.style.transition = 'opacity 0.5s';
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 500);
        }
    }, 5000);
</script>
@endsection
