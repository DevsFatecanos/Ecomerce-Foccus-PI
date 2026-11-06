@extends('layouts.app')

@section('title', 'Atendimento #' . $duvida->id)

@section('content')
@php
    $statusClass = [
        'aberta' => 'danger',
        'em_andamento' => 'warning',
        'respondida' => 'success',
        'fechada' => 'secondary',
    ];
    $pedido = $duvida->pedido;
@endphp
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.duvidas.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mb-2">
            <i class="fas fa-headset text-primary"></i> Dúvida #{{ $duvida->id }}
            <span class="badge bg-{{ $statusClass[$duvida->status] ?? 'secondary' }} fs-6 align-middle">{{ $duvida->status_label }}</span>
        </h1>
        <p class="text-muted">Pedido #{{ $pedido->id }} &middot; {{ $pedido->referencia }}</p>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <span><i class="fas fa-comment"></i> Mensagem do cliente</span>
                    <small class="text-muted">{{ $duvida->created_at->format('d/m/Y \à\s H:i') }}</small>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line">{{ $duvida->mensagem }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-reply"></i> Resposta e status
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.duvidas.update', $duvida) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="resposta" class="form-label">Resposta ao cliente</label>
                            <textarea class="form-control @error('resposta') is-invalid @enderror" id="resposta" name="resposta"
                                      rows="5" maxlength="5000"
                                      placeholder="A resposta ficará visível para o cliente no histórico de pedidos.">{{ old('resposta', $duvida->resposta) }}</textarea>
                            @if($duvida->respondida_em)
                                <small class="text-muted">Respondida em {{ $duvida->respondida_em->format('d/m/Y H:i') }}</small>
                            @endif
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    @foreach(\App\Models\PedidoDuvida::STATUS as $chave => $label)
                                        <option value="{{ $chave }}" @selected(old('status', $duvida->status) === $chave)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Ao registrar uma resposta, dúvidas abertas passam a "Respondida".</small>
                            </div>
                            <div class="col-md-6 mb-3 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar atendimento
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($outrasDuvidas->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-history"></i> Outras dúvidas deste pedido
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($outrasDuvidas as $outra)
                            <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <small class="text-muted">{{ $outra->created_at->format('d/m/Y H:i') }}</small>
                                    <div>{{ Str::limit($outra->mensagem, 120) }}</div>
                                </div>
                                <div class="text-nowrap">
                                    <span class="badge bg-{{ $statusClass[$outra->status] ?? 'secondary' }}">{{ $outra->status_label }}</span>
                                    <a href="{{ route('admin.duvidas.show', $outra) }}" class="btn btn-sm btn-outline-primary ms-1"><i class="fas fa-eye"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-user"></i> Cliente</div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $duvida->user?->name ?? $pedido->nome_cliente }}</strong></p>
                    <p class="mb-1"><a href="mailto:{{ $duvida->user?->email ?? $pedido->email_cliente }}">{{ $duvida->user?->email ?? $pedido->email_cliente }}</a></p>
                    @if($pedido->telefone_cliente)
                        <p class="mb-0"><a href="tel:{{ preg_replace('/\D/', '', $pedido->telefone_cliente) }}">{{ $pedido->telefone_cliente }}</a></p>
                    @endif
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-receipt"></i> Pedido #{{ $pedido->id }}</span>
                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">Ver pedido</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Data:</span>
                        <strong>{{ $pedido->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status:</span>
                        <strong>{{ strtoupper($pedido->status) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pagamento:</span>
                        <strong>{{ ucfirst($pedido->metodo_pagamento) }}</strong>
                    </div>
                    <hr>
                    @foreach($pedido->itens as $item)
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $item->quantidade }}x {{ $item->nome_produto }}</span>
                            <span>R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Total:</span>
                        <strong class="text-success">R$ {{ number_format((float) $pedido->total, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
