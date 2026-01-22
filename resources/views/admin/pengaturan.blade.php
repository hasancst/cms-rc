@extends('admin.layout')

@section('judul', 'Pengaturan Sistem')

@section('styles')
<style>
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 10px;
    }
    .tab-item {
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 10px;
        font-weight: 600;
        color: var(--text-muted);
        transition: all 0.2s;
    }
    .tab-item:hover {
        background: var(--primary-light);
        color: var(--primary);
    }
    .tab-item.active {
        background: var(--primary);
        color: #fff;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
@endsection

@section('konten')
<div class="card">
    <div class="tabs">
        <div class="tab-item active" onclick="switchTab('umum')">Umum</div>
        <div class="tab-item" onclick="switchTab('optimasi')">Optimasi</div>
        <div class="tab-item" onclick="switchTab('email')">Konfigurasi Email</div>
        <div class="tab-item" onclick="switchTab('keamanan')">Keamanan</div>
    </div>

    <form action="/admin/pengaturan" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Tab Umum -->
        <div id="umum" class="tab-content active">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Informasi Situs</h3>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Logo Website</label>
                        @if(isset($pengaturan['logo']) && $pengaturan['logo'])
                            <div style="margin-bottom: 10px;">
                                <img src="/storage/{{ $pengaturan['logo'] }}" alt="Logo Saat Ini" style="max-height: 80px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" style="width: 100%; padding: 8px; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px;">
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px; display: {{ isset($pengaturan['logo']) ? 'none' : 'block' }}">Format: PNG, JPG, SVG (Max 2MB)</p>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Situs</label>
                        <input type="text" name="nama_situs" value="{{ $pengaturan['nama_situs'] ?? 'CMS Rumah Cyber' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Deskripsi Situs</label>
                        <textarea name="deskripsi_situs" rows="3">{{ $pengaturan['deskripsi_situs'] ?? '' }}</textarea>
                    </div>
                </div>

                <div>
                    <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Kontak</h3>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email Admin</label>
                        <input type="email" name="email_admin" value="{{ $pengaturan['email_admin'] ?? 'admin@rumahcyber.com' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Alamat Kantor</label>
                        <textarea name="alamat" rows="3">{{ $pengaturan['alamat'] ?? '' }}</textarea>
                    </div>

                    <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Media Sosial</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600;">Facebook URL</label>
                        <input type="text" name="sosmed_facebook" value="{{ $pengaturan['sosmed_facebook'] ?? '' }}" placeholder="https://facebook.com/username">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600;">Twitter/X URL</label>
                        <input type="text" name="sosmed_twitter" value="{{ $pengaturan['sosmed_twitter'] ?? '' }}" placeholder="https://twitter.com/username">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600;">Instagram URL</label>
                        <input type="text" name="sosmed_instagram" value="{{ $pengaturan['sosmed_instagram'] ?? '' }}" placeholder="https://instagram.com/username">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600;">LinkedIn URL</label>
                        <input type="text" name="sosmed_linkedin" value="{{ $pengaturan['sosmed_linkedin'] ?? '' }}" placeholder="https://linkedin.com/in/username">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600;">YouTube URL</label>
                        <input type="text" name="sosmed_youtube" value="{{ $pengaturan['sosmed_youtube'] ?? '' }}" placeholder="https://youtube.com/c/channelname">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Optimasi -->
        <div id="optimasi" class="tab-content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Redis Cache</h3>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Gunakan Redis</label>
                        <select name="optimasi_redis_aktif">
                            <option value="0" {{ ($pengaturan['optimasi_redis_aktif'] ?? '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="1" {{ ($pengaturan['optimasi_redis_aktif'] ?? '0') == '1' ? 'selected' : '' }}>Aktif</option>
                        </select>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Pastikan server Redis Anda sudah berjalan.</p>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Redis Host</label>
                        <input type="text" name="optimasi_redis_host" value="{{ $pengaturan['optimasi_redis_host'] ?? '127.0.0.1' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Redis Port</label>
                        <input type="text" name="optimasi_redis_port" value="{{ $pengaturan['optimasi_redis_port'] ?? '6379' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Redis Password</label>
                        <input type="password" name="optimasi_redis_password" value="{{ $pengaturan['optimasi_redis_password'] ?? '' }}" placeholder="Kosongkan jika tidak ada">
                    </div>
                </div>

                <div>
                    <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Optimasi Gambar</h3>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Auto Convert ke WebP</label>
                        <select name="optimasi_webp_aktif">
                            <option value="0" {{ ($pengaturan['optimasi_webp_aktif'] ?? '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="1" {{ ($pengaturan['optimasi_webp_aktif'] ?? '0') == '1' ? 'selected' : '' }}>Aktif</option>
                        </select>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Semua gambar yang di-upload akan otomatis dikonversi ke format .webp untuk kecepatan akses.</p>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Kualitas WebP (1-100)</label>
                        <input type="text" name="optimasi_webp_kualitas" value="{{ $pengaturan['optimasi_webp_kualitas'] ?? '80' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Email -->
        <div id="email" class="tab-content">
            <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">SMTP (Mail Server)</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Mail Driver</label>
                        <select name="mail_driver">
                            <option value="smtp" {{ ($pengaturan['mail_driver'] ?? '') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="mailgun" {{ ($pengaturan['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="sendmail" {{ ($pengaturan['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Host</label>
                        <input type="text" name="mail_host" value="{{ $pengaturan['mail_host'] ?? '' }}" placeholder="smtp.gmail.com">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Port</label>
                        <input type="text" name="mail_port" value="{{ $pengaturan['mail_port'] ?? '587' }}">
                    </div>
                </div>
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Username</label>
                        <input type="text" name="mail_username" value="{{ $pengaturan['mail_username'] ?? '' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Password</label>
                        <input type="password" name="mail_password" value="{{ $pengaturan['mail_password'] ?? '' }}">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Enkripsi</label>
                        <select name="mail_encryption">
                            <option value="tls" {{ ($pengaturan['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS (Direkomendasikan)</option>
                            <option value="ssl" {{ ($pengaturan['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ ($pengaturan['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Identitas Pengirim</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email Pengirim (From Address)</label>
                        <input type="email" name="mail_from_address" value="{{ $pengaturan['mail_from_address'] ?? '' }}" placeholder="no-reply@domain.com">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Pengirim (From Name)</label>
                        <input type="text" name="mail_from_name" value="{{ $pengaturan['mail_from_name'] ?? '' }}" placeholder="Admin Rumah Cyber">
                    </div>
                </div>
            </div>

        </div>

        <!-- Tab Keamanan -->
        <div id="keamanan" class="tab-content">
            <h3 style="border-bottom: 2px solid var(--accent); padding-bottom: 10px; margin-bottom: 20px;">Google reCAPTCHA v2</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Status CAPTCHA</label>
                        <select name="captcha_aktif">
                            <option value="0" {{ ($pengaturan['captcha_aktif'] ?? '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="1" {{ ($pengaturan['captcha_aktif'] ?? '0') == '1' ? 'selected' : '' }}>Aktif</option>
                        </select>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Aktifkan untuk mencegah spam pada form komentar.</p>
                    </div>
                </div>
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Site Key</label>
                        <input type="text" name="captcha_site_key" value="{{ $pengaturan['captcha_site_key'] ?? '' }}" placeholder="Contoh: 6Ld...">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Secret Key</label>
                        <input type="text" name="captcha_secret_key" value="{{ $pengaturan['captcha_secret_key'] ?? '' }}" placeholder="Contoh: 6Ld...">
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        // Hilangkan active dari semua tab item
        document.querySelectorAll('.tab-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Sembunyikan semua konten
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Tambahkan active ke tab yang dipilih
        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>
@endsection
