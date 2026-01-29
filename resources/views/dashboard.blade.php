<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('Dashboard') }}
        </h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    Welcome back, {{ Auth::user()->name }}!
                </p>
            </div>
        </div>
    </x-slot>

    @php($isSuper = Auth::user()->is_super_admin)
    <div class="grid grid-cols-1 gap-6">
        @if ($isSuper)
            <!-- Row 1: three KPI cards in a single row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Companies</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Team::count() }}</p>
                        </div>
                        <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4m0 6l9 4 9-4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Users</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Added new plans</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Plan::where('created_at','>=', now()->subDays(30))->count() }}</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: two charts side-by-side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Revenue by provider') }}</h3>
                    <div class="relative" style="height: 220px;">
                        <canvas id="chart-provider"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Orders by status') }}</h3>
                    <div class="relative" style="height: 220px;">
                        <canvas id="chart-status"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bottom full-width: revenue over time -->
            <div class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Revenue (last 30 days)') }}</h3>
                <div class="relative overflow-hidden" style="height: 280px;">
                    <canvas id="chart-revenue"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                (function(){
                    const rev = document.getElementById('chart-revenue');
                    if (!rev) return;
                    const fmtMoney = (v) => (v/100).toFixed(2);
                    fetch(@json(route('dashboard.charts')))
                        .then(r => r.json())
                        .then(d => {
                            const showNoData = (canvasId, message = 'No Data') => {
                                const canvas = document.getElementById(canvasId);
                                if (!canvas) return;
                                const parent = canvas.parentElement;
                                canvas.remove();
                                const div = document.createElement('div');
                                div.className = 'absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-slate-400';
                                div.textContent = message;
                                parent.appendChild(div);
                            };

                            // Revenue line chart
                            const revenueTotal = (d.line.data || []).reduce((a,b)=>a+(+b||0),0);
                            if (revenueTotal === 0) {
                                showNoData('chart-revenue');
                            } else {
                                new Chart(rev, {
                                type: 'line',
                                data: {
                                    labels: d.line.labels,
                                    datasets: [{
                                        label: 'Revenue',
                                        data: d.line.data,
                                        borderColor: '#2563eb',
                                        backgroundColor: 'rgba(37,99,235,0.15)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                    }]
                                },
                                options: {
                                    scales: { 
                                        x: {
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45,
                                                maxTicksLimit: 10
                                            }
                                        },
                                        y: { 
                                            ticks: { 
                                                callback: (v)=> '$' + fmtMoney(v) 
                                            } 
                                        } 
                                    },
                                    plugins: { 
                                        legend: { display: false },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            callbacks: {
                                                label: function(context) {
                                                    return '$' + fmtMoney(context.parsed.y);
                                                }
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            right: 5,
                                            top: 10
                                        }
                                    }
                                }
                                });
                            }

                            // Provider doughnut chart
                            const providerSum = (d.providers.data || []).reduce((a,b)=>a+(+b||0),0);
                            if (providerSum === 0) {
                                showNoData('chart-provider');
                            } else {
                                new Chart(document.getElementById('chart-provider'), {
                                type: 'doughnut',
                                data: {
                                    labels: d.providers.labels,
                                    datasets: [{
                                        data: d.providers.data,
                                        backgroundColor: ['#2563eb','#16a34a','#f59e0b','#dc2626','#7c3aed']
                                    }]
                                },
                                options: {
                                    plugins: { 
                                        legend: { 
                                            position: 'bottom',
                                            labels: {
                                                boxWidth: 12,
                                                padding: 8
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                    return context.label + ': $' + fmtMoney(context.parsed) + ' (' + percentage + '%)';
                                                }
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            bottom: 10
                                        }
                                    }
                                }
                                });
                            }

                            // Status pie chart
                            const statusSum = (d.statuses.data || []).reduce((a,b)=>a+(+b||0),0);
                            if (statusSum === 0) {
                                showNoData('chart-status');
                            } else {
                                new Chart(document.getElementById('chart-status'), {
                                type: 'pie',
                                data: {
                                    labels: d.statuses.labels,
                                    datasets: [{
                                        data: d.statuses.data,
                                        backgroundColor: ['#16a34a','#f59e0b','#dc2626','#6b7280']
                                    }]
                                },
                                options: {
                                    plugins: { 
                                        legend: { 
                                            position: 'bottom',
                                            labels: {
                                                boxWidth: 12,
                                                padding: 8
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                                }
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            bottom: 10
                                        }
                                    }
                                }
                                });
                            }
                        });
                })();
            </script>
        @else
            @php($team = Auth::user()->currentTeam())
            
            <!-- KPI Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">{{ __('Team Members') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="users-count">{{ $team ? $team->users()->count() : 0 }}</p>
                        </div>
                        <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">{{ __('Total Orders') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="total-orders">—</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">{{ __('Total Spent') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="total-spent">—</p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-stat">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">{{ __('Pending Requests') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="pending-requests">—</p>
                        </div>
                        <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription & Recent Activity Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Subscription Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Active Subscription') }}</h3>
                    <div id="subscription-info">
                        <div class="flex items-center justify-center py-8">
                            <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Orders by Status') }}</h3>
                        <div class="relative" style="height: 200px;">
                            <canvas id="chart-status"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Payment History (30 days)') }}</h3>
                        <div class="relative overflow-hidden" style="height: 200px;">
                            <canvas id="chart-payments"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Orders') }}</h3>
                    <a href="{{ route('company.orders.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('View All') }}</a>
                </div>
                <div id="recent-orders" class="space-y-2">
                    <div class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                (function(){
                    fetch("{{ route('dashboard.company_data') }}")
                        .then(response => response.json())
                        .then(data => {
                            const showNoData = (canvasId, message = 'No Data') => {
                                const canvas = document.getElementById(canvasId);
                                if (!canvas) return;
                                const parent = canvas.parentElement;
                                canvas.remove();
                                const div = document.createElement('div');
                                div.className = 'absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-slate-400';
                                div.textContent = message;
                                parent.appendChild(div);
                            };
                            // Update KPI cards
                            document.getElementById('total-orders').textContent = data.stats.totalOrders || 0;
                            const totalSpent = data.stats.totalSpent || 0;
                            document.getElementById('total-spent').textContent = '$' + (totalSpent / 100).toFixed(2);
                            document.getElementById('pending-requests').textContent = data.stats.pendingRequests || 0;

                            // Subscription info
                            const subInfo = document.getElementById('subscription-info');
                            if (data.activePlan) {
                                subInfo.innerHTML = `
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">${data.activePlan.name}</p>
                                            <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">${data.activePlan.interval}</p>
                                        </div>
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-slate-700">
                                            <span class="text-sm text-gray-600 dark:text-slate-400">{{ __('Amount') }}</span>
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">${data.activePlan.currency.toUpperCase()} ${(data.activePlan.amount / 100).toFixed(2)}</span>
                                        </div>
                                        <div class="pt-2">
                                            <a href="{{ route('billing.choose') }}" class="block text-center px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700 text-sm">
                                                {{ __('Manage Subscription') }}
                                            </a>
                                        </div>
                                    </div>
                                `;
                            } else {
                                subInfo.innerHTML = `
                                    <div class="text-center py-8">
                                        <p class="text-gray-600 dark:text-slate-400 mb-4">{{ __('No active subscription') }}</p>
                                        <a href="{{ route('billing.choose') }}" class="inline-block px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700 text-sm">
                                            {{ __('Subscribe Now') }}
                                        </a>
                                    </div>
                                `;
                            }

                            // Status chart
                            const cStatusSum = (data.chart.statuses.data || []).reduce((a,b)=>a+(+b||0),0);
                            if (cStatusSum === 0) {
                                showNoData('chart-status');
                            } else {
                                new Chart(document.getElementById('chart-status'), {
                                type: 'doughnut',
                                data: {
                                    labels: data.chart.statuses.labels,
                                    datasets: [{
                                        data: data.chart.statuses.data,
                                        backgroundColor: ['#16a34a', '#f59e0b', '#dc2626', '#6b7280', '#8b5cf6']
                                    }]
                                },
                                options: {
                                    plugins: { 
                                        legend: { 
                                            position: 'bottom',
                                            labels: {
                                                boxWidth: 12,
                                                padding: 8
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            bottom: 10
                                        }
                                    }
                                }
                                });
                            }

                            // Payments chart
                            const cRevenueTotal = (data.chart.line.data || []).reduce((a,b)=>a+(+b||0),0);
                            if (cRevenueTotal === 0) {
                                showNoData('chart-payments');
                            } else {
                                new Chart(document.getElementById('chart-payments'), {
                                type: 'line',
                                data: {
                                    labels: data.chart.line.labels,
                                    datasets: [{
                                        label: 'Amount',
                                        data: data.chart.line.data,
                                        borderColor: '#2563eb',
                                        backgroundColor: 'rgba(37,99,235,0.15)',
                                        tension: 0.3,
                                        fill: true
                                    }]
                                },
                                options: {
                                    scales: {
                                        x: {
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45,
                                                maxTicksLimit: 10
                                            }
                                        },
                                        y: {
                                            ticks: {
                                                callback: (v) => '$' + (v / 100).toFixed(2)
                                            }
                                        }
                                    },
                                    plugins: { 
                                        legend: { display: false },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            right: 5
                                        }
                                    }
                                }
                                });
                            }

                            // Recent orders
                            const ordersDiv = document.getElementById('recent-orders');
                            if (data.recentOrders && data.recentOrders.length > 0) {
                                ordersDiv.innerHTML = data.recentOrders.map(order => {
                                    const statusClass = order.status === 'succeeded' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                        : order.status === 'pending'
                                        ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400';
                                    const statusText = order.status === 'succeeded' ? '{{ __("Succeeded") }}' 
                                        : order.status === 'pending' ? '{{ __("Pending") }}'
                                        : '{{ __("Failed") }}';
                                    return `
                                        <div class="flex items-center justify-between p-3 rounded-xl border bg-white dark:bg-slate-900 dark:border-slate-800">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">${order.invoice_number || '—'}</p>
                                                <p class="text-xs text-gray-600 dark:text-slate-400">${order.date}</p>
                                            </div>
                                            <div class="text-right mr-4">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">${order.currency.toUpperCase()} ${(order.amount / 100).toFixed(2)}</p>
                                                <p class="text-xs text-gray-500 dark:text-slate-500 capitalize">${order.provider}</p>
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded-full ${statusClass}">${statusText}</span>
                                        </div>
                                    `;
                                }).join('');
                            } else {
                                ordersDiv.innerHTML = `
                                    <p class="text-center text-gray-600 dark:text-slate-400 py-8">{{ __('No recent orders') }}</p>
                                `;
                            }
                        })
                        .catch(err => {
                            console.error('Error loading dashboard data:', err);
                        });
                })();
            </script>
        @endif
</div>

</x-app-layout>
