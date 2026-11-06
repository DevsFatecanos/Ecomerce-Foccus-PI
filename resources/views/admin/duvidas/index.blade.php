@extends('layouts.app')

@section('title', 'Atendimento - Dúvidas sobre Pedidos')

@section('content')
@php
    $statusClass = [
        'aberta' => 'danger',
        'em_andamento' => 'warning',
        'respondida' => 'success',
        'fechada' => 'secondary',
    ];
@endphp
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="mb-2">
            <i class="fas fa-headset text-primary"></i> Atendimento
        </h1>
        <p class="text-muted">Dúvidas enviadas pelos clientes sobre seus pedidos</p>
    </div>

    <ul class="nav nav-pills mb-3 gap-2">
        <li class="nav-item">
            <a class="nav-link {{ ! $status ? 'active' : '' }}" href="{{ route('admin.duvidas.index') }}">
                Todas <span class="badge bg-light text-dark ms-1">{{ $contagens->sum() }}</span>
            </a>
        </li>
        @foreach(\App\Models\PedidoDuvida::STATUS as $chave => $label)
            <li class="nav-item">
                <a class="nav-link {{ $status === $chave ? 'active' : '' }}" href="{{ route('admin.duvidas.index', ['status' => $chave]) }}">
                    {{ $label }} <span class="badge bg-{{ $statusClass[$chave] }} ms-1">{{ $contagens[$chave] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    @if($duvidas->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> Nenhuma dúvida encontrada.
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Mensagem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($duvidas as $duvida)
                            <tr class="{{ ! $duvida->lida_em ? 'fw-semibold' : '' }}">
                                <td class="text-nowrap">
                                    @if(! $duvida->lida_em)
                                        <i class="fas fa-circle text-primary" style="font-size: .5rem" title="Não lida"></i>
                                    @endif
                                    <small>{{ $duvida->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pedidos.show', $duvida->pedido_id) }}">#{{ $duvida->pedido_id }}</a>
                                    <div><small class="text-muted">{{ $duvida->pedido?->referencia }}</small></div>
                                </td>
                                <td>
                                    <div>{{ $duvida->user?->name ?? '—' }}</div>
                                    <small class="text-muted">{{ $duvida->user?->email }}</small>
                                </td>
                                <td style="max-width: 380px">{{ Str::limit($duvida->mensagem, 110) }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusClass[$duvida->status] ?? 'secondary' }}">{{ $duvida->status_label }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.duvidas.show', $duvida) }}" class="btn btn-sm btn-primary" title="Abrir atendimento">
                                        <i class="fas fa-reply"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($duvidas->hasPages())
                <div class="card-footer">
                    {{ $duvidas->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
