<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sortable-ghost { opacity: 0.4; background: #dbeafe; border: 2px dashed #3b82f6; }
        .sortable-drag  { opacity: 0.9; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .canvas-dragover { border-color: #3b82f6 !important; background: #eff6ff !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.18),_transparent_30%),linear-gradient(135deg,_#f8fbff_0%,_#eef4ff_45%,_#fdf2f8_100%)] text-gray-800 text-sm">
    @yield('content')
    @stack('scripts')
</body>
</html>
