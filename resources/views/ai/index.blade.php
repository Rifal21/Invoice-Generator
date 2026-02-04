@extends('layouts.app')

@section('title', 'AI Insights Analyst')

@section('content')
    <div
        class="px-3 sm:px-6 lg:px-8 py-4 md:py-6 h-[calc(100vh-6.5rem)] md:h-[calc(100vh-6rem)] flex flex-col relative overflow-hidden">
        <!-- Abstract Background Decorative Elements (Visible mostly on desktop) -->
        <div
            class="hidden md:block absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl -z-10 animate-pulse">
        </div>
        <div class="hidden md:block absolute -bottom-24 -left-24 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl -z-10 animate-pulse"
            style="animation-delay: 2s"></div>

        <!-- Header Section -->
        <div class="mb-4 md:mb-6 flex-shrink-0 flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-5">
                <div class="relative">
                    <div
                        class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-xl shadow-indigo-200 group transition-transform hover:scale-105 duration-300">
                        <i class="fas fa-brain text-xl md:text-2xl group-hover:animate-bounce"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-lg md:text-2xl font-black text-gray-900 uppercase tracking-tighter leading-none">AI
                        Insight Analyst</h1>
                    <p
                        class="text-[10px] md:text-sm font-medium text-gray-500 mt-0.5 md:mt-1 flex items-center gap-1 md:gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Asisten Data Cerdas KJRS
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.location.reload()"
                    class="p-2.5 rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-indigo-600 shadow-sm transition-all"
                    title="Reset Chat">
                    <i class="fas fa-sync-alt text-sm"></i>
                </button>
                <div
                    class="hidden sm:flex items-center gap-3 px-4 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-white shadow-sm">
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</p>
                        <p class="text-xs font-black text-gray-700">Online</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div
            class="flex-1 flex flex-col min-h-0 bg-white shadow-2xl shadow-indigo-100/30 md:bg-white/60 md:backdrop-blur-xl rounded-2xl md:rounded-[2.5rem] border border-gray-100 md:border-white overflow-hidden">
            <!-- Chat Container -->
            <div id="chat-container"
                class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6 md:space-y-8 scroll-smooth custom-scrollbar">
                <!-- Welcome Message -->
                <div class="flex items-start gap-3 md:gap-4">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-br from-gray-900 to-gray-700 flex items-center justify-center text-white flex-shrink-0 shadow-lg">
                        <i class="fas fa-robot text-xs md:text-sm"></i>
                    </div>
                    <div class="flex-1 max-w-[90%] md:max-w-2xl">
                        <div
                            class="bg-gray-50 md:bg-white text-gray-800 p-4 md:p-6 rounded-2xl md:rounded-3xl rounded-tl-none shadow-sm border border-gray-100 leading-relaxed relative">
                            <h3 class="font-black text-indigo-600 mb-2 uppercase tracking-wide text-[10px]">Asisten KJRS
                            </h3>
                            <p class="text-xs md:text-sm font-medium text-gray-600">
                                Halo! Saya siap membantu Anda menganalisis data Koperasi. <br class="hidden md:block"> Apa
                                yang ingin Anda ketahui hari ini?
                            </p>
                            <div class="mt-4 grid grid-cols-1 gap-2">
                                <button onclick="sendSuggested('Ringkasan performa bisnis bulan ini')"
                                    class="p-3 rounded-xl bg-indigo-50/50 border border-indigo-100/50 flex items-center justify-between hover:bg-indigo-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px]">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <span class="text-[10px] font-black uppercase text-indigo-700">Ringkasan
                                            Bisnis</span>
                                    </div>
                                    <i
                                        class="fas fa-chevron-right text-[8px] text-indigo-300 group-hover:translate-x-1 transition-transform"></i>
                                </button>
                                <button onclick="sendSuggested('Siapa pelanggan dengan total beli terbesar?')"
                                    class="p-3 rounded-xl bg-pink-50/50 border border-pink-100/50 flex items-center justify-between hover:bg-pink-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-pink-600 text-white flex items-center justify-center text-[10px]">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <span class="text-[10px] font-black uppercase text-pink-700">Pelanggan VIP</span>
                                    </div>
                                    <i
                                        class="fas fa-chevron-right text-[8px] text-pink-300 group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggested Section (Hidden on very small screens or compact) -->
            <div id="suggested-container"
                class="px-4 md:px-8 py-3 flex gap-2 overflow-x-auto no-scrollbar border-t border-gray-50 bg-gray-50/50 md:bg-white/40 flex-shrink-0">
                <button onclick="sendSuggested('Cek stok produk yang menipis')"
                    class="whitespace-nowrap px-4 py-2 rounded-full bg-white border border-gray-200 text-[10px] md:text-[11px] font-black uppercase tracking-wider text-gray-600 hover:border-indigo-500 hover:shadow-md transition-all">
                    <i class="fas fa-box-open mr-1.5 text-indigo-500"></i> Stok Menipis
                </button>
                <button onclick="sendSuggested('Berapa laba kotor hari ini?')"
                    class="whitespace-nowrap px-4 py-2 rounded-full bg-white border border-gray-200 text-[10px] md:text-[11px] font-black uppercase tracking-wider text-gray-600 hover:border-indigo-500 hover:shadow-md transition-all">
                    <i class="fas fa-wallet mr-1.5 text-green-500"></i> Laba Hari Ini
                </button>
                <button onclick="sendSuggested('Analisis tren penjualan mingguan')"
                    class="whitespace-nowrap px-4 py-2 rounded-full bg-white border border-gray-200 text-[10px] md:text-[11px] font-black uppercase tracking-wider text-gray-600 hover:border-indigo-500 hover:shadow-md transition-all">
                    <i class="fas fa-fire mr-1.5 text-orange-500"></i> Tren Terkini
                </button>
            </div>

            <!-- Input Area -->
            <div class="p-4 md:p-6 bg-white border-t border-gray-100 flex-shrink-0">
                <form id="chat-form"
                    class="max-w-4xl mx-auto flex items-center gap-2 md:gap-4 bg-gray-100 md:bg-gray-50 p-1.5 md:p-2 rounded-2xl md:rounded-[2rem] border border-gray-100 transition-all duration-300 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:bg-white">
                    <input type="text" id="prompt-input"
                        class="flex-1 bg-transparent border-0 focus:ring-0 text-sm font-bold text-gray-700 placeholder:text-gray-400 py-3 md:py-4 px-3 md:px-4"
                        placeholder="Tanya analisis..." required autocomplete="off">
                    <button type="submit" id="submit-btn"
                        class="h-10 w-10 md:h-auto md:w-auto md:px-8 md:py-4 rounded-xl md:rounded-[1.5rem] bg-indigo-600 text-white font-black text-[10px] md:text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 flex items-center justify-center gap-2 transition-all active:scale-95">
                        <span id="btn-text" class="hidden md:inline">Analis</span>
                        <i class="fas fa-paper-plane md:text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Premium Markdown Styling */
        .markdown-content h1,
        .markdown-content h2,
        .markdown-content h3 {
            font-weight: 800;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            color: #1e293b;
            letter-spacing: -0.01em;
        }

        .markdown-content h2 {
            border-left: 3px solid #6366f1;
            padding-left: 10px;
            font-size: 1rem;
        }

        .markdown-content p {
            margin-bottom: 0.75rem;
            line-height: 1.6;
            font-size: 0.9rem;
            color: #475569;
        }

        .markdown-content ul {
            list-style: none;
            margin-bottom: 0.75rem;
        }

        .markdown-content li {
            position: relative;
            padding-left: 1.25rem;
            margin-bottom: 0.35rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .markdown-content li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #6366f1;
            font-weight: bold;
        }

        .markdown-content table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 1rem 0;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .markdown-content th {
            background: #f8fafc;
            text-align: left;
            padding: 0.75rem;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 1px solid #f1f5f9;
        }

        .markdown-content td {
            padding: 0.75rem;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            background: white;
        }
    </style>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatContainer = document.getElementById('chat-container');
        const promptInput = document.getElementById('prompt-input');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = promptInput.value;
            if (!prompt) return;

            addMessage(prompt, 'user');
            promptInput.value = '';
            setLoading(true);

            try {
                const response = await fetch("{{ route('ai.analyze') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        prompt
                    })
                });

                const data = await response.json();
                if (data.success) {
                    addMessage(data.response, 'ai');
                } else {
                    addMessage('Terjadi gangguan: ' + (data.message || 'Error'), 'error');
                }
            } catch (error) {
                addMessage('Gagal terhubung ke pusat AI.', 'error');
            } finally {
                setLoading(false);
            }
        });

        function sendSuggested(text) {
            promptInput.value = text;
            chatForm.dispatchEvent(new Event('submit'));
        }

        function addMessage(content, type) {
            const wrapper = document.createElement('div');
            wrapper.className =
                `flex items-start gap-3 md:gap-4 ${type === 'user' ? 'justify-end' : ''} animate-in fade-in slide-in-from-bottom-2 duration-300`;

            if (type === 'user') {
                wrapper.innerHTML = `
                    <div class="max-w-[85%] md:max-w-xl">
                        <div class="bg-indigo-600 text-white p-4 md:p-6 rounded-2xl md:rounded-[2rem] rounded-tr-none shadow-lg">
                            <p class="text-xs md:text-sm font-bold leading-relaxed">${content}</p>
                        </div>
                    </div>
                `;
            } else if (type === 'ai') {
                wrapper.innerHTML = `
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg">
                        <i class="fas fa-robot text-[10px] md:text-sm"></i>
                    </div>
                    <div class="flex-1 max-w-[90%] md:max-w-4xl overflow-hidden">
                        <div class="bg-white text-gray-900 p-4 md:p-8 rounded-2xl md:rounded-[2rem] rounded-tl-none border border-gray-100 shadow-sm markdown-content">
                            <h3 class="font-black text-indigo-600 mb-3 uppercase tracking-wider text-[9px] flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Analisis Sistem
                             </h3>
                            <div class="overflow-x-auto">${content}</div>
                        </div>
                    </div>
                `;
            } else {
                wrapper.innerHTML = `
                    <div class="w-full flex justify-center py-2">
                        <div class="bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full border border-red-100 shadow-sm items-center gap-2 flex">
                            <i class="fas fa-exclamation-triangle"></i> ${content}
                        </div>
                    </div>
                `;
            }

            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function setLoading(loading) {
            if (loading) {
                submitBtn.disabled = true;
                if (btnText) btnText.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

                const skeleton = document.createElement('div');
                skeleton.id = 'ai-skeleton';
                skeleton.className = 'flex items-start gap-3 animate-pulse active-skeleton';
                skeleton.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-gray-200"></div>
                    <div class="flex-1 max-w-[70%]">
                        <div class="h-16 bg-gray-100 rounded-2xl rounded-tl-none"></div>
                    </div>
                `;
                chatContainer.appendChild(skeleton);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            } else {
                submitBtn.disabled = false;
                if (btnText) btnText.innerText = 'Analis';
                const skeleton = document.getElementById('ai-skeleton');
                if (skeleton) skeleton.remove();
            }
        }
    </script>
@endsection
