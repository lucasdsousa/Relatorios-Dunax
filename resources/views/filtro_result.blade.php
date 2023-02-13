@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
    <table class="table">
            <tr>
            <th scope="col">Periodo</th>
            <th scope="col">Empresa</th>
            <th scope="col">Estado</th>
            <th scope="col">Cidade</th>
            <th scope="col">Clientes Faturados</th>
            <th scope="col">Clientes Ativos</th>
            <th scope="col">Total Vendido</th>
            <th scope="col">Meta Clientes por Cidade</th><!-- aqui é calculado "Meta Clientes por Cidade" calculando "Meta LT por Cidade" dividido por "Meta Cliente por Estado" -->
            <th scope="col">Meta Clientes por Estado</th>
            <th scope="col">% População Por Estado</th><!-- aqui é calculado "porcentagem de população da cidade por estado" calculando população por cidade dividido por população por estado vezes 100 -->
            <th scope="col">Meta LT por Cidade</th><!-- aqui é calculado  "Meta de Lt por Estado" multiplicado por "% população por estado", isto dividido por 100 (atenção ao arredondamento gerado pelas tabelas) -->
            <th scope="col">Meta LT por Estado</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$mes}}</td>
                    <td>{{$empresa}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{$clientes_ativos}}</td>
                    <td>{{$d->TotalVendido}} Litros</td>
                    <td>280</td>
                    <td>280</td>
                    <td>{{$d->perc_estado_2022}}</td>
                    <td>null</td>
                    @if($mes == "01/2023")
                        <td>{{$jan_2023}} Litros</td>
                    @else
                        <td>0</td>
                    @endif
                @endforeach
        </tbody>
    </table>
</div>

@endsection