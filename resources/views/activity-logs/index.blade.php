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

                                        <div x-show="open"
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                                            style="display: none;" x-transition.opacity>
                                            <div @click.away="open = false"
                                                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 transform transition-all flex flex-col max-h-[90vh]">
                                                <div class="flex justify-between items-center mb-4">
                                                    <div>
                                                        <h3 class="text-lg font-black text-gray-900">Detail Perubahan</h3>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $log->action }} on {{ class_basename($log->model_type) }}
                                                            #{{ $log->model_id }}
                                                        </p>
                                                    </div>
                                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                        <i class="fas fa-times text-xl"></i>
                                                    </button>
                                                </div>

                                                <div class="overflow-y-auto flex-1 pr-2">
                                                    @if (isset($log->changes['before']) || isset($log->changes['after']))
                                                        <table class="w-full text-sm text-left">
                                                            <thead
                                                                class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                                                                <tr>
                                                                    <th class="px-4 py-2 w-1/4">Field</th>
                                                                    <th class="px-4 py-2 w-1/3 text-red-600 bg-red-50/50">
                                                                        Sebelum</th>
                                                                    <th
                                                                        class="px-4 py-2 w-1/3 text-green-600 bg-green-50/50">
                                                                        Sesudah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="divide-y divide-gray-100 border-b border-gray-100">
                                                                @php
                                                                    $before = $log->changes['before'] ?? [];
                                                                    $after = $log->changes['after'] ?? [];
                                                                    // Get all unique keys from both arrays
                                                                    $allKeys = array_unique(
                                                                        array_merge(
                                                                            array_keys($before),
                                                                            array_keys($after),
                                                                        ),
                                                                    );
                                                                    // Filter out keys that shouldn't be shown (like timestamps if unchanged, but usually we want to see everything that changed)
                                                                    // Or just iterate all keys.
                                                                @endphp

                                                                @foreach ($allKeys as $key)
                                                                    @php
                                                                        $valBefore = $before[$key] ?? '-';
                                                                        $valAfter = $after[$key] ?? '-';

                                                                        // Skip updated_at if it's the only change? Maybe not, strict audit wants everything.
// But let's format dates nicely
                                                                        if (
                                                                            str_contains($key, '_at') ||
                                                                            str_contains($key, 'date')
                                                                        ) {
                                                                            try {
                                                                                if ($valBefore !== '-') {
                                                                                    $valBefore = \Carbon\Carbon::parse(
                                                                                        $valBefore,
                                                                                    )->format('d M Y H:i:s');
                                                                                }
                                                                                if ($valAfter !== '-') {
                                                                                    $valAfter = \Carbon\Carbon::parse(
                                                                                        $valAfter,
                                                                                    )->format('d M Y H:i:s');
                                                                                }
                                                                            } catch (\Exception $e) {
                                                                            }
                                                                        }

                                                                        // Format numbers/money
                                                                        if (
                                                                            in_array($key, [
                                                                                'amount',
                                                                                'price',
                                                                                'total',
                                                                                'salary',
                                                                                'balance',
                                                                            ])
                                                                        ) {
                                                                            if (is_numeric($valBefore)) {
                                                                                $valBefore = number_format(
                                                                                    $valBefore,
                                                                                    0,
                                                                                    ',',
                                                                                    '.',
                                                                                );
                                                                            }
                                                                            if (is_numeric($valAfter)) {
                                                                                $valAfter = number_format(
                                                                                    $valAfter,
                                                                                    0,
                                                                                    ',',
                                                                                    '.',
                                                                                );
                                                                            }
                                                                        }

                                                                        // Handle Arrays/Objects
                                                                        if (is_array($valBefore)) {
                                                                            $valBefore = json_encode($valBefore);
                                                                        }
                                                                        if (is_array($valAfter)) {
                                                                            $valAfter = json_encode($valAfter);
                                                                        }
                                                                    @endphp
                                                                    <tr class="hover:bg-gray-50/50">
                                                                        <td
                                                                            class="px-4 py-3 font-medium text-gray-700 font-mono text-xs border-r border-gray-50">
                                                                            {{ $key }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-red-600 bg-red-50/20 font-mono text-xs break-all">
                                                                            {{ $valBefore }}</td>
                                                                        <td
                                                                            class="px-4 py-3 text-green-600 bg-green-50/20 font-mono text-xs break-all">
                                                                            {{ $valAfter }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <div class="text-center py-8 text-gray-500 italic">
                                                            Tidak ada detail field yang tercatat (mungkin data raw).
                                                            <pre class="mt-4 text-xs font-mono bg-gray-50 p-3 rounded-lg text-left overflow-auto max-h-40">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                                                    <div class="mr-auto text-xs text-gray-400 self-center">
                                                        Browser: {{ Str::limit($log->user_agent, 40) }}
                                                    </div>
                                                    <button @click="open = false"
                                                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">Tutup</button>
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
