<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Point of Sale - Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Apply saved theme (match main layout behavior) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || 'dark';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            }
        })();
    </script>
    <style>
        @keyframes scanAnim {
            0%   { transform: translateY(-48px); }
            50%  { transform: translateY(48px); }
            100% { transform: translateY(-48px); }
        }

        #popupToast {
            min-width: 240px;
            max-width: 320px;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    {{-- HEADER ATAS --}}
    <div class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between
                px-4 py-2 bg-white dark:bg-gray-900 border-b border-gray-200
                dark:border-gray-700 shadow-sm">
        <div class="flex items-center gap-2">
            {{-- Tombol Kembali --}}
            <button type="button"
                    onclick="window.history.back()"
                    class="w-9 h-9 flex items-center justify-center bg-teal-600 text-white
                           rounded-lg hover:bg-teal-700 transition-colors"
                    title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            {{-- Tombol Fullscreen --}}
            <button type="button"
                    id="btnFullscreen"
                    onclick="toggleFullscreen()"
                    class="w-9 h-9 flex items-center justify-center bg-teal-600 text-white
                           rounded-lg hover:bg-teal-700 transition-colors"
                    title="Fullscreen">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     id="iconFullscreen">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5M4 16v4m0 0h4
                             m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>
        </div>
        {{-- Tombol Print Resi --}}
        <button type="button"
                id="btnPrintResi"
                onclick="printLastResi()"
                disabled
                class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white
                       rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-40
                       disabled:cursor-not-allowed"
                title="Cetak Resi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0
                         002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2
                         2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
        </button>
    </div>

    <div id="popupToast"
         class="fixed right-4 top-24 z-50 hidden rounded-2xl border border-gray-200
                bg-white/95 px-4 py-3 text-sm font-medium text-gray-800 shadow-2xl
                backdrop-blur transition-all duration-300 ease-out opacity-0"></div>

    {{-- MAIN CONTENT --}}
    <div class="pt-16 px-4 pb-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- KOLOM KIRI — AREA PRODUK --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- SEARCH BAR ROW --}}
                <div class="flex gap-3 mb-4">
                    {{-- Search nama/SKU --}}
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input type="text"
                               id="searchProduct"
                               placeholder="Cari nama atau SKU produk..."
                               oninput="filterProducts()"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl
                                      focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm
                                      dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>
                    {{-- Scan barcode input + tombol kamera --}}
                    <div class="relative flex-1 flex gap-2">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16
                                         16h1m-1 0v-3m-4 4h.01M10 4h4"/>
                            </svg>
                            <input type="text"
                                   id="barcodeInput"
                                   placeholder="Scan barcode..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl
                                          focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm
                                          dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        </div>
                        {{-- Tombol kamera scan --}}
                        <button type="button"
                                id="btnToggleCamera"
                                onclick="toggleCamera()"
                                class="w-11 h-11 flex items-center justify-center bg-teal-600 text-white
                                       rounded-xl hover:bg-teal-700 transition-colors flex-shrink-0"
                                title="Scan via Kamera">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4c-1.5 0-3 .5-4 1.5L6 7H4a2 2 0 00-2 2v8a2 2 0 002 2h16
                                         a2 2 0 002-2V9a2 2 0 00-2-2h-2l-2-1.5C15 4.5 13.5 4 12 4z"/>
                                <circle cx="12" cy="13" r="3" stroke="currentColor" stroke-width="2"
                                        fill="none"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Area kamera scan (tersembunyi awalnya) --}}
                <div id="cameraSection" class="hidden mb-4">
                    <div class="relative w-full rounded-xl overflow-hidden bg-black" style="height:220px">
                        <video id="barcodeVideo" class="w-full h-full object-cover"
                               autoplay muted playsinline></video>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-52 h-32 border-2 border-teal-400 rounded-lg relative">
                                <div id="scanLine"
                                     class="absolute left-0 right-0 h-0.5 bg-teal-400 opacity-80"
                                     style="animation: scanAnim 2s linear infinite; top: 50%"></div>
                                <span class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-teal-400"></span>
                                <span class="absolute -top-1 -right-1 w-4 h-4 border-t-2 border-r-2 border-teal-400"></span>
                                <span class="absolute -bottom-1 -left-1 w-4 h-4 border-b-2 border-l-2 border-teal-400"></span>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-teal-400"></span>
                            </div>
                        </div>
                        <button onclick="closeCamera()"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full
                                       flex items-center justify-center hover:bg-red-600 text-xs font-bold">
                            ✕
                        </button>
                        <div class="absolute bottom-2 left-0 right-0 text-center">
                            <span class="text-white text-xs bg-black bg-opacity-60 px-3 py-1 rounded-full">
                                Arahkan kamera ke barcode produk
                            </span>
                        </div>
                    </div>
                    <canvas id="barcodeCanvas" class="hidden"></canvas>
                </div>

                {{-- KATEGORI FILTER CHIPS --}}
                <div class="flex gap-2 flex-wrap mb-4">
                    <button type="button"
                            onclick="filterByCategory(null)"
                            data-category="all"
                            class="category-chip px-4 py-2 rounded-full text-sm font-medium
                                   bg-teal-600 text-white transition-colors">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                        <button type="button"
                                onclick="filterByCategory({{ $cat->id }})"
                                data-category="{{ $cat->id }}"
                                class="category-chip px-4 py-2 rounded-full text-sm font-medium
                                       bg-white border border-gray-300 text-gray-700 hover:bg-gray-50
                                       transition-colors dark:bg-gray-800 dark:border-gray-600
                                       dark:text-gray-300">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                {{-- GRID PRODUK --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 overflow-y-auto"
                     id="productGrid"
                     style="max-height: calc(100vh - 280px);">
                    @forelse($products as $product)
                        <div class="product-card bg-white dark:bg-gray-800 rounded-xl border
                                    border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer
                                    hover:border-teal-400 hover:shadow-md transition-all"
                             data-category="{{ $product->category_id }}"
                             data-name="{{ strtolower($product->name) }}"
                             data-sku="{{ strtolower($product->sku) }}"
                             data-barcode="{{ $product->barcode }}"
                             onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}',
                                      {{ $product->price }}, {{ $product->stock }})">
                            {{-- Gambar produk --}}
                            <div class="w-full bg-gray-100 dark:bg-gray-700 flex items-center
                                        justify-center overflow-hidden" style="height: 100px;">
                                @if($product->image)
                                    @php
                                        $imgPath = str_starts_with($product->image, 'http')
                                                    ? $product->image
                                                    : asset('storage/' . $product->image);
                                    @endphp
                                    <img src="{{ $imgPath }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center w-full h-full\'><svg class=\'w-8 h-8 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                                 a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                                                 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            {{-- Info produk --}}
                            <div class="p-2.5">
                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 leading-tight
                                            truncate mb-0.5">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    Stok: {{ $product->stock }}
                                </div>
                                <div class="text-sm font-bold text-teal-600 dark:text-teal-400">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 py-12 text-center text-gray-400">
                            Tidak ada produk tersedia
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM KANAN — KERANJANG --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200
                            dark:border-gray-700 p-4 h-full">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Keranjang</h2>
                            <button type="button"
                                    onclick="resetCart()"
                                    class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-sm
                                           hover:bg-red-600 font-medium">
                                Reset
                            </button>
                        </div>

                        {{-- Feedback scan --}}
                        <div id="scanFeedback" class="hidden mb-3 p-2 rounded-lg text-xs text-center"></div>

                        {{-- Cart items --}}
                        <div class="flex-1 overflow-y-auto space-y-2 mb-4" id="cartItems"
                             style="max-height: calc(100vh - 420px);">
                        </div>

                        {{-- Summary --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2 mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span id="subtotalDisplay">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white">
                                <span>Total</span>
                                <span id="totalDisplay">Rp 0</span>
                            </div>
                        </div>

                        {{-- Form checkout --}}
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Nama Pelanggan (opsional)
                                </label>
                                <input type="text"
                                       id="customerName"
                                       placeholder="Nama pelanggan..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                              focus:outline-none focus:ring-2 focus:ring-teal-500
                                              dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Metode Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <select id="paymentMethodId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-teal-500
                                               dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- Pilih Metode --</option>
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Jumlah Bayar <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="paidAmount"
                                       placeholder="0"
                                       min="0"
                                       oninput="updateChange()"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                              focus:outline-none focus:ring-2 focus:ring-teal-500
                                              dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex justify-between items-center p-3 bg-teal-50 dark:bg-teal-900/20
                                        rounded-lg">
                                <span class="text-sm font-medium text-teal-700 dark:text-teal-300">Kembalian</span>
                                <span id="changeDisplay"
                                      class="text-lg font-bold text-teal-700 dark:text-teal-300">Rp 0</span>
                            </div>
                            <button type="button"
                                    onclick="submitTransaction()"
                                    id="btnSubmit"
                                    class="w-full py-3 bg-teal-600 text-white rounded-xl font-semibold
                                           text-sm hover:bg-teal-700 transition-colors disabled:opacity-50
                                           disabled:cursor-not-allowed">
                                Proses Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ===== CART STATE =====
let cart = [];
let lastTransactionId = null;

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
}

// ===== CART FUNCTIONS =====
function addToCart(id, name, price, stock) {
    const existing = cart.find(i => i.id == id);
    if (existing) {
        if (existing.qty >= stock) {
            showScanFeedback('Stok ' + name + ' tidak mencukupi!', 'error');
            return;
        }
        existing.qty++;
    } else {
        cart.push({ id, name, price: parseFloat(price), qty: 1, stock });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id != id);
    renderCart();
}

function changeQty(id, val) {
    const item = cart.find(i => i.id == id);
    if (!item) return;
    const qty = parseInt(val) || 1;
    if (qty < 1) { removeFromCart(id); return; }
    if (qty > item.stock) {
        showScanFeedback('Stok tidak mencukupi!', 'error');
        return;
    }
    item.qty = qty;
    renderCart();
}

function resetCart() {
    cart = [];
    renderCart();
    document.getElementById('customerName').value = '';
    document.getElementById('paymentMethodId').value = '';
    document.getElementById('paidAmount').value = '';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    document.getElementById('scanFeedback').classList.add('hidden');
}

function renderCart() {
  const container = document.getElementById('cartItems');

  if (cart.length === 0) {
    container.innerHTML = `
      <div id="emptyCart" class="flex flex-col items-center justify-center
                                 py-16 text-gray-400">
        <svg class="w-16 h-16 mb-3 text-gray-300" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293
                   c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8
                   2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-sm font-medium">Keranjang Kosong</p>
        <p class="text-xs mt-1">Pilih produk untuk memulai transaksi</p>
      </div>`;
    document.getElementById('subtotalDisplay').textContent = 'Rp 0';
    document.getElementById('totalDisplay').textContent = 'Rp 0';
    document.getElementById('changeDisplay').textContent = 'Rp 0';
    return;
  }

  let total = 0;
  let html = '';
  cart.forEach(item => {
    const sub = item.price * item.qty;
    total += sub;
    html += `
      <div class="flex items-start gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg
                  border border-gray-200 dark:border-gray-700">
        <div class="flex-1 min-w-0">
          <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate">
            ${item.name}
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            ${formatRupiah(item.price)} × ${item.qty}
          </div>
          <div class="text-xs font-bold text-teal-600 dark:text-teal-400">
            ${formatRupiah(sub)}
          </div>
        </div>
        <div class="flex items-center gap-1 flex-shrink-0">
          <button type="button" onclick="changeQty(${item.id}, ${item.qty - 1})"
                  class="w-6 h-6 flex items-center justify-center bg-gray-200
                         dark:bg-gray-700 rounded text-sm font-bold
                         hover:bg-gray-300">−</button>
          <input type="number" value="${item.qty}" min="1" max="${item.stock}"
                 onchange="changeQty(${item.id}, parseInt(this.value))"
                 class="w-10 text-center text-xs border border-gray-300 rounded
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white py-0.5">
          <button type="button" onclick="changeQty(${item.id}, ${item.qty + 1})"
                  class="w-6 h-6 flex items-center justify-center bg-gray-200
                         dark:bg-gray-700 rounded text-sm font-bold
                         hover:bg-gray-300">+</button>
          <button type="button" onclick="removeFromCart(${item.id})"
                  class="w-6 h-6 flex items-center justify-center text-red-500
                         hover:bg-red-50 dark:hover:bg-red-900/20 rounded
                         text-xs ml-1 font-bold">✕</button>
        </div>
      </div>`;
  });

  container.innerHTML = html;
  document.getElementById('subtotalDisplay').textContent = formatRupiah(total);
  document.getElementById('totalDisplay').textContent = formatRupiah(total);
  updateChange();
}

function updateChange() {
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const paid  = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paid - total;
    const el = document.getElementById('changeDisplay');
    el.textContent = change >= 0 ? formatRupiah(change) : '- ' + formatRupiah(Math.abs(change));
    el.className = 'text-lg font-bold ' +
        (change >= 0 ? 'text-teal-700 dark:text-teal-300' : 'text-red-600 dark:text-red-400');
}

// ===== SUBMIT TRANSAKSI =====
async function submitTransaction() {
    if (cart.length === 0) { showScanFeedback('Keranjang masih kosong!', 'warning'); return; }
    const pmId = document.getElementById('paymentMethodId').value;
    if (!pmId) { showScanFeedback('Pilih metode pembayaran!', 'warning'); return; }
    const paid = parseFloat(document.getElementById('paidAmount').value);
    if (!paid || paid <= 0) { showScanFeedback('Masukkan jumlah bayar!', 'warning'); return; }
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
    if (paid < total) { showScanFeedback('Jumlah bayar kurang dari total!', 'error'); return; }

    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        const res = await fetch('{{ route("pos.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                items: cart.map(i => ({ product_id: i.id, quantity: i.qty })),
                payment_method_id: pmId,
                paid_amount: paid,
                customer_name: document.getElementById('customerName').value,
            }),
        });
        const data = await res.json();
        if (data.success) {
            lastTransactionId = data.transaction_id;
            document.getElementById('btnPrintResi').disabled = false;
            showScanFeedback(
                '✓ Transaksi berhasil! Kembalian: ' + formatRupiah(data.change),
                'success'
            );
            showPopupNotification('Transaksi berhasil!.', 'success');
            resetCart();
        } else {
            const message = data.message || 'Terjadi kesalahan.';
            showScanFeedback('Gagal: ' + message, 'error');
            showPopupNotification('Gagal menyimpan transaksi: ' + message, 'error');
        }
    } catch(e) {
        showScanFeedback('Error koneksi. Coba lagi.', 'error');
        showPopupNotification('Error koneksi. Coba lagi.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Proses Transaksi';
    }
}

function printLastResi() {
    if (!lastTransactionId) return;
    window.open('{{ url("/pos/resi") }}/' + lastTransactionId, '_blank');
}

// ===== FILTER PRODUK =====
function filterProducts() {
    const q = document.getElementById('searchProduct').value.toLowerCase().trim();
    document.querySelectorAll('.product-card').forEach(card => {
        const name    = card.dataset.name || '';
        const sku     = card.dataset.sku  || '';
        const match   = name.includes(q) || sku.includes(q);
        card.classList.toggle('hidden', !match);
    });
}

let activeCategory = null;
function filterByCategory(catId) {
    activeCategory = catId;
    document.querySelectorAll('.category-chip').forEach(btn => {
        const active = (catId === null && btn.dataset.category === 'all') ||
                       (String(btn.dataset.category) === String(catId));
        btn.className = btn.className
            .replace(/bg-teal-600 text-white/g, '')
            .replace(/bg-white border border-gray-300 text-gray-700 hover:bg-gray-50/g, '')
            .trim();
        if (active) {
            btn.classList.add('bg-teal-600', 'text-white');
        } else {
            btn.classList.add('bg-white', 'border', 'border-gray-300',
                              'text-gray-700', 'hover:bg-gray-50');
        }
    });
    document.querySelectorAll('.product-card').forEach(card => {
        const show = catId === null || String(card.dataset.category) === String(catId);
        card.classList.toggle('hidden', !show);
    });
}

// ===== SCAN FEEDBACK =====
function showScanFeedback(msg, type) {
    const el = document.getElementById('scanFeedback');
    const styles = {
        success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        error:   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        loading: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    };
    el.className = 'mb-3 p-2 rounded-lg text-xs text-center ' + (styles[type] || styles.loading);
    el.textContent = msg;
    el.classList.remove('hidden');
    if (type === 'success' || type === 'warning') {
        setTimeout(() => el.classList.add('hidden'), 3000);
    }
}

function showPopupNotification(message, type = 'success') {
    const toast = document.getElementById('popupToast');
    const colors = {
        success: 'border-green-200 bg-green-50 text-green-800',
        error:   'border-red-200 bg-red-50 text-red-800',
        warning: 'border-yellow-200 bg-yellow-50 text-yellow-800',
        loading: 'border-blue-200 bg-blue-50 text-blue-800',
    };
    toast.className = 'fixed right-4 top-24 z-50 rounded-2xl border px-4 py-3 text-sm font-medium shadow-2xl backdrop-blur transition-all duration-300 ease-out';
    const colorClasses = colors[type] ? colors[type].split(' ') : colors.success.split(' ');
    toast.classList.add(...colorClasses);
    toast.textContent = message;
    toast.classList.remove('hidden', 'opacity-0');
    toast.classList.add('opacity-100');

    if (window.popupToastTimer) {
        clearTimeout(window.popupToastTimer);
    }
    window.popupToastTimer = setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4000);
}

// ===== FULLSCREEN =====
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen();
    }
}

// ===== BARCODE HARDWARE SCANNER =====
let barcodeBuffer = '';
let barcodeTimer  = null;
document.addEventListener('keydown', function(e) {
    const active = document.activeElement;
    if (active && active.id === 'barcodeInput') return;
    if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) return;
    if (e.key === 'Enter') {
        if (barcodeBuffer.length >= 6) {
            e.preventDefault();
            const code = barcodeBuffer.trim();
            barcodeBuffer = '';
            if (barcodeTimer) clearTimeout(barcodeTimer);
            document.getElementById('barcodeInput').value = code;
            searchByBarcodeValue(code);
        }
        return;
    }
    if (e.key.length === 1) {
        barcodeBuffer += e.key;
        if (barcodeTimer) clearTimeout(barcodeTimer);
        barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 100);
    }
});

document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = this.value.trim();
        if (val.length >= 6) searchByBarcodeValue(val);
    }
});

async function searchByBarcodeValue(barcode) {
  showScanFeedback('Mencari produk...', 'loading');
  try {
    const res = await fetch(
      '{{ route("pos.scan-barcode") }}?barcode=' + encodeURIComponent(barcode),
      {
        headers: {
          'Accept': 'application/json'
          // DO NOT send X-CSRF-TOKEN for GET requests
        }
      }
    );

    if (!res.ok) {
      showScanFeedback('Gagal menghubungi server (' + res.status + ').', 'error');
      return;
    }

    const data = await res.json();
    if (data.found) {
      const p = data.product;
      const existing = cart.find(i => i.id == p.id);
      if (existing) {
        if (existing.qty >= p.stock) {
          showScanFeedback('Stok ' + p.name + ' tidak mencukupi!', 'error');
          return;
        }
        existing.qty++;
      } else {
        cart.push({
          id: p.id, name: p.name,
          price: parseFloat(p.price), qty: 1, stock: p.stock
        });
      }
      renderCart();
      showScanFeedback('✓ ' + p.name + ' ditambahkan!', 'success');
      document.getElementById('barcodeInput').value = '';
      document.getElementById('barcodeInput').focus();
    } else {
      showScanFeedback(data.message || 'Produk tidak ditemukan.', 'error');
      document.getElementById('barcodeInput').select();
    }
  } catch(e) {
    showScanFeedback('Error jaringan: ' + e.message, 'error');
  }
}

// ===== KAMERA SCANNER (QuaggaJS) =====
let cameraActive = false, scannerRunning = false;
let lastScanned = '', lastScannedTime = 0;

function toggleCamera() {
    cameraActive ? closeCamera() : openCamera();
}

function openCamera() {
    document.getElementById('cameraSection').classList.remove('hidden');
    cameraActive = true;
    Quagga.init({
        inputStream: {
            name: 'Live', type: 'LiveStream',
            target: document.querySelector('#barcodeVideo'),
            constraints: { width: { ideal: 640 }, height: { ideal: 480 },
                           facingMode: 'environment' },
        },
        locator:    { patchSize: 'medium', halfSample: true },
        numOfWorkers: 2, frequency: 10,
        decoder: {
            readers: ['ean_reader','ean_8_reader','upc_reader',
                        'code_128_reader','code_39_reader'],
        },
        locate: true,
    }, err => {
        if (err) {
            showScanFeedback('Tidak bisa akses kamera. Berikan izin kamera.', 'error');
            closeCamera(); return;
        }
        Quagga.start();
        scannerRunning = true;
    });

    Quagga.onDetected(result => {
        if (!result?.codeResult?.code) return;
        const code = result.codeResult.code;
        const now  = Date.now();
        if (code === lastScanned && (now - lastScannedTime) < 2000) return;
        lastScanned = code; lastScannedTime = now;
        document.getElementById('barcodeInput').value = code;
        searchByBarcodeValue(code);
    });
}

function closeCamera() {
    if (scannerRunning) { Quagga.stop(); scannerRunning = false; }
    const v = document.querySelector('#barcodeVideo');
    if (v?.srcObject) { v.srcObject.getTracks().forEach(t => t.stop()); v.srcObject = null; }
    document.getElementById('cameraSection').classList.add('hidden');
    cameraActive = false;
}

window.addEventListener('beforeunload', closeCamera);
window.addEventListener('load', () => {
  renderCart(); // show empty state on load
  const inp = document.getElementById('barcodeInput');
  if (inp) inp.focus();
});
    </script>
</body>
</html>
