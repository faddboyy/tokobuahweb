<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TokoBuah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --win-accent: #0067c0;
            --win-glass: rgba(255, 255, 255, 0.7);
        }

        body {
            background: linear-gradient(135deg, #c3d8e6 0%, #ecd6e3 100%);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: var(--win-glass);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1.5rem;
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.5);
            transition: all 0.2s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(0, 103, 192, 0.15);
            border-color: var(--win-accent);
            background: white;
        }

        .btn-login {
            background: var(--win-accent);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #0056a3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 103, 192, 0.3);
        }

        .invalid-feedback {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .alert-modern {
            border-radius: 10px;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="bg-primary rounded-3 d-inline-flex p-3 mb-3 shadow-sm">
            <i data-lucide="citrus" class="text-white" style="width: 32px; height: 32px;"></i>
        </div>
        <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
        <p class="text-muted small">Masuk ke Sistem</p>
    </div>

    <div id="globalAlert" class="alert alert-danger alert-modern shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i data-lucide="alert-circle" class="me-2" style="width: 18px;"></i>
            <span id="globalMessage"></span>
        </div>
    </div>

    <form id="loginForm" novalidate>
        <div class="mb-3">
            <label class="form-label small fw-bold">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                    <i data-lucide="user" style="width: 18px;"></i>
                </span>
                <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Masukkan username" required style="border-radius: 0 10px 10px 0;">
                <div id="error-username" class="invalid-feedback"></div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                    <i data-lucide="lock" style="width: 18px;"></i>
                </span>
                <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="••••••••" required style="border-radius: 0 10px 10px 0;">
                <div id="error-password" class="invalid-feedback"></div>
            </div>
        </div>

        <button type="submit" id="btnLogin" class="btn btn-primary btn-login w-100 d-flex align-items-center justify-content-center gap-2">
            <span>Masuk Sekarang</span>
            <i data-lucide="arrow-right" style="width: 18px;"></i>
        </button>
    </form>
</div>

<script>
    // Inisialisasi Lucide Icons
    lucide.createIcons();

    const loginForm = document.getElementById('loginForm');
    const btnLogin = document.getElementById('btnLogin');
    const globalAlert = document.getElementById('globalAlert');
    const globalMessage = document.getElementById('globalMessage');

    // --- Validasi On-Typing (Vanilla JS) ---
    const inputs = ['username', 'password'];
    inputs.forEach(id => {
        const inputEl = document.getElementById(id);
        inputEl.addEventListener('input', function() {
            // Hapus status error saat user mulai mengetik
            this.classList.remove('is-invalid');
            document.getElementById(`error-${id}`).innerText = '';
            
            // Validasi lokal sederhana
            if (id === 'username' && this.value.length < 3) {
                showFieldError('username', 'Username minimal 3 karakter');
            } else if (id === 'password' && this.value.length < 4) {
                showFieldError('password', 'Password minimal 4 karakter');
            }
        });
    });

    function showFieldError(field, message) {
        const inputEl = document.getElementById(field);
        const errorEl = document.getElementById(`error-${field}`);
        inputEl.classList.add('is-invalid');
        errorEl.innerText = message;
    }

    // --- Handle Form Submit ---
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Reset UI
        globalAlert.style.display = 'none';
        btnLogin.disabled = true;
        btnLogin.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghubungkan...`;

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('<?= base_url('login') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (response.ok && result.status) {
                // Sukses
                btnLogin.innerHTML = `<i data-lucide="check-circle" style="width:18px"></i> Berhasil!`;
                lucide.createIcons();
                setTimeout(() => window.location.href = result.redirect, 800);
            } else {
                // Error dari Controller (422, 401, 403)
                handleErrors(result);
            }
        } catch (error) {
            globalMessage.innerText = "Gagal menghubungi server. Coba lagi nanti.";
            globalAlert.style.display = 'block';
        } finally {
            if (btnLogin.innerText !== 'Berhasil!') {
                btnLogin.disabled = false;
                btnLogin.innerHTML = `<span>Masuk Sekarang</span> <i data-lucide="arrow-right" style="width: 18px;"></i>`;
                lucide.createIcons();
            }
        }
    });

    function handleErrors(result) {
        // 1. Jika ada error per field (validasi gagal)
        if (result.errors) {
            Object.keys(result.errors).forEach(key => {
                showFieldError(key, result.errors[key]);
            });
        }
        
        // 2. Jika ada pesan error global (misal: password salah, akun tidak aktif)
        if (result.message) {
            globalMessage.innerText = result.message;
            globalAlert.style.display = 'block';
        }
    }
</script>

</body>
</html>