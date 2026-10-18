@if (session('success'))
    <div role="status" class="mb-6 rounded-lg border-l-4 border-found bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div role="alert" class="mb-6 rounded-lg border-l-4 border-lost bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div role="alert" class="mb-6 rounded-lg border-l-4 border-lost bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
        <p class="font-bold mb-1">Ada isian yang perlu diperbaiki:</p>
        <ul class="list-disc ml-5 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
