@extends('layouts.app')

@section('title', 'Dashboard')

@section('header_actions')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="glass-card rounded-xl p-6 relative overflow-hidden group">
    <div class="absolute -right-6 -top-6 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all duration-500"></div>
    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h2 class="font-display-lg-mobile text-display-lg-mobile text-on-surface tracking-tight mb-1">Selamat Datang Kembali, {{ $user->name }}</h2>
            <p class="font-code-label text-code-label text-on-surface-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">sync</span> Terakhir Sinkronisasi: {{ $user->synced_at ? \Carbon\Carbon::parse($user->synced_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : '-' }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 bg-primary/10 text-primary border border-primary/20 px-3 py-1 rounded-full font-code-label text-xs uppercase tracking-wider">
            {{ $user->role }}
        </div>
    </div>
</div>

<!-- Quick Stats Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stat Card 1 -->
    <div class="glass-card rounded-xl p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-surface border border-outline-variant/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary">inventory_2</span>
            </div>
            <span class="flex items-center text-primary font-code-label text-code-label">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +12.5%
            </span>
        </div>
        <div>
            <div class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-1">Total Barang</div>
            <div class="font-display-lg text-display-lg text-on-surface neon-text-primary">{{ number_format($totalItems) }}</div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="glass-card rounded-xl p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-error/5 rounded-full blur-2xl group-hover:bg-error/10 transition-all duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-surface border border-outline-variant/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-error">warning</span>
            </div>
            <span class="flex items-center text-error font-code-label text-code-label">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_down</span> Action
            </span>
        </div>
        <div>
            <div class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-1">Stok Menipis</div>
            <div class="font-display-lg text-display-lg text-on-surface">{{ number_format($lowStockItems) }}</div>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="glass-card rounded-xl p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-secondary/5 rounded-full blur-2xl group-hover:bg-secondary/10 transition-all duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-surface border border-outline-variant/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary">shopping_cart</span>
            </div>
            <span class="flex items-center text-primary font-code-label text-code-label">
                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> Today
            </span>
        </div>
        <div>
            <div class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-1">Transaksi Hari Ini</div>
            <div class="font-display-lg text-display-lg text-on-surface">{{ number_format($todayTransactions) }}</div>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="glass-card rounded-xl p-6 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-black/5 rounded-full blur-2xl group-hover:bg-black/10 transition-all duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-surface border border-outline-variant/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-on-surface-variant">local_shipping</span>
            </div>
            <span class="flex items-center text-on-surface-variant font-code-label text-code-label">
                <span class="material-symbols-outlined text-[16px] mr-1">horizontal_rule</span> Active
            </span>
        </div>
        <div>
            <div class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-1">Supplier Aktif</div>
            <div class="font-display-lg text-display-lg text-on-surface">{{ number_format($activeSuppliers) }}</div>
        </div>
    </div>
</div>

<!-- Main Data Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-2">
    <!-- Grafik Tren Stok (Volume) -->
    <div class="glass-card rounded-xl p-6 lg:col-span-2 flex flex-col">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="flex flex-col gap-2">
                <h3 class="font-headline-md text-headline-md text-on-surface">Volume Inventaris</h3>
                <!-- Custom Legend -->
                <div class="flex items-center gap-3 text-xs font-code-label">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-[#22c55e] dark:bg-[#4ade80]"></span>
                        <span class="text-on-surface-variant">Barang Masuk</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-[#ef4444] dark:bg-[#f87171]"></span>
                        <span class="text-on-surface-variant">Barang Keluar</span>
                    </div>
                </div>
            </div>
            <div class="flex bg-surface/50 rounded-lg p-1 border border-outline-variant/30">
                <a href="?range=7H" class="px-3 py-1 text-xs font-code-label rounded {{ request('range', '7H') == '7H' ? 'bg-white shadow-sm text-on-surface' : 'text-on-surface-variant hover:text-on-surface' }}">7H</a>
                <a href="?range=30H" class="px-3 py-1 text-xs font-code-label rounded {{ request('range') == '30H' ? 'bg-white shadow-sm text-on-surface' : 'text-on-surface-variant hover:text-on-surface' }}">30H</a>
                <a href="?range=YTD" class="px-3 py-1 text-xs font-code-label rounded {{ request('range') == 'YTD' ? 'bg-white shadow-sm text-on-surface' : 'text-on-surface-variant hover:text-on-surface' }}">YTD</a>
            </div>
        </div>
        <div class="flex-1 relative min-h-[250px] w-full flex items-end mt-4">
            <canvas id="inventoryChart" role="img" aria-label="Grafik perbandingan volume barang masuk dan keluar per hari"></canvas>
        </div>
    </div>
    
    <!-- Data Container Grafik (to prevent JS linter errors in VS Code) -->
    <div id="dashboardChartData" class="hidden"
         data-dates="{{ json_encode($chartDates) }}"
         data-masuk="{{ json_encode($chartMasuk) }}"
         data-keluar="{{ json_encode($chartKeluar) }}">
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('inventoryChart').getContext('2d');
            
            const chartDataEl = document.getElementById('dashboardChartData');
            const chartLabels = JSON.parse(chartDataEl.dataset.dates);
            const chartMasuk = JSON.parse(chartDataEl.dataset.masuk);
            const chartKeluar = JSON.parse(chartDataEl.dataset.keluar);

            const isDarkMode = document.documentElement.classList.contains('dark') || window.matchMedia('(prefers-color-scheme: dark)').matches;
            const colorMasuk = isDarkMode ? '#4ade80' : '#22c55e';
            const colorKeluar = isDarkMode ? '#f87171' : '#ef4444';

            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: chartMasuk,
                            backgroundColor: colorMasuk,
                            borderRadius: 4,
                            maxBarThickness: 20
                        },
                        {
                            label: 'Barang Keluar',
                            data: chartKeluar,
                            backgroundColor: colorKeluar,
                            borderRadius: 4,
                            maxBarThickness: 20
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false 
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#a0a0a0',
                                font: {
                                    family: "'JetBrains Mono', monospace"
                                },
                                stepSize: 1,
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#a0a0a0',
                                font: {
                                    family: "'JetBrains Mono', monospace"
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    
    <!-- Recent Activity / Action List -->
    <div class="glass-card rounded-xl p-6 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-headline-md text-on-surface">Perlu Tindakan</h3>
            <button class="text-secondary hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined text-[20px]">more_horiz</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto space-y-4 pr-2">
            @forelse($actionItems as $item)
            <!-- Action Item -->
            <a href="{{ route('items.edit', $item) }}" class="block p-3 rounded-lg bg-white/60 border border-outline-variant/30 hover:bg-white transition-colors cursor-pointer shadow-sm group">
                <div class="flex gap-3">
                    <div class="mt-1">
                        <div class="w-2 h-2 rounded-full {{ $item->current_stock == 0 ? 'bg-error shadow-[0_0_8px_rgba(255,0,51,0.8)]' : 'bg-warning shadow-[0_0_8px_rgba(255,165,0,0.8)]' }}"></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-on-surface mb-1 group-hover:text-primary transition-colors">Restock: {{ $item->name }}</div>
                        <div class="font-code-label text-code-label text-on-surface-variant flex items-center flex-wrap gap-2">
                            <span>Kategori: {{ $item->category ? $item->category->name : '-' }}</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full hidden sm:block"></span>
                            <span class="{{ $item->current_stock == 0 ? 'text-error' : 'text-warning' }}">Sisa {{ $item->current_stock }} {{ $item->unit }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-on-surface-variant opacity-70 pb-10">
                <span class="material-symbols-outlined text-4xl mb-2">check_circle</span>
                <p class="text-sm font-medium">Semua stok aman!</p>
                <p class="text-xs mt-1">Tidak ada tindakan yang diperlukan.</p>
            </div>
            @endforelse
        </div>
        <a href="{{ route('items.index', ['condition' => 'low_stock']) }}" class="mt-4 block w-full text-center py-2 bg-transparent border border-secondary text-secondary rounded-lg font-code-label text-sm hover:bg-secondary/10 hover:shadow-[0_0_10px_rgba(0,255,255,0.2)] transition-all">
            Lihat Semua Peringatan
        </a>
    </div>
</div>
@endsection