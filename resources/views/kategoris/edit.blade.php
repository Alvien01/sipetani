@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl mx-auto">
    <form action="{{ route('kategoris.update', $kategori) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $kategori->name) }}" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('kategoris.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-700 hover:bg-green-800 rounded-xl transition-colors shadow-sm">Update</button>
        </div>
    </form>
</div>
@endsection
