@if (session('success'))
    <div role="status" class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div role="alert" class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div role="alert" class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
