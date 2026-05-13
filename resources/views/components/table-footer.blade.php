@props(['paginator' => null])

@php
    $request = request();
    $perPage = (int) $request->query('per_page', 10);
    $options = [10, 25, 50, 100];

    $isPaginator = $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator || $paginator instanceof \Illuminate\Pagination\Paginator;

    if ($isPaginator) {
        $from = $paginator->firstItem() ?? 0;
        $to = $paginator->lastItem() ?? 0;
        $total = $paginator->total();
    } elseif (is_countable($paginator)) {
        $count = count($paginator);
        $from = $count ? 1 : 0;
        $to = $count;
        $total = $count;
    } else {
        $from = $to = $total = 0;
    }
@endphp

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-4">
    <div class="text-sm text-slate-600 dark:text-slate-400">Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} hasil</div>

    <div class="flex items-center gap-3">
        <form method="get" action="{{ url()->current() }}" class="flex items-center gap-2">
            @foreach(request()->except(['per_page','page']) as $name => $value)
                @if(is_array($value))
                    @foreach($value as $v)
                        <input type="hidden" name="{{ $name }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endif
            @endforeach

            <label for="per_page" class="text-sm text-slate-500 dark:text-slate-400 mr-2">per halaman</label>
            <select id="per_page" name="per_page" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 border-slate-300">
                @foreach($options as $opt)
                    <option value="{{ $opt }}" @if((int)request('per_page', 10) === $opt) selected @endif>{{ $opt }}</option>
                @endforeach
            </select>
        </form>

        @if($isPaginator)
            <div>
                {!! $paginator->appends(request()->except('page'))->links() !!}
            </div>
        @endif
    </div>
</div>
