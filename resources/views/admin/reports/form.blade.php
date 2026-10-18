@extends('layouts.admin')
@php $editing = $item !== null; @endphp
@section('title', ($editing ? 'Ubah ' : 'Tambah ').$meta['title'])
@section('content')
<div class="max-w-3xl">
    <a href="{{ $editing ? route($meta['route'].'.show', $item->id) : route($meta['route'].'.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Kembali</a>
    <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-ink mt-2 mb-6">{{ $editing ? 'Ubah' : 'Tambah' }} {{ strtolower($meta['title']) }}</h1>
    @include('admin.reports._form')
</div>
@endsection
