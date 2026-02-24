@extends('layouts.app')

@section('title', 'Kalender Invoice')

@push('scripts')
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        .fc-event {
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 0.75rem !important;
            padding: 2px 4px !important;
        }

        @media (max-width: 640px) {
            .fc-event {
                font-size: 0.6rem !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .fc-toolbar {
                display: flex !important;
                flex-direction: column !important;
                gap: 1rem !important;
            }

            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }

            .fc-daygrid-day-number {
                font-size: 0.75rem !important;
            }

            .fc-col-header-cell-cushion {
                font-size: 0.7rem !important;
            }
        }

        .fc-event:hover {
            transform: scale(1.02);
        }

        .fc-day-today {
            background-color: #f5f3ff !important;
        }

        .fc-toolbar-title {
            font-weight: 900 !important;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .fc-button-primary {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            font-weight: 800 !important;
            border-radius: 12px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem !important;
        }

        /* Modal Responsive */
        @media (max-width: 640px) {
            #invoiceModal .p-8 {
                padding: 1.5rem !important;
            }

            #invoiceModal .px-8 {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }

            #modalDateTitle {
                font-size: 1rem !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-8 gap-6">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs font-black uppercase tracking-[0.2em]">
                        <li><a href="{{ route('invoices.index') }}"
                                class="text-gray-400 hover:text-indigo-600 transition-colors">Invoice</a></li>
                        <li class="text-gray-300">/</li>
                        <li class="text-indigo-600">Kalender</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Kalender Invoice</h1>
                <p class="mt-3 text-base md:text-lg text-gray-500 font-medium tracking-tight">Pantau jadwal dan distribusi
                    invoice harian secara visual.</p>
            </div>
            <div class="flex flex-wrap gap-2 sm:gap-4 mt-4 md:mt-0">
                <a href="{{ route('invoices.index') }}"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 sm:px-6 py-3 sm:py-4 rounded-2xl sm:rounded-[1.5rem] bg-white border border-gray-100 text-gray-600 font-black text-xs sm:text-sm hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-list mr-2"></i> <span class="whitespace-nowrap">List View</span>
                </a>
                <a href="{{ route('invoices.create') }}"
                    class="flex-1 sm:flex-none group inline-flex items-center justify-center px-4 sm:px-8 py-3 sm:py-4 border border-transparent text-xs sm:text-sm font-black rounded-2xl sm:rounded-[1.5rem] text-white bg-indigo-600 shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">
                    <i class="fas fa-plus mr-2"></i> <span class="whitespace-nowrap">Buat Baru</span>
                </a>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="bg-white shadow-2xl rounded-[2.5rem] p-6 md:p-10 border border-gray-100">
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Modal for Invoice List -->
    <div id="invoiceModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-transparent backdrop-blur-sm bg-opacity-75" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block relative z-10 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-[2.5rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="px-8 py-6 bg-indigo-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-white uppercase tracking-widest" id="modalDateTitle">Daftar
                            Invoice</h3>
                        <button onclick="closeModal()" class="text-white hover:text-indigo-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-8">
                    <div id="modalContent" class="space-y-4 max-h-[60vh] overflow-y-auto">
                        <!-- Content loaded via JS -->
                    </div>
                </div>
                <div class="px-8 py-6 bg-gray-50 flex justify-end">
                    <button onclick="closeModal()"
                        class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-black uppercase tracking-widest hover:bg-gray-100 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('turbo:load', function() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            var isMobile = window.innerWidth < 768;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                events: '{{ route('invoices.calendar.events') }}',
                headerToolbar: isMobile ? {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today'
                } : {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                dayHeaderFormat: {
                    weekday: isMobile ? 'narrow' : 'long'
                },
                dateClick: function(info) {
                    // Try to find if there are events on this date
                    const events = calendar.getEvents().filter(e => e.startStr === info.dateStr);
                    showInvoices(info.dateStr, events.length > 0 ? events[0].extendedProps.invoices :
                    []);
                },
                eventClick: function(info) {
                    showInvoices(info.event.startStr, info.event.extendedProps.invoices);
                },
                dayMaxEvents: isMobile ? 2 : true,
                height: 'auto',
                aspectRatio: isMobile ? 0.8 : 1.35,
                windowResize: function(view) {
                    if (window.innerWidth < 768) {
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next',
                            center: 'title',
                            right: 'today'
                        });
                        calendar.setOption('dayHeaderFormat', {
                            weekday: 'narrow'
                        });
                        calendar.setOption('aspectRatio', 0.8);
                    } else {
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek'
                        });
                        calendar.setOption('dayHeaderFormat', {
                            weekday: 'long'
                        });
                        calendar.setOption('aspectRatio', 1.35);
                    }
                }
            });
            calendar.render();
        });

        function showInvoices(date, invoices) {
            const formattedDate = new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('modalDateTitle').innerText = formattedDate;

            let html = '';
            invoices.forEach(inv => {
                let itemsHtml = '';
                if (inv.items && inv.items.length > 0) {
                    itemsHtml = `
                        <div class="mt-3 bg-gray-50 rounded-xl p-3 space-y-2">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-1 mb-2">Item List</p>
                            ${inv.items.map(item => `
                                                                        <div class="flex justify-between items-center text-[11px] font-bold">
                                                                            <div class="flex-1">
                                                                                <span class="text-gray-900">${item.name}</span>
                                                                                <span class="text-gray-400 ml-1">x ${item.qty} ${item.unit}</span>
                                                                            </div>
                                                                            <div class="text-indigo-600 font-black">${item.total}</div>
                                                                        </div>
                                                                    `).join('')}
                        </div>
                    `;
                }

                html += `
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:border-indigo-200 hover:shadow-md transition-all group">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-black text-gray-900 uppercase tracking-tight">${inv.number}</h4>
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">${inv.customer || '-'}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-indigo-600">${inv.total}</p>
                            </div>
                        </div>
                        
                        ${itemsHtml}

                        <div class="mt-4 flex justify-end gap-2">
                            <a href="${inv.url}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                    </div>
                `;
            });

            document.getElementById('modalContent').innerHTML = html ||
                '<div class="text-center py-10 text-gray-400 font-bold uppercase tracking-widest">Tidak ada invoice di tanggal ini.</div>';
            document.getElementById('invoiceModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('invoiceModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
