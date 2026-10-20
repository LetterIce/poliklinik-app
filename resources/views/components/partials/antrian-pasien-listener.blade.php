{{-- Component Listener Real-Time Panggilan Antrean Pasien --}}
@php
    $currentPatientQueue = \App\Models\DaftarPoli::where('id_pasien', auth()->id())
        ->doesntHave('periksas')
        ->with(['jadwalPeriksa.dokter.poli'])
        ->latest()
        ->first();
@endphp

<div id="antrianPasienListenerConfig"
    data-patient-id="{{ auth()->id() }}"
    data-patient-name="{{ auth()->user()->nama }}"
    data-queue-id="{{ $currentPatientQueue?->id ?? '' }}"
    data-jadwal-id="{{ $currentPatientQueue?->id_jadwal ?? '' }}"
    data-no-antrian="{{ $currentPatientQueue?->no_antrian ?? '' }}"
    data-poli-name="{{ optional($currentPatientQueue?->jadwalPeriksa?->dokter?->poli)->nama_poli ?? 'Poliklinik' }}"
    data-dokter-name="{{ optional($currentPatientQueue?->jadwalPeriksa?->dokter)->nama ?? 'Dokter' }}"
    data-status-url="{{ route('pasien.antrian.status') }}"
    class="hidden">
</div>

<script>
    (function() {
        const configEl = document.getElementById('antrianPasienListenerConfig');
        if (!configEl) return;

        const myPatientId = configEl.dataset.patientId;
        const myPatientName = configEl.dataset.patientName;
        let myJadwalId = configEl.dataset.jadwalId;
        let myNoAntrian = configEl.dataset.noAntrian;
        let myPoliName = configEl.dataset.poliName;
        let myDokterName = configEl.dataset.dokterName;
        const statusUrl = configEl.dataset.statusUrl;

        let lastHandledCallKey = null;

        // 1. Audio Ding-Dong Chime Menggunakan Web Audio API (Bebas dari file eksternal)
        function playHospitalChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                const playTone = (freq, startTime, duration, volume = 0.3) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, startTime);

                    // Fade in & exponential decay seperti bel rumah sakit / bandara
                    gain.gain.setValueAtTime(0.0001, startTime);
                    gain.gain.linearRampToValueAtTime(volume, startTime + 0.04);
                    gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(startTime);
                    osc.stop(startTime + duration);
                };

                const now = ctx.currentTime;
                // Nada 1: G5 (784 Hz), Nada 2: C5 (523 Hz) - Karakter nada rumah sakit
                playTone(783.99, now, 0.75, 0.4);
                playTone(523.25, now + 0.45, 1.2, 0.45);
            } catch (err) {
                console.warn('Audio chime warning:', err);
            }
        }

        // 2. Pengumuman Suara Panggilan Bahasa Indonesia (Web Speech API)
        function announceCallWithSpeech(nomor, nama, poli, dokter) {
            if (!('speechSynthesis' in window)) return;

            try {
                window.speechSynthesis.cancel();

                const callText = `Panggilan nomor antrean ${nomor}. Atas nama ${nama}. Silakan segera masuk ke ruang ${poli}, dokter ${dokter}.`;
                const utterance = new SpeechSynthesisUtterance(callText);
                utterance.lang = 'id-ID';
                utterance.rate = 0.92;
                utterance.pitch = 1.05;

                // Cari voice Bahasa Indonesia jika terpasang di browser
                const voices = window.speechSynthesis.getVoices();
                const idVoice = voices.find(v => v.lang && (v.lang.includes('id') || v.lang.includes('ID')));
                if (idVoice) {
                    utterance.voice = idVoice;
                }

                // Berikan jeda sebentar agar denting bel berbunyi terlebih dahulu
                setTimeout(() => {
                    window.speechSynthesis.speak(utterance);
                }, 650);
            } catch (err) {
                console.warn('Speech synthesis warning:', err);
            }
        }

        // 3. Modal Pop-up Interaktif SweetAlert2
        function triggerPatientCalledAlert(data) {
            const callKey = `${data.nomor}-${data.jadwal_id || data.poli}-${new Date().getMinutes()}`;
            if (lastHandledCallKey === callKey) return;
            lastHandledCallKey = callKey;

            // Bunyikan bel & suara
            playHospitalChime();
            announceCallWithSpeech(data.nomor, data.nama, data.poli, data.dokter);

            // Perbarui visual badge notifikasi di header jika belum update
            updateHeaderNotificationBadge();

            // Tampilkan Modal Panggilan
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<div class="text-xl sm:text-2xl font-black text-[#2d4499] flex items-center justify-center gap-2 tracking-tight"><i class="fas fa-bullhorn text-amber-500 animate-bounce"></i> Giliran Anda Telah Dipanggil!</div>',
                    html: `
                        <div class="py-2 text-center">
                            <div class="inline-flex flex-col items-center justify-center px-8 py-4 rounded-2xl bg-blue-50 border-2 border-blue-200 shadow-inner mb-4">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Nomor Antrean Anda</span>
                                <span class="text-4xl sm:text-5xl font-black text-[#2d4499] my-1">#${data.nomor}</span>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full mt-1 flex items-center gap-1.5 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping inline-block"></span>
                                    <span>Sedang Dipanggil ke Ruangan</span>
                                </span>
                            </div>

                            <div class="bg-slate-50 rounded-xl p-3.5 text-left space-y-2 border border-slate-200 text-xs sm:text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-user-doctor text-blue-500 w-4"></i>Dokter:</span>
                                    <span class="font-bold text-slate-800">${data.dokter}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-clinic-medical text-teal-500 w-4"></i>Ruang Poli:</span>
                                    <span class="font-bold text-slate-800">${data.poli}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-user text-indigo-500 w-4"></i>Pasien:</span>
                                    <span class="font-bold text-slate-800">${data.nama}</span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 mt-4 leading-relaxed font-medium">
                                Dokter pemeriksa sedang menanti Anda di dalam ruang periksa. Silakan segera menuju ke ruangan sekarang juga.
                            </p>
                        </div>
                    `,
                    confirmButtonText: '<i class="fas fa-check-circle mr-1.5"></i> Mengerti, Saya Segera Menuju Ruangan',
                    confirmButtonColor: '#2d4499',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: 'rgba(15, 23, 42, 0.78)',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100 max-w-md',
                        confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md'
                    }
                });
            }
        }

        // Bantuan untuk perbarui badge notifikasi bell jika ada panggilan baru
        function updateHeaderNotificationBadge() {
            const bellBtn = document.querySelector('button[aria-label="Notifikasi"]');
            if (!bellBtn) return;
            let badge = bellBtn.querySelector('span.rounded-full');
            if (badge) {
                let current = parseInt(badge.textContent) || 0;
                badge.textContent = (current + 1) > 9 ? '9+' : (current + 1);
            } else {
                badge = document.createElement('span');
                badge.className = 'absolute top-1 right-1 sm:top-1.5 sm:right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white';
                badge.textContent = '1';
                bellBtn.appendChild(badge);
            }
        }

        // 4. Handler Pemeriksaan Kecocokan Antrean
        function checkAndHandleEvent(event) {
            // Forward event untuk halaman lain (seperti tabel live di dashboard)
            document.dispatchEvent(new CustomEvent('antrian-updated-broadcast', { detail: event }));

            const eventPatientId = event.id_pasien ? String(event.id_pasien) : null;
            const eventNomor = event.nomor_sedang_dilayani ? String(event.nomor_sedang_dilayani) : null;
            const eventJadwalId = event.id_jadwal ? String(event.id_jadwal) : null;

            // Kriteria 1: Cocok langsung berdasarkan ID Pasien
            const isDirectMatch = (eventPatientId && eventPatientId === String(myPatientId));

            // Kriteria 2: Cocok berdasarkan Jadwal & Nomor Antrean Pasien
            const isQueueMatch = (myNoAntrian && myJadwalId &&
                eventJadwalId === String(myJadwalId) &&
                eventNomor === String(myNoAntrian));

            if (isDirectMatch || isQueueMatch) {
                console.log('🎉 [Antrian Listener] Panggilan untuk Anda terdeteksi!', event);
                triggerPatientCalledAlert({
                    nomor: eventNomor || myNoAntrian,
                    nama: event.nama_pasien || myPatientName,
                    poli: event.nama_poli || myPoliName,
                    dokter: event.nama_dokter || myDokterName,
                    jadwal_id: eventJadwalId || myJadwalId
                });
            }
        }

        // 5. Inisialisasi Listener WebSocket Reverb (Laravel Echo)
        function initEcho() {
            if (window.Echo) {
                console.log('🎧 [Antrian Listener] Memasang listener real-time di channel "antrian-channel"');
                window.Echo.channel('antrian-channel')
                    .listen('.AntrianUpdated', (event) => {
                        checkAndHandleEvent(event);
                    });
            } else {
                setTimeout(initEcho, 300);
            }
        }

        initEcho();

        // 6. Polling Berkala Sebagai Fallback
        async function syncQueueStatus() {
            if (!statusUrl) return;
            try {
                const res = await fetch(statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                if (data.has_active_queue && data.active_queue) {
                    const q = data.active_queue;
                    myNoAntrian = String(q.nomor_anda);
                    myPoliName = q.poli;
                    myDokterName = q.dokter;

                    // Jika nomor sedang dilayani sama dengan nomor pasien
                    if (String(q.sedang_dilayani) === String(q.nomor_anda)) {
                        triggerPatientCalledAlert({
                            nomor: q.nomor_anda,
                            nama: myPatientName,
                            poli: q.poli,
                            dokter: q.dokter,
                            jadwal_id: myJadwalId
                        });
                    }
                }
            } catch (err) {
                // Silently ignore network error
            }
        }

        // Jalankan sinkronisasi fallback setiap 10 detik
        setInterval(syncQueueStatus, 10000);
    })();
</script>
