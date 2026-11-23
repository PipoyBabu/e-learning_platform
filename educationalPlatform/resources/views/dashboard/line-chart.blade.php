@php
    $chartWidth = 600;
    $chartHeight = 220;
    $chartLeft = 36;
    $chartRight = 18;
    $chartTop = 20;
    $chartBottom = 34;
    $plotWidth = $chartWidth - $chartLeft - $chartRight;
    $plotHeight = $chartHeight - $chartTop - $chartBottom;
    $maxValue = max(1, max(array_column($data, 'value')));
    $points = collect($data)->values()->map(function (array $point, int $index) use ($plotWidth, $plotHeight, $chartLeft, $chartTop, $maxValue, $data): string {
        $x = $chartLeft + ($index * ($plotWidth / max(1, count($data) - 1)));
        $y = $chartTop + $plotHeight - (($point['value'] / $maxValue) * $plotHeight);
        return round($x, 2).','.round($y, 2);
    })->implode(' ');
    $hasData = collect($data)->sum('value') > 0;
@endphp

<section class="rounded-xl bg-white p-6 shadow-sm" data-animate="chart">
    <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-xl font-semibold">{{ $title }}</h2><p class="mt-1 text-sm text-slate-500">Last six months</p></div>@if (!$hasData)<span class="text-xs font-semibold text-slate-400">No activity yet</span>@endif</div>
    <div class="mt-4 overflow-x-auto">
        <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="min-w-[520px]" role="img" aria-label="{{ $title }} line chart">
            <line x1="{{ $chartLeft }}" y1="{{ $chartTop + $plotHeight }}" x2="{{ $chartWidth - $chartRight }}" y2="{{ $chartTop + $plotHeight }}" stroke="#CBD5E1" />
            <line x1="{{ $chartLeft }}" y1="{{ $chartTop + ($plotHeight / 2) }}" x2="{{ $chartWidth - $chartRight }}" y2="{{ $chartTop + ($plotHeight / 2) }}" stroke="#E2E8F0" stroke-dasharray="4 4" />
            <polyline points="{{ $points }}" fill="none" stroke="#4F46E5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            @foreach ($data as $index => $point)
                @php $x = $chartLeft + ($index * ($plotWidth / max(1, count($data) - 1))); $y = $chartTop + $plotHeight - (($point['value'] / $maxValue) * $plotHeight); @endphp
                <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#FFFFFF" stroke="#0EA5E9" stroke-width="3" />
                <text x="{{ $x }}" y="{{ $chartHeight - 10 }}" text-anchor="middle" font-size="12" fill="#64748B">{{ $point['label'] }}</text>
            @endforeach
        </svg>
    </div>
</section>
