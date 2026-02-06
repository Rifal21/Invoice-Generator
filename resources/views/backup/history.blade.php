@forelse($backups as $backup)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
            {{ $backup->created_at->format('d/m/Y H:i') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-900">
            @if ($backup->type === 'database')
                Full Database (SQL/Excel)
            @elseif ($backup->type === 'products')
                Data Produk (Excel)
            @elseif ($backup->type === 'weekly' && $backup->start_date && $backup->end_date)
                {{ $backup->start_date->format('d/m/Y') }} - {{ $backup->end_date->format('d/m/Y') }}
            @elseif ($backup->type === 'weekly')
                Minggu {{ $backup->period_week }}, {{ $backup->period_year }}
            @else
                {{ \Carbon\Carbon::createFromDate($backup->period_year, $backup->period_month, 1)->format('F Y') }}
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-xs">
            @if ($backup->status == 'completed')
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sukses</span>
            @elseif($backup->status == 'failed' || $backup->status == 'error')
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
            @elseif($backup->status == 'processing')
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 animate-pulse">Proses...</span>
            @elseif($backup->status == 'queued')
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Antrian</span>
            @else
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($backup->status) }}</span>
            @endif
        </td>
        <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-xs" title="{{ $backup->details }}">
            {{ \Illuminate\Support\Str::limit($backup->details, 30) }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-4 text-center text-xs text-gray-500">Belum ada riwayat backup.</td>
    </tr>
@endforelse
