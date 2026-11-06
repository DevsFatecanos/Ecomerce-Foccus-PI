@extends('layouts.app')

@section('title', 'Nova Pergunta - FAQ')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3"><i class="fas fa-circle-question text-primary"></i> Nova Pergunta</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf

                @include('admin.faqs._form')

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
