@extends('layouts.app')

@section('title', 'Global Chat')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-[calc(100vh-140px)] flex flex-col mb-20">
        <!-- Chat Header -->
        <div class="bg-white rounded-t-3xl shadow-lg border border-gray-100 flex-shrink-0 z-10">
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="fas fa-comments text-lg"></i>
                        </div>
                        <div
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full animate-pulse">
                        </div>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Global Live Chat</h3>
                        <p class="text-xs text-green-600 font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Online
                        </p>
                    </div>
                </div>

                @if (auth()->user()->name === 'Rifal Kurniawan')
                    <button onclick="confirmClearChat()"
                        class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i>
                        Hapus Semua
                    </button>
                @endif
            </div>
        </div>

        <!-- Chat Body -->
        <div class="flex-grow bg-gray-50/50 border-x border-gray-100 relative overflow-hidden backdrop-blur-sm">
            <!-- Background Decoration -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                style="background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 20px 20px;">
            </div>

            <div id="chat-box" class="absolute inset-0 p-4 overflow-y-auto space-y-6 custom-scrollbar">
                <!-- Messages injected here -->
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="bg-white rounded-b-3xl shadow-lg border border-gray-100 p-4 z-10 relative">

            <!-- Mention Suggestions -->
            <div id="mention-box"
                class="hidden absolute bottom-20 left-4 bg-white shadow-xl rounded-2xl border border-gray-100 max-h-40 overflow-y-auto min-w-[200px] z-50">
                <!-- Suggestions injected here -->
            </div>

            <form id="chat-form" class="relative flex gap-2">
                <div class="relative flex-grow">
                    <input type="text" id="chat-input" placeholder="Ketik pesan... (Gunakan @ untuk tag)"
                        class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-medium placeholder:text-gray-400"
                        autocomplete="off">
                    <div
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold bg-gray-100 px-2 py-1 rounded">
                        ENTER
                    </div>
                </div>
                <button type="submit"
                    class="bg-indigo-600 text-white w-12 h-12 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center justify-center group active:scale-95">
                    <i class="fas fa-paper-plane text-sm group-hover:scale-110 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            border: 2px solid #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .chat-bubble {
            position: relative;
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .chat-bubble.self {
            background: #4f46e5;
            color: white;
            border-bottom-right-radius: 4px;
            margin-left: auto;
        }

        .chat-bubble.other {
            background: white;
            color: #1f2937;
            border-bottom-left-radius: 4px;
            border: 1px solid #f3f4f6;
        }

        .mention-tag {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .other .mention-tag {
            background: #e0e7ff;
            color: #4f46e5;
        }
    </style>
@endsection

@push('scripts')
    <script>
        const currentUser = "{{ auth()->user()->name }}";
        let lastMessageCount = 0;
        let usersList = [];
        const mentionBox = document.getElementById('mention-box');
        const chatInput = document.getElementById('chat-input');

        // Fetch users for mention
        async function fetchUsers() {
            try {
                const res = await fetch('{{ route('chat.users') }}');
                usersList = await res.json();
            } catch (error) {
                console.error('Fetch Users Error:', error);
            }
        }
        fetchUsers();

        // Mention Logic
        chatInput.addEventListener('input', (e) => {
            const val = e.target.value;
            const lastWord = val.split(' ').pop();

            if (lastWord.startsWith('@')) {
                const query = lastWord.substring(1).toLowerCase();
                const matches = usersList.filter(u => u.name.toLowerCase().includes(query));

                if (matches.length > 0) {
                    showMentions(matches);
                } else {
                    mentionBox.classList.add('hidden');
                }
            } else {
                mentionBox.classList.add('hidden');
            }
        });

        function showMentions(users) {
            mentionBox.innerHTML = users.map(user => `
                <div onclick="insertMention('${user.name}')" 
                    class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2 text-sm text-gray-700 font-bold border-b border-gray-50 last:border-0">
                    <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xs shadow-sm">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    ${user.name}
                </div>
            `).join('');
            mentionBox.classList.remove('hidden');
        }

        function insertMention(name) {
            const val = chatInput.value;
            const words = val.split(' ');
            words.pop(); // Remove the incomplete @word
            chatInput.value = words.join(' ') + (words.length > 0 ? ' ' : '') + '@' + name + ' ';
            chatInput.focus();
            mentionBox.classList.add('hidden');
        }

        // Hide mentions when clicking outside
        document.addEventListener('click', (e) => {
            if (!mentionBox.contains(e.target) && e.target !== chatInput) {
                mentionBox.classList.add('hidden');
            }
        });

        function updateChat(messages) {
            const box = document.getElementById('chat-box');
            // Check if user is near bottom before update to decide if we auto-scroll
            const isAtBottom = box.scrollHeight - box.scrollTop <= box.clientHeight + 100;

            if (messages.length === 0) {
                box.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-3 opacity-60">
                        <i class="fas fa-comments text-4xl"></i>
                        <p class="text-sm font-medium">Belum ada percakapan</p>
                    </div>
                `;
                return;
            }

            box.innerHTML = messages.map(msg => {
                const isSelf = msg.user_name === currentUser;
                const initial = msg.user_name.charAt(0).toUpperCase();

                // Highlight mentions
                let content = msg.message.replace(/@(\w+(\s\w+)?)/g,
                    '<span class="mention-tag">@$1</span>');

                if (isSelf) {
                    return `
                        <div class="flex items-end justify-end gap-2 group">
                             <div class="flex flex-col items-end max-w-[80%]">
                                <span class="text-[10px] text-gray-400 mr-1 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </span>
                                <div class="chat-bubble self text-left">
                                    ${content}
                                </div>
                             </div>
                        </div>
                    `;
                } else {
                    return `
                        <div class="flex items-end gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0 cursor-pointer hover:scale-110 transition-transform"
                                onclick="tagUser('${msg.user_name}')" title="Balas ${msg.user_name}">
                                ${initial}
                            </div>
                            <div class="flex flex-col items-start max-w-[80%]">
                                <div class="flex items-center gap-2 mb-1 ml-1">
                                    <span class="text-[11px] font-bold text-gray-600 cursor-pointer hover:text-indigo-600 hover:underline" 
                                        onclick="tagUser('${msg.user_name}')">${msg.user_name}</span>
                                    <span class="text-[10px] text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                        ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                    </span>
                                </div>
                                <div class="chat-bubble other">
                                    ${content}
                                </div>
                            </div>
                        </div>
                    `;
                }
            }).join('');

            // Only scroll if we were already at bottom or it's a new message load
            if (isAtBottom || lastMessageCount === 0) {
                setTimeout(() => {
                    box.scrollTop = box.scrollHeight;
                }, 50);
            }
            lastMessageCount = messages.length;
        }

        function tagUser(name) {
            const input = document.getElementById('chat-input');
            input.value += `@${name} `;
            input.focus();
        }

        async function syncChat() {
            try {
                const response = await fetch('{{ route('chat.messages') }}');
                const messages = await response.json();
                updateChat(messages);
            } catch (error) {
                console.error('Sync Error:', error);
            }
        }

        // Chat Logic
        document.getElementById('chat-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;

            input.value = '';
            // Optimistic update omitted for simplicity, relying on quick sync

            try {
                await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message
                    })
                });
                syncChat();
                // Scroll to bottom immediately after send
                const box = document.getElementById('chat-box');
                box.scrollTop = box.scrollHeight;
            } catch (error) {
                console.error('Chat Error:', error);
            }
        });

        // Admin Clear Chat
        @if (auth()->user()->name === 'Rifal Kurniawan')
            async function confirmClearChat() {
                const result = await Swal.fire({
                    title: 'Hapus Semua Chat?',
                    text: "Tindakan ini tidak bisa dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                });

                if (result.isConfirmed) {
                    try {
                        const res = await fetch('{{ route('chat.clear') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (res.ok) {
                            Swal.fire('Terhapus!', 'Riwayat chat berhasil dihapus.', 'success');
                            syncChat();
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                        }
                    } catch (error) {
                        console.error('Clear Error:', error);
                    }
                }
            }
        @endif

        // Loop sync every 2 seconds for snappier feel
        setInterval(syncChat, 2000);
        syncChat();
    </script>
@endpush
