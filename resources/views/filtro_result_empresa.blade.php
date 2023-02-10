@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>



<div>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">Periodo</th>
            <th scope="col">Estado</th>
            <th scope="col">Cidade</th>
            <th scope="col">Clientes no Mes</th>
            <th scope="col">Clientes Ativos</th>
            <th scope="col">Total Vendido</th>
            <th scope="col">Meta Clientes 2023</th>
            <th scope="col">Empresa</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$mes}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{$clientes_ativos}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                    <td>280</td>
                    <td>{{$d->Empresa}}</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection