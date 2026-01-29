@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="px-4 py-8">
        <div class="sm:flex sm:items-center mb-8">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Activity Logs</h1>
                <p class="mt-2 text-sm text-gray-700">A detailed log of all create, update, and delete actions performed in
                    the system.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Details
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs ring-2 ring-white shadow-sm">
                                            {{ substr($log->user_name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-xs font-bold text-gray-900">{{ $log->user_name ?? 'System' }}
                                            </div>
                                            <div class="text-[10px] text-gray-500">{{ $log->ip_address }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = match ($log->action) {
                                            'create' => 'green',
                                            'update' => 'amber',
                                            'delete' => 'red',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-md bg-{{ $color }}-50 px-2 py-1 text-xs font-medium text-{{ $color }}-700 ring-1 ring-inset ring-{{ $color }}-600/20 capitalize">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ class_basename($log->model_type) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500">ID: {{ $log->model_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-900 max-w-xs truncate" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="text-indigo-600 hover:text-indigo-900 font-bold text-xs">View
                                            Changes</button>

                                        <!-- Modal for Changes -->
                                        <div x-show="open"
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                                            style="display: none;">
                                            <div @click.away="open = false"
                                                class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 transform transition-all">
                                                <h3 class="text-lg font-black text-gray-900 mb-4">Change Details</h3>
                                                <div
                                                    class="bg-gray-50 rounded-xl p-4 overflow-auto max-h-96 text-xs font-mono text-gray-700">
                                                    <pre>{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                                <div class="mt-6 flex justify-end">
                                                    <button @click="open = false"
                                                        class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-indigo-700">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
