<x-layouts.app title="Profil & Ganti Password">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Profil & Keamanan Akun
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola informasi akun Anda dan perbarui kata sandi untuk menjaga keamanan akun.</p>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-2xl shadow-sm text-white">
        <i class="fas fa-circle-check text-lg"></i>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Ringkasan Profil Akun --}}
        <div class="space-y-6">
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-[#2d4499] to-indigo-600 h-28 relative">
                    <div class="absolute -bottom-10 left-6">
                        <div class="w-20 h-20 rounded-2xl bg-white p-1.5 shadow-md">
                            <div class="w-full h-full rounded-xl bg-indigo-100 text-[#2d4499] flex items-center justify-center font-bold text-2xl">
                                {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-12 p-6">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-bold text-lg text-slate-800">{{ $user->nama }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' :
                               ($user->role === 'dokter' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                               'bg-blue-100 text-blue-800 border border-blue-200') }}">
                            {{ $user->role }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 font-mono mb-4">{{ $user->email }}</p>

                    <div class="border-t border-slate-100 pt-4 space-y-3 text-xs">
                        @if($user->role === 'pasien')
                            @if($user->no_rm)
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-500">No. Rekam Medis:</span>
                                <span class="font-mono font-bold text-[#2d4499] bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">{{ $user->no_rm }}</span>
                            </div>
                            @endif
                            @if($user->no_ktp)
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-500">No. KTP:</span>
                                <span class="font-mono text-slate-700 font-semibold">{{ $user->no_ktp }}</span>
                            </div>
                            @endif
                        @endif

                        @if($user->role === 'dokter' && $user->poli)
                            <div class="flex justify-between items-center py-1">
                                <span class="text-slate-500">Poli / Spesialis:</span>
                                <span class="font-semibold text-slate-800">{{ $user->poli->nama_poli }}</span>
                            </div>
                        @endif

                        @if($user->no_hp)
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500">No. Telepon / HP:</span>
                            <span class="font-mono text-slate-700 font-medium">{{ $user->no_hp }}</span>
                        </div>
                        @endif

                        @if($user->alamat)
                        <div class="py-1">
                            <span class="text-slate-500 block mb-1">Alamat:</span>
                            <p class="text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed">{{ $user->alamat }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tips Keamanan --}}
            <div class="card bg-blue-50/70 border border-blue-200/80 rounded-2xl p-5 text-xs text-slate-600">
                <div class="flex items-center gap-2 text-[#2d4499] font-bold text-sm mb-2">
                    <i class="fas fa-shield-halved text-base"></i>
                    <span>Tips Keamanan Sandi</span>
                </div>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600 leading-relaxed">
                    <li>Gunakan minimal 8 karakter kombinasi huruf dan angka.</li>
                    <li>Jangan menggunakan kata sandi yang sama dengan akun lain.</li>
                    <li>Pastikan tidak membagikan kata sandi Anda kepada orang lain.</li>
                </ul>
            </div>
        </div>

        {{-- Right Column: Form Ganti Password --}}
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-[#2d4499] flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Formulir Ganti Password</h3>
                        <p class="text-[11px] text-slate-500">Masukkan kata sandi lama dan tentukan kata sandi baru akun Anda</p>
                    </div>
                </div>

                <div class="card-body p-7">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Kolom 1: Password Lama --}}
                        <div class="form-control mb-5">
                            <label for="password_lama" class="label pb-1.5">
                                <span class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-lock text-slate-400 text-xs"></i>
                                    Password Lama <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password_lama" name="password_lama" required
                                    placeholder="Masukkan password akun yang sedang digunakan saat ini"
                                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-slate-300 text-sm focus:border-[#2d4499] focus:outline-none transition @error('password_lama') border-red-500 bg-red-50/30 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password_lama', 'eye_lama')"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i id="eye_lama" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            @error('password_lama')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-xs"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Kolom 2: Password Baru --}}
                        <div class="form-control mb-5">
                            <label for="password_baru" class="label pb-1.5">
                                <span class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-key text-slate-400 text-xs"></i>
                                    Password Baru <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password_baru" name="password_baru" required
                                    placeholder="Minimal 8 karakter dan berbeda dari password lama"
                                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-slate-300 text-sm focus:border-[#2d4499] focus:outline-none transition @error('password_baru') border-red-500 bg-red-50/30 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password_baru', 'eye_baru')"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i id="eye_baru" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Minimal 8 karakter dan tidak boleh sama dengan password lama.</span>
                            @error('password_baru')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-xs"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Kolom 3: Konfirmasi Password Baru --}}
                        <div class="form-control mb-6">
                            <label for="password_baru_confirmation" class="label pb-1.5">
                                <span class="text-sm font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-slate-400 text-xs"></i>
                                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation" required
                                    placeholder="Ulangi pengetikan password baru Anda"
                                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-slate-300 text-sm focus:border-[#2d4499] focus:outline-none transition @error('password_baru_confirmation') border-red-500 bg-red-50/30 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password_baru_confirmation', 'eye_konfirmasi')"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i id="eye_konfirmasi" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            @error('password_baru_confirmation')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-xs"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="reset"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                                Reset Form
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold text-sm shadow-sm transition">
                                <i class="fas fa-save text-xs"></i>
                                <span>Simpan Perubahan Password</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Script Show/Hide Password --}}
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</x-layouts.app>
