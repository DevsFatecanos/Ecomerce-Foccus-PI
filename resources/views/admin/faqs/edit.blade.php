@extends('layouts.app')

@section('title', 'Editar Pergunta - FAQ')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3"><i class="fas fa-circle-question text-primary"></i> Editar Pergunta</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.faqs._form')

                <div class="d-flex justify-content-between">
                    <small class="text-muted align-self-center">
                        Última atualização: {{ $faq->updated_at?->format('d/m/Y H:i') }}
                    </small>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
