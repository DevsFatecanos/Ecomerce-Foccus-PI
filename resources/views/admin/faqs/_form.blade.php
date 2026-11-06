@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5>Erros encontrados:</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="mb-3">
    <label for="pergunta" class="form-label">Pergunta <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('pergunta') is-invalid @enderror"
           id="pergunta" name="pergunta" maxlength="255" required
           value="{{ old('pergunta', $faq->pergunta) }}">
    @error('pergunta')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="mb-3">
    <label for="resposta" class="form-label">Resposta <span class="text-danger">*</span></label>
    <textarea class="form-control @error('resposta') is-invalid @enderror"
              id="resposta" name="resposta" rows="6" maxlength="5000" required>{{ old('resposta', $faq->resposta) }}</textarea>
    @error('resposta')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="categoria" class="form-label">Categoria</label>
        <input type="text" class="form-control @error('categoria') is-invalid @enderror"
               id="categoria" name="categoria" maxlength="80" list="faq-categorias"
               placeholder="Ex.: Pagamentos, Entregas..."
               value="{{ old('categoria', $faq->categoria) }}">
        <datalist id="faq-categorias">
            @foreach($categorias as $cat)
                <option value="{{ $cat }}">
            @endforeach
        </datalist>
        <small class="text-muted">Sem categoria, a pergunta aparece em "Geral".</small>
        @error('categoria')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-3">
        <label for="ordem" class="form-label">Ordem de exibição</label>
        <input type="number" class="form-control @error('ordem') is-invalid @enderror"
               id="ordem" name="ordem" min="0" max="9999"
               value="{{ old('ordem', $faq->ordem) }}">
        <small class="text-muted">Menor aparece primeiro.</small>
        @error('ordem')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check form-switch mt-3">
            <input type="hidden" name="ativo" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo" value="1"
                   @checked(old('ativo', $faq->ativo))>
            <label class="form-check-label" for="ativo">Ativa (visível no site)</label>
        </div>
    </div>
</div>
