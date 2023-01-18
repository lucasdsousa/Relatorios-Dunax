@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/Filtrar" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
        @foreach($period as $p)
            @php
                $totais = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalMes')->whereRaw('Data regexp "'. $p->format("Y-m") .'"')->get();
            @endphp

            @foreach($totais as $t)
            <h3 class="mt-5 mb-3">Total vendido em {{ $p->format("m/Y") }}: {{ number_format($t->TotalMes, 2, ',', '.') }} Litros</h3>
            @endforeach
        @endforeach
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
                    <td>{{$regiao->regiao}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection