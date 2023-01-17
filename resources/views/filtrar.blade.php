@extends('layouts.app')

@section('content')

<form method="GET" enctype="multipart/form-data">
    @csrf

    <div class="row container mt-5">
        <h3 class="mb-5">Litros Totais do Banco de Dados: {{ number_format($total_litros_vendidos->total, 2, ",", ".") }}</h3>

        <div class="col-md-2">
            <label for="dataInicial" class="form-label">Data Inicial</label>
            <input type="date" class="form-control" name="dataInicial" id="dataInicial" required>
        </div>

        <div class="col-md-2">
            <label for="dataFinal" class="form-label">Data Final</label>
            <input type="date" class="form-control" name="dataFinal" id="dataFinal" required>
        </div>
        
        <div class="col-md-2" id="regiao">
            <label for="regiao" class="form-label">Região</label>
            <select name="regiao" class="form-select">
            <option selected value="0">Selecione...</option>
            @foreach($regioes as $r)
                <option value="{{ $r->regiao }}">{{ $r->regiao }}</option>
            @endforeach
            </select>
        </div>

        <div class="col-md-2" id="estado">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" class="form-select">
                @foreach($regN as $n)
                    <option class="N">{{ $n->uf }}</option>
                @endforeach

                @foreach($regNE as $ne)
                    <option class="NE">{{ $ne->uf }}</option>
                @endforeach

                @foreach($regCO as $co)
                    <option class="CO">{{ $co->uf }}</option>
                @endforeach

                @foreach($regSD as $sd)
                    <option class="SD">{{ $sd->uf }}</option>
                @endforeach

                @foreach($regS as $s)
                    <option class="S">{{ $s->uf }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2" id="empresa">
            <label for="empresa" class="form-label">Empresa</label>
            <select name="empresa" class="form-select">
            <option selected value="0">Selecione...</option>
            @foreach($empresas as $e)
                <option value="{{ $e->Empresa }}">{{ $e->Empresa}}</option>
            @endforeach
            </select>
        </div>

        <div class="col-12 mt-3">
            <button type="submit" id="buscar" class="btn btn-info text-white" style="margin-right: 250px" formaction="/Filtrar">Filtrar Período</button>
            <button type="submit" id="regiao" class="btn btn-primary mx-3" formaction="/Filtrar-Regiao">Filtrar Região</button>
            <button type="submit" id="estado" class="btn btn-success mx-3" formaction="/Filtrar-Estado">Filtrar Estado</button>
            <button type="submit" id="empresa" class="btn btn-danger text-white mx-3" formaction="/Filtrar-Empresa">Filtrar Empresa</button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script>
    var estado = $("[name=estado] option").detach()
    $("[name=regiao]").change(function() {
    var val = $(this).val()
    $("[name=estado] option").detach()
    estado.filter("." + val).clone().appendTo("[name=estado]")
    }).change()

</script>

@endsection