<?php ob_start(); ?>
<?php
$waktu_mulai = strtotime($cbt['waktu_mulai']);
$waktu_selesai = $waktu_mulai + ($durasi_menit * 60);
$sisa_detik = max(0, $waktu_selesai - time());
?>
<div class="max-w-5xl mx-auto" x-data="cbtExam(<?= $sisa_detik ?>)">
    <!-- Header CBT (Sticky) -->
    <div class="bg-white shadow sticky top-0 z-50 mb-6 border-b-4 border-emerald-500">
        <div class="px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Ujian CBT SIT-PSB</h2>
                <p class="text-sm text-gray-500">Peserta: <?= htmlspecialchars(Auth::user()['username']) ?> - <?= htmlspecialchars(Auth::user()['name']) ?></p>
            </div>
            <div class="text-right flex items-center bg-gray-100 rounded-full px-4 py-2">
                <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="font-mono text-xl font-bold text-red-600" x-text="formattedTime"></div>
            </div>
        </div>
    </div>

    <!-- Form Ujian -->
    <form id="formCBT" action="<?= url('santri/submit-cbt') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 mb-8">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        
        <?php if(empty($soal_list)): ?>
            <div class="text-center py-10 bg-yellow-50 rounded-lg">
                <p class="text-yellow-800">Maaf, soal ujian belum tersedia untuk gelombang ini.</p>
            </div>
        <?php else: ?>
            <div class="space-y-10">
                <?php $no = 1; foreach($soal_list as $soal): ?>
                <div class="border-b pb-8 last:border-0 last:pb-0 relative" id="soal_<?= $no ?>">
                    <div class="flex">
                        <div class="mr-4 font-bold text-lg text-emerald-600"><?= $no ?>.</div>
                        <div class="flex-1">
                            <div class="text-lg text-gray-800 mb-4 prose max-w-none">
                                <?= safe_html($soal['pertanyaan']) ?>
                            </div>
                            
                            <?php if($soal['tipe'] == 'pg'): ?>
                                <?php $pilihan = json_decode($soal['pilihan_json'], true); ?>
                                <div class="space-y-3 pl-2">
                                    <?php foreach(['A', 'B', 'C', 'D'] as $opsi): ?>
                                    <?php if(isset($pilihan[$opsi])): ?>
                                        <label class="flex items-start cursor-pointer group">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="jawaban[<?= $soal['id'] ?>]" value="<?= $opsi ?>" class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300">
                                            </div>
                                            <div class="ml-3 text-gray-700 bg-gray-50 border border-gray-200 rounded px-4 py-2 w-full group-hover:bg-emerald-50 transition">
                                                <span class="font-bold mr-2"><?= $opsi ?>.</span> <?= htmlspecialchars($pilihan[$opsi]) ?>
                                            </div>
                                        </label>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif($soal['tipe'] == 'rekam_suara'): ?>
                                <?php $pilihan = json_decode($soal['pilihan_json'], true); ?>
                                
                                <?php if(isset($pilihan['is_quran']) && $pilihan['is_quran']): ?>
                                    <div class="mt-4 mb-6 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl" x-data="{ 
                                        ayatText: '', 
                                        loading: true,
                                        async fetchText() {
                                            try {
                                                const res = await fetch('https://equran.id/api/v2/surat/<?= $pilihan['surah_no'] ?>');
                                                const data = await res.json();
                                                const verses = data.data.ayat.filter(a => a.nomorAyat >= <?= $pilihan['ayat_start'] ?> && a.nomorAyat <= <?= $pilihan['ayat_end'] ?>);
                                                this.ayatText = verses.map(v => v.teksArab + ' (' + v.nomorAyat + ')').join(' ');
                                                this.loading = false;
                                            } catch(e) { this.ayatText = 'Gagal memuat teks Arabic'; this.loading = false; }
                                        }
                                    }" x-init="fetchText()">
                                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-100/50 px-3 py-1 rounded-lg w-fit">Materi Hafalan: Surah <?= $pilihan['surah_name'] ?> (Ayat <?= $pilihan['ayat_start'] ?>-<?= $pilihan['ayat_end'] ?>)</span>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Silakan Baca Ayat Berikut:</span>
                                        </div>
                                        <div x-show="loading" class="animate-pulse flex justify-end">
                                            <div class="h-8 bg-emerald-100 rounded w-3/4"></div>
                                        </div>
                                        <div x-show="!loading" class="text-right text-3xl sm:text-4xl font-arabic leading-[4rem] sm:leading-[5.5rem] text-emerald-950 dir-rtl">
                                            <span x-text="ayatText"></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-4 p-4 border rounded-lg bg-gray-50" x-data="audioRecorder('rekaman_<?= $soal['id'] ?>')">
                                    <p class="text-sm text-gray-600 mb-3 font-medium">🎤 Rekam Suara Langsung:</p>
                                    
                                    <div class="flex items-center space-x-4">
                                        <button type="button" @click="startRecording()" x-show="!isRecording && !hasRecording" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow flex items-center transition">
                                            <span class="w-3 h-3 bg-white rounded-full mr-2"></span> Mulai Rekam
                                        </button>
                                        
                                        <button type="button" @click="stopRecording()" x-show="isRecording" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded shadow flex items-center animate-pulse transition">
                                            <span class="w-3 h-3 bg-red-500 mr-2"></span> Berhenti (<span x-text="recordingTime"></span>s)
                                        </button>

                                        <div x-show="hasRecording" class="flex items-center space-x-3 w-full">
                                            <audio x-ref="audioPlayer" controls class="h-10 outline-none flex-1"></audio>
                                            <button type="button" @click="clearRecording()" class="text-red-600 hover:text-red-800 font-medium text-sm border border-red-200 px-3 py-2 rounded bg-red-50">
                                                🗑️ Hapus & Ulangi
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" x-ref="audioInput" name="rekaman[<?= $soal['id'] ?>]" class="hidden" accept="audio/*">
                                    <p class="text-xs text-red-500 mt-2" x-show="errorMsg" x-text="errorMsg"></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php $no++; endforeach; ?>
            </div>
            
            <div class="mt-10 pt-6 border-t border-gray-200 flex justify-between items-center">
                <p class="text-gray-500 text-sm">Pastikan semua jawaban telah terisi sebelum mengumpulkan.</p>
                <button type="button" @click="submitExam()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded shadow-lg transition">
                    Selesai & Kumpulkan
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
function cbtExam(initialSeconds) {
    return {
        seconds: initialSeconds,
        formattedTime: '00:00:00',
        timerInterval: null,
        
        init() {
            if (this.seconds <= 0) {
                this.timeUp();
                return;
            }
            this.updateDisplay();
            this.timerInterval = setInterval(() => {
                this.seconds--;
                this.updateDisplay();
                
                if (this.seconds <= 0) {
                    clearInterval(this.timerInterval);
                    this.timeUp();
                } else if (this.seconds === 300) { // 5 menit peringatan
                    Swal.fire({
                        title: 'Peringatan Waktu!',
                        text: 'Waktu ujian Anda tersisa 5 menit lagi. Segera selesaikan jawaban Anda.',
                        icon: 'warning',
                        confirmButtonText: 'Siap!'
                    });
                }
            }, 1000);
        },
        
        updateDisplay() {
            const h = Math.floor(this.seconds / 3600);
            const m = Math.floor((this.seconds % 3600) / 60);
            const s = this.seconds % 60;
            this.formattedTime = 
                (h > 0 ? h.toString().padStart(2, '0') + ':' : '') +
                m.toString().padStart(2, '0') + ':' + 
                s.toString().padStart(2, '0');
        },
        
        timeUp() {
            this.formattedTime = '00:00:00';
            Swal.fire({
                title: 'Waktu Habis!',
                text: 'Waktu pengerjaan sudah selesai. Jawaban Anda akan dikirim secara otomatis.',
                icon: 'error',
                allowOutsideClick: false,
                confirmButtonText: 'Kirim Sekarang'
            }).then(() => {
                document.getElementById('formCBT').submit();
            });
        },
        
        async submitExam() {
            const result = await Swal.fire({
                title: 'Kumpulkan jawaban sekarang?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Belum'
            });

            if (result.isConfirmed) {
                clearInterval(this.timerInterval);
                document.getElementById('formCBT').submit();
            }
        }
    }
}

function audioRecorder(id) {
    return {
        mediaRecorder: null,
        audioChunks: [],
        isRecording: false,
        hasRecording: false,
        recordingTime: 0,
        recordingInterval: null,
        errorMsg: '',
        
        async startRecording() {
            this.errorMsg = '';
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];
                
                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };
                
                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    this.$refs.audioPlayer.src = audioUrl;
                    
                    // Create File object to put in the hidden input
                    const file = new File([audioBlob], `rekaman_${new Date().getTime()}.webm`, { type: 'audio/webm' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.$refs.audioInput.files = dataTransfer.files;
                    
                    this.hasRecording = true;
                };
                
                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingTime = 0;
                
                this.recordingInterval = setInterval(() => {
                    this.recordingTime++;
                    if(this.recordingTime >= 300) { // Limit 5 menit
                        this.stopRecording();
                    }
                }, 1000);
            } catch (err) {
                this.errorMsg = 'Error mengakses microphone: ' + err.message;
            }
        },
        
        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop()); // release mic
            }
            clearInterval(this.recordingInterval);
            this.isRecording = false;
        },
        
        clearRecording() {
            this.hasRecording = false;
            this.audioChunks = [];
            this.$refs.audioInput.value = '';
            this.$refs.audioPlayer.src = '';
        }
    }
}
</script>

<?php
$content = ob_get_clean();
$title = "Ujian CBT - SIT-PSB / PPRTQ Raudlatul Falah";
require __DIR__ . '/../layouts/santri.php';
