@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/Personalizado" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
        @foreach($period as $p)
            @php
                $totais = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalMes')
                                                ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
                                                ->whereRaw('dw_dunax.Situacao <> "Cancelado" 
                                                                    and dw_dunax.Objeto not regexp "Arla" 
                                                                    and dw_dunax.Objeto not regexp "Freio" 
                                                                    and dw_dunax.Objeto not regexp "Aditivo" 
                                                                    and dw_dunax.Data regexp "'. $p->format("Y-m") .'"')
                                                ->get();
            @endphp

            @foreach($totais as $t)
                <h3 class="mt-3 mb-3">Total vendido em {{ $p->format("m/Y") }}: {{ number_format($t->TotalMes, 2, ',', '.') }} Litros</h3>
            @endforeach
        @endforeach
        
        <h5 class="mt-5 mb-3">Quantidade total de cidades atendidas no período {{ $cidades }}</h5>
        <h5 class="mb-3">Quantidade total de clientes atendidos no período {{ $clientes }}</h5>
</div>

<div>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">Regiao</th>
            <th scope="col">Estado</th>
            <th scope="col">Cidade</th>
            <th scope="col">Clientes</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$d->regiao}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection