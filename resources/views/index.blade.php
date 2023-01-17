@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <button class="btn btn-primary mr-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeral" aria-expanded="false" aria-controls="collapseGeral">
        Relatório Geral
    </button>

    <a href="/Filtro" class="btn btn-primary">Relatório Personalizado</a>

</div>

<div class="container collapse mt-5" id="collapseGeral">
    <h3><strong>Relatório Geral</strong></h3>
    <br>
    <table class="table table-hover">
    <thead>
        <tr>
            <th scope="col">Periodo</th>
            <th scope="col">Cidade</th>
            <th scope="col">ibge_cidade</th>
            <th scope="col">Estado</th>
            <th scope="col">ibge_estado</th>
            <th scope="col">regiao</th>
            <th scope="col">total_venda</th>
            <th scope="col">pop_cidade</th>
            <th scope="col">pop_estado</th>
            <th scope="col">Cpop_regiaoidade</th>
            <th scope="col">tot_venda_lub_cid</th>
            <th scope="col">tot_venda_lub_estado</th>
            <th scope="col">tot_venda_lub_regiao</th>
            <th scope="col">consumo_per_capita</th>
            <th scope="col">venda_per_capita</th>
        </tr>
    </thead>
    <tbody>
        @foreach($estados as $e)
            <tr>
                <th scope="row"></th>
                <td>{{ $e->uf }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>


@endsection