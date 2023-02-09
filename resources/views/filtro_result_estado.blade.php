@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/Personalizado" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>

        <h3 class="mt-5 mb-3">Total vendido em {{ $periodo }}:  {{ $total_vendido }}Litros</h3>

        
        <h5 class="mt-5 mb-3">Porcentagem de cidades atendidas em  {{ $estado }}: {{ $cidades }} / {{ $cidadesEstado }} = <strong>{{ round(($cidades / $cidadesEstado) * 100, 2) }}%</strong></h5>
        <h5 class="mb-3">Quantidade de clientes atendidos em {{ $estado }}: {{ $clientes }}</h5>
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
            <th scope="col">População Estimada</th>
            <th scope="col">Meta Clientes 2023</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$regiao->regiao}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                    <td>{{number_format($d->populacao_estimada, 0, ',', '.')}}</td>
                    <td>280</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

@endsection