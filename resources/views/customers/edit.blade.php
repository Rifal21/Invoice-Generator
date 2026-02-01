@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('customers.index') }}"
                            class="text-sm font-bold text-gray-400 hover:text-indigo-600">Pelanggan</a></li>
                    <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg></li>
                    <li class="text-sm font-bold text-indigo-600">Edit</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Edit Pelanggan</h1>
            <p class="mt-2 text-sm text-gray-500">Perbarui data pelanggan.</p>
        </div>

        <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
            <form action="{{ route('customers.update', $customer) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Pelanggan <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pic" class="block text-sm font-bold text-gray-700 mb-2">PIC</label>
                        <input type="text" name="pic" id="pic" value="{{ old('pic', $customer->pic) }}"
                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @error('pic')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telegram_chat_id" class="block text-sm font-bold text-gray-700 mb-2">Telegram Chat
                            ID</label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                            value="{{ old('telegram_chat_id', $customer->telegram_chat_id) }}"
                            placeholder="Contoh: 7817420087"
                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @error('telegram_chat_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="bank_account_info" class="block text-sm font-bold text-gray-700 mb-2">Info Rekening
                        Pembayaran</label>
                    <textarea name="bank_account_info" id="bank_account_info" rows="2"
                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                        placeholder="Contoh: BCA 1234567890 a.n Nama">{{ old('bank_account_info', $customer->bank_account_info) }}</textarea>
                    @error('bank_account_info')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-bold text-gray-700 mb-2">Alamat</label>
                    <textarea name="address" id="address" rows="3"
                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Tambahan</label>
                    <textarea name="description" id="description" rows="2"
                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">{{ old('description', $customer->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('customers.index') }}"
                        class="px-6 py-3 border border-gray-300 rounded-2xl text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-3 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Perbarui Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
