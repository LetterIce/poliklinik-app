{{-- Component Listener Real-Time Antrean & Notifikasi untuk Dokter --}}
@php
    $currentDoctor = auth()->user();
    $doctorJadwalIds = \App\Models\JadwalPeriksa::where('id_dokter', $currentDoctor->id)->pluck('id')->toArray();
@endphp

<div id="antrianDokterListenerConfig"
    data-doctor-id="{{ $currentDoctor->id }}"
    data-doctor-name="{{ $currentDoctor->nama }}"
    data-doctor-jadwals="{{ json_encode($doctorJadwalIds) }}"
    class="hidden">
</div>

<script>
    (function() {
        const configEl = document.getElementById('antrianDokterListenerConfig');
        if (!configEl) return;

        const myDoctorId = configEl.dataset.doctorId;
        const myDoctorName = configEl.dataset.doctorName;
        let myJadwalIds = [];
        try {
            myJadwalIds = JSON.parse(configEl.dataset.doctorJadwals || '[]');
        } catch (e) {
            myJadwalIds = [];
        }

        let sharedAudioCtx = null;
        let isAudioUnlocked = false;
        let isSyncing = false;
        const handledRegistrationKeys = new Set();

        // Buka izin AudioContext secara otomatis pada interaksi pertama dokter
        function unlockAudio() {
            if (isAudioUnlocked) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    if (!sharedAudioCtx) sharedAudioCtx = new AudioCtx();
                    if (sharedAudioCtx.state === 'suspended') {
                        sharedAudioCtx.resume().then(() => { isAudioUnlocked = true; });
                    } else {
                        isAudioUnlocked = true;
                    }
                }
            } catch (e) {
                // Silently ignore
            }
        }

        ['click', 'touchstart', 'keydown'].forEach(evt => {
            window.addEventListener(evt, unlockAudio, { once: true, passive: true });
        });

        // Nada Notifikasi Halus & Elegan untuk Dokter (C5 523Hz -> G5 784Hz)
        function playNewPatientChime() {
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

                const playTone = (freq, startTime, duration, volume = 0.25) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, startTime);

                    gain.gain.setValueAtTime(0.0001, startTime);
                    gain.gain.linearRampToValueAtTime(volume, startTime + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(startTime);
                    osc.stop(startTime + duration);
                };

                // Lembut dan ramah di telinga dokter
                playTone(523.25, now, 0.35, 0.25);
                playTone(783.99, now + 0.18, 0.55, 0.30);
            } catch (err) {
                // Silently ignore
            }
        }

        // Tampilkan Notifikasi Toast Non-Intrusif (Tidak Menghalangi Pekerjaan Dokter)
        function showNewPatientToast(nama, nomor) {
            const patientName = nama || 'Pasien Baru';
            const queueNumber = nomor ? `#${nomor}` : '';

            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'info',
                    title: `<div class="font-bold text-sm text-slate-800">Pasien Baru Mendaftar!</div>`,
                    html: `<div class="text-xs text-slate-600 mt-0.5"><strong>${patientName}</strong> (${queueNumber}) telah masuk ke antrean.</div>`,
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-200'
                    }
                });
            }
        }

        // Pembaruan Live DOM Seamless (Tanpa Refresh Halaman)
        async function syncDoctorViews() {
            if (isSyncing) return;
            isSyncing = true;

            try {
                // Request halaman saat ini dengan fetch
                const res = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });

                if (!res.ok) {
                    isSyncing = false;
                    return;
                }

                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update Tabel Daftar Pasien (/dokter/periksa-pasien)
                const currentTableCard = document.getElementById('tabelPeriksaPasienCard');
                const newTableCard = doc.getElementById('tabelPeriksaPasienCard');
                if (currentTableCard && newTableCard) {
                    if (currentTableCard.innerHTML !== newTableCard.innerHTML) {
                        currentTableCard.innerHTML = newTableCard.innerHTML;
                    }
                }

                // 2. Update Card Antrean Menunggu di Dashboard (/dokter/dashboard)
                const currentCardAntrean = document.getElementById('cardAntreanMenunggu');
                const newCardAntrean = doc.getElementById('cardAntreanMenunggu');
                if (currentCardAntrean && newCardAntrean) {
                    if (currentCardAntrean.innerHTML !== newCardAntrean.innerHTML) {
                        currentCardAntrean.innerHTML = newCardAntrean.innerHTML;
                    }
                }

                // 3. Update Banner Antrean Terdepan (/dokter/dashboard)
                const currentBanner = document.getElementById('bannerAntreanDokter');
                const newBanner = doc.getElementById('bannerAntreanDokter');
                if (currentBanner && newBanner) {
                    if (currentBanner.innerHTML !== newBanner.innerHTML) {
                        currentBanner.innerHTML = newBanner.innerHTML;
                    }
                }

                // 4. Update Tabel Antrean Menunggu di Dashboard (/dokter/dashboard)
                const currentTabelAntrean = document.getElementById('tabelAntreanMenungguCard');
                const newTabelAntrean = doc.getElementById('tabelAntreanMenungguCard');
                if (currentTabelAntrean && newTabelAntrean) {
                    if (currentTabelAntrean.innerHTML !== newTabelAntrean.innerHTML) {
                        currentTabelAntrean.innerHTML = newTabelAntrean.innerHTML;
                    }
                }

                // 5. Update Header Notification Dropdown & Badge Lonceng
                const currentNotifDropdown = document.getElementById('headerNotificationDropdown');
                const newNotifDropdown = doc.getElementById('headerNotificationDropdown');
                if (currentNotifDropdown && newNotifDropdown) {
                    if (currentNotifDropdown.innerHTML !== newNotifDropdown.innerHTML) {
                        currentNotifDropdown.innerHTML = newNotifDropdown.innerHTML;
                    }
                }
            } catch (err) {
                // Silently ignore
            } finally {
                isSyncing = false;
            }
        }

        // Cek apakah event WebSocket relevan untuk dokter ini
        function handleAntrianEvent(event) {
            const eventDoctorId = event.id_dokter ? String(event.id_dokter) : null;
            const eventJadwalId = event.id_jadwal ? parseInt(event.id_jadwal) : null;

            const isForMe = (eventDoctorId && eventDoctorId === String(myDoctorId)) ||
                            (eventJadwalId && myJadwalIds.includes(eventJadwalId));

            if (!isForMe) return;

            // Jika ada pasien baru mendaftar
            if (event.action === 'daftar') {
                const regKey = `${event.id_jadwal}_${event.no_antrian}_${event.nama_pasien || ''}`;
                if (!handledRegistrationKeys.has(regKey)) {
                    handledRegistrationKeys.add(regKey);
                    playNewPatientChime();
                    showNewPatientToast(event.nama_pasien, event.no_antrian);
                }
            }

            // Segera sinkronisasikan tampilan halaman dokter tanpa refresh
            syncDoctorViews();
        }

        // Inisialisasi Listener WebSocket Reverb
        function initDoctorEcho() {
            if (window.Echo) {
                window.Echo.channel('antrian-channel')
                    .listen('.AntrianUpdated', (event) => {
                        handleAntrianEvent(event);
                    });
            } else {
                setTimeout(initDoctorEcho, 250);
            }
        }

        initDoctorEcho();

        // Background Fallback Polling (setiap 7 detik) memastikan tabel selalu sinkron
        // bahkan jika koneksi WebSocket sedang offline / reconnecting
        setInterval(syncDoctorViews, 7000);
    })();
</script>
