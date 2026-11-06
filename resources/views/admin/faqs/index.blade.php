@extends('layouts.app')

@section('title', 'Dúvidas Frequentes (FAQ)')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-circle-question text-primary"></i> Dúvidas Frequentes
            </h1>
            <p class="text-muted mb-0">Gerencie as perguntas exibidas na página pública de FAQ</p>
        </div>
        <div>
            <a href="{{ route('faq.index') }}" target="_blank" class="btn btn-outline-secondary">
                <i class="fas fa-external-link-alt"></i> Ver página pública
            </a>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-success ms-2">
                <i class="fas fa-plus"></i> Nova pergunta
            </a>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($faqs->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> Nenhuma pergunta cadastrada ainda.
            <a href="{{ route('admin.faqs.create') }}">Cadastre a primeira</a>.
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Perguntas cadastradas</span>
                <span class="badge bg-primary">{{ $faqs->total() }} pergunta(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ordem</th>
                            <th>Pergunta</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $faq)
                            <tr>
                                <td><strong>{{ $faq->ordem }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $faq->pergunta }}</div>
                                    <small class="text-muted">{{ Str::limit($faq->resposta, 90) }}</small>
                                </td>
                                <td>{{ $faq->categoria ?? 'Geral' }}</td>
                                <td>
                                    <span class="badge bg-{{ $faq->ativo ? 'success' : 'secondary' }}">
                                        {{ $faq->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <form action="{{ route('admin.faqs.toggle', $faq) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $faq->ativo ? 'warning' : 'success' }}"
                                                title="{{ $faq->ativo ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $faq->ativo ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Excluir esta pergunta? Essa ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($faqs->hasPages())
                <div class="card-footer">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
