@extends('admin.layouts.app')

@section('title', 'Rol ve Yetkiler')
@section('page-title', 'Rol ve Yetkiler')

@section('content')
<div class="card card-custom">
    <div class="card-body text-center py-5">
        <i class="fas fa-tools fa-4x text-muted mb-4"></i>
        <h3>Yakında</h3>
        <p class="text-muted">Rol ve yetki yönetimi özelliği yakında eklenecektir.</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kontrol Paneline Dön
        </a>
    </div>
</div>
@endsection
