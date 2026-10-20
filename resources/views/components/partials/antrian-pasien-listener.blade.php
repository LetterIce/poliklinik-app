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

        let lastHandledTimestamp = 0;
        let sharedAudioCtx = null;
        let isAudioUnlocked = false;

        // Manajemen riwayat ID panggilan agar HANYA berbunyi 1 kali per klik dokter & TIDAK SPAM
        function getHandledCallIds() {
            try {
                const stored = sessionStorage.getItem('poliklinik_handled_calls');
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                return [];
            }
        }

        function markCallIdAsHandled(id) {
            if (!id) return;
            try {
                const list = getHandledCallIds();
                if (!list.includes(String(id))) {
                    list.push(String(id));
                    if (list.length > 50) list.shift();
                    sessionStorage.setItem('poliklinik_handled_calls', JSON.stringify(list));
                }
            } catch (e) {
                // Silently ignore
            }
        }

        // Pre-warm Speech Synthesis voices di awal agar siap bicara seketika
        function prewarmVoices() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.getVoices();
                if (speechSynthesis.onvoiceschanged !== undefined) {
                    speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
                }
            }
        }
        prewarmVoices();

        // Buka izin AudioContext & Speech secara otomatis begitu user berinteraksi
        function unlockBrowserAudio() {
            if (isAudioUnlocked) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    if (!sharedAudioCtx) {
                        sharedAudioCtx = new AudioCtx();
                    }
                    if (sharedAudioCtx.state === 'suspended') {
                        sharedAudioCtx.resume().then(() => {
                            isAudioUnlocked = true;
                        });
                    } else {
                        isAudioUnlocked = true;
                    }
                }

                if ('speechSynthesis' in window && window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                }
            } catch (e) {
                // Silently ignore
            }
        }

        ['click', 'touchstart', 'keydown'].forEach(evt => {
            window.addEventListener(evt, unlockBrowserAudio, { once: true, passive: true });
        });

        // 1. Audio Bel Rumah Sakit (Ding-Dong) via Web Audio API - Suara Bersamaan
        function playHospitalChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;

                if (!sharedAudioCtx || sharedAudioCtx.state === 'closed') {
                    sharedAudioCtx = new AudioCtx();
                }

                if (sharedAudioCtx.state === 'suspended') {
                    sharedAudioCtx.resume();
                }

                const ctx = sharedAudioCtx;
                const now = ctx.currentTime;

                const playTone = (freq, startTime, duration, volume = 0.4) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, startTime);

                    gain.gain.setValueAtTime(0.0001, startTime);
                    gain.gain.linearRampToValueAtTime(volume, startTime + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(startTime);
                    osc.stop(startTime + duration);
                };

                // Ding-Dong: Nada 1 (G5 / 784 Hz, 0.45s) -> Nada 2 (C5 / 523 Hz, 0.75s)
                playTone(783.99, now, 0.45, 0.4);
                playTone(523.25, now + 0.22, 0.75, 0.45);
            } catch (err) {
                // Silently ignore
            }
        }

        // 2. Pengumuman Suara Panggilan Bahasa Indonesia - LANGSUNG BICARA BERSAMAAN DENGAN POP-UP
        function announceCallWithSpeech(nomor, nama, poli, dokter) {
            if (!('speechSynthesis' in window)) return;

            try {
                if (window.speechSynthesis.speaking || window.speechSynthesis.pending) {
                    window.speechSynthesis.cancel();
                }

                const callText = `Panggilan nomor antrean ${nomor}. Atas nama ${nama}. Silakan segera masuk ke ruang ${poli}, dokter ${dokter}.`;
                const utterance = new SpeechSynthesisUtterance(callText);

                // Cari voice bahasa Indonesia terbaik
                const voices = window.speechSynthesis.getVoices() || [];
                let chosenVoice = null;

                if (voices.length > 0) {
                    // 1. Cari voice bahasa Indonesia (id-ID)
                    chosenVoice = voices.find(v => v.lang && (v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.toLowerCase().startsWith('id')));

                    // 2. Cari voice dengan nama Indonesia
                    if (!chosenVoice) {
                        chosenVoice = voices.find(v => v.name && (v.name.toLowerCase().includes('indonesia') || v.name.toLowerCase().includes('indonesian') || v.name.includes('Gadis') || v.name.includes('Ardi')));
                    }

                    // 3. Fallback voice default sistem
                    if (!chosenVoice) {
                        chosenVoice = voices.find(v => v.default) || voices[0] || null;
                    }
                }

                if (chosenVoice) {
                    utterance.voice = chosenVoice;
                    utterance.lang = chosenVoice.lang || 'id-ID';
                } else {
                    utterance.lang = 'id-ID';
                }

                utterance.volume = 1.0;
                utterance.rate = 0.95;
                utterance.pitch = 1.0;

                // Cegah Chromium garbage collection bug dengan referensi global
                window._activeSpeechUtterance = utterance;

                // Bicara langsung bersamaan tanpa delay
                window.speechSynthesis.speak(utterance);

                if (window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                }
            } catch (err) {
                // Silently ignore
            }
        }

        // 3. Modal Pop-up Interaktif SweetAlert2 - MUNCUL BERSAMAAN DENGAN SUARA TANPA DELAY
        function triggerPatientCalledAlert(data) {
            // Cegah duplikasi jika modal saat ini sudah terbuka di layar
            if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                return;
            }

            const now = Date.now();
            if (now - lastHandledTimestamp < 4000) return;
            lastHandledTimestamp = now;

            // 1. Tampilkan Modal Pop-up SEGERA
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<div class="text-xl sm:text-2xl font-black text-[#2d4499] flex items-center justify-center gap-2 tracking-tight">Giliran Anda Telah Dipanggil!</div>',
                    html: `
                        <div class="py-2 text-center">
                            <div class="inline-flex flex-col items-center justify-center px-8 py-4 rounded-2xl bg-blue-50 border-2 border-blue-200 shadow-inner mb-4">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Nomor Antrean Anda</span>
                                <span class="text-4xl sm:text-5xl font-black text-[#2d4499] my-1">#${data.nomor}</span>
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
                                Silakan segera menuju ke ruangan.
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

            // 2. Bunyikan Bel dan Suara SEGERA bersamaan dengan modal pop-up (ZERO DELAY)
            playHospitalChime();
            announceCallWithSpeech(data.nomor, data.nama, data.poli, data.dokter);

            // 3. Perbarui badge lonceng notifikasi di header
            updateHeaderNotificationBadge();
        }

        // Bantuan untuk perbarui badge lonceng notifikasi
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

        // 4. Handler Pemeriksaan Kecocokan Antrean via WebSocket Event
        function checkAndHandleEvent(event) {
            document.dispatchEvent(new CustomEvent('antrian-updated-broadcast', { detail: event }));

            const eventPatientId = event.id_pasien ? String(event.id_pasien) : null;
            const eventNomor = event.nomor_sedang_dilayani ? String(event.nomor_sedang_dilayani) : null;
            const eventJadwalId = event.id_jadwal ? String(event.id_jadwal) : null;

            const isDirectMatch = (eventPatientId && eventPatientId === String(myPatientId));
            const isQueueMatch = (myNoAntrian && myJadwalId &&
                eventJadwalId === String(myJadwalId) &&
                eventNomor === String(myNoAntrian));

            if (isDirectMatch || isQueueMatch) {
                const callPayload = {
                    nomor: eventNomor || myNoAntrian,
                    nama: event.nama_pasien || myPatientName,
                    poli: event.nama_poli || myPoliName,
                    dokter: event.nama_dokter || myDokterName,
                    jadwal_id: eventJadwalId || myJadwalId
                };

                triggerPatientCalledAlert(callPayload);
            }
        }

        // 5. Inisialisasi Listener WebSocket Reverb
        function initEcho() {
            if (window.Echo) {
                window.Echo.channel('antrian-channel')
                    .listen('.AntrianUpdated', (event) => {
                        checkAndHandleEvent(event);
                    });
            } else {
                setTimeout(initEcho, 250);
            }
        }

        initEcho();

        // 6. Polling Sinkronisasi Data & Fallback Panggilan Terverifikasi (Bebas Spam)
        async function syncQueueData() {
            if (!statusUrl) return;
            try {
                const res = await fetch(statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                // Perbarui variabel lokal antrean pasien
                if (data.has_active_queue && data.active_queue) {
                    const q = data.active_queue;
                    myNoAntrian = String(q.nomor_anda);
                    myPoliName = q.poli;
                    myDokterName = q.dokter;
                }

                // Cek apakah ada notifikasi panggilan baru dari dokter
                if (data.latest_call && data.latest_call.id) {
                    const callId = String(data.latest_call.id);
                    const handledCalls = getHandledCallIds();

                    // Jika ID panggilan ini belum pernah diputar di sesi ini:
                    if (!handledCalls.includes(callId)) {
                        markCallIdAsHandled(callId);

                        triggerPatientCalledAlert({
                            nomor: data.latest_call.nomor || myNoAntrian,
                            nama: data.latest_call.nama || myPatientName,
                            poli: data.latest_call.poli || myPoliName,
                            dokter: data.latest_call.dokter || myDokterName,
                            jadwal_id: myJadwalId
                        });
                    }
                }
            } catch (err) {
                // Silently ignore
            }
        }

        // Panggil sinkronisasi pertama kali & ulangi setiap 3.5 detik sebagai pengaman
        syncQueueData();
        setInterval(syncQueueData, 3500);
    })();
</script>
