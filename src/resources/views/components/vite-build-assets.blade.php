@props(['entries' => []])

@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath)
        ? json_decode(file_get_contents($manifestPath), true)
        : [];

    $printedCss = [];
    $printedJs = [];
@endphp

@foreach ((array) $entries as $entry)
    @php($asset = $manifest[$entry] ?? null)

    @if ($asset)
        @if (isset($asset['css']))
            @foreach ($asset['css'] as $cssFile)
                @continue(isset($printedCss[$cssFile]))
                @php($printedCss[$cssFile] = true)
                <link rel="stylesheet" href="/build/{{ $cssFile }}">
            @endforeach
        @endif

        @if (isset($asset['file']) && str_ends_with($asset['file'], '.css'))
            @continue(isset($printedCss[$asset['file']]))
            @php($printedCss[$asset['file']] = true)
            <link rel="stylesheet" href="/build/{{ $asset['file'] }}">
        @endif

        @if (isset($asset['file']) && str_ends_with($asset['file'], '.js'))
            @continue(isset($printedJs[$asset['file']]))
            @php($printedJs[$asset['file']] = true)
            <script type="module" src="/build/{{ $asset['file'] }}"></script>
        @endif
    @endif
@endforeach
