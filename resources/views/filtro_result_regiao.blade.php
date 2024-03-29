@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
        @foreach($period as $p)
            @php
                $totais = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalMes')
                                                ->join('populacao', 'IBGECidade', '=', 'populacao.cod_municipio')
                                                                    and Objeto not regexp "Arla" 
                                                                    and Objeto not regexp "Freio" 
                                                                    and Objeto not regexp "Aditivo"
                                                                    and Cliente not regexp "DULUB"
                                                                    and Cliente not regexp "DUNAX"
                                                                    and TipoDeOperacao not regexp "Devol"
                                                                    and dw_dunax.Data regexp "'. $p->format("Y-m") . '"')                                             
                                                ->where('populacao.regiao', '=', $regiao)
                                                ->get();
            @endphp

            @foreach($totais as $t)
            <h3 class="mt-5 mb-3">Total vendido em {{ $p->format("m/Y") }}: {{ number_format($t->TotalMes, 2, ',', '.') }} Litros</h3>
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
            <th scope="col">Total Vendido</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$d->regiao}}</td>
                    <td>{{$d->uf}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection