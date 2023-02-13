@extends('layouts.app')

@section('content')

<form method="GET" enctype="multipart/form-data">
    @csrf

    <div class="row container mt-5">
        <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
    </div>

    <div>
    <h3>    Favor preencher Empresa e/ou Estado</h3>
</div>

    <div class="row container mt-5">

        <!-- <div class="col-md-2">
            <label for="dataInicial" class="form-label">Data Inicial</label>
            <input type="date" class="form-control" name="dataInicial" id="dataInicial" required>
        </div>

        <div class="col-md-2">
            <label for="dataFinal" class="form-label">Data Final</label>
            <input type="date" class="form-control" name="dataFinal" id="dataFinal" required>
        </div> -->
        
        <div class="col-md-2" id="periodo">
            <label for="periodo" class="form-label">Escolha o Mês</label>
            <select name="periodo" class="form-select">
            <option selected value="0">Selecione...</option>

            <!-- 2021 -->
            <option value="2021-01">Janeiro/21</option>
            <option value="2021-02">Fevereiro/21</option>
            <option value="2021-03">Março/21</option>
            <option value="2021-04">Abril/21</option>
            <option value="2021-05">Maio/21</option>
            <option value="2021-06">Junho/21</option>
            <option value="2021-07">Julho/21</option>
            <option value="2021-08">Agosto/21</option>
            <option value="2021-09">Setembro/21</option>
            <option value="2021-10">Outubro/21</option>
            <option value="2021-11">Novembro/21</option>
            <option value="2021-12">Dezembro/21</option>
           <!-- </select>
        </div>
        
        <div class="col-md-2" id="periodo">
            <label for="periodo" class="form-label">2022</label>
            <select name="periodo[]" class="form-select">
            <option selected value="0">Selecione...</option>
            -->
            
            <!-- 2022 -->
            <option value="2022-01">Janeiro/22</option>
            <option value="2022-02">Fevereiro/22</option>
            <option value="2022-03">Março/22</option>
            <option value="2022-04">Abril/22</option>
            <option value="2022-05">Maio/22</option>
            <option value="2022-06">Junho/22</option>
            <option value="2022-07">Julho/22</option>
            <option value="2022-08">Agosto/22</option>
            <option value="2022-09">Setembro/22</option>
            <option value="2022-10">Outubro/22</option>
            <option value="2022-11">Novembro/22</option>
            <option value="2022-12">Dezembro/22</option>
            
            <!-- 2023 -->
            <option value="2022-01">Janeiro/23</option>
            <option value="2023-02">Fevereiro/23</option>
            <!-- <option value="2023-03">Março/23</option>
            <option value="2023-04">Abril/23</option>
            <option value="2023-05">Maio/23</option>
            <option value="2023-06">Junho/23</option>
            <option value="2023-07">Julho/23</option>
            <option value="2023-08">Agosto/23</option>
            <option value="2023-09">Setembro/23</option>
            <option value="2023-10">Outubro/23</option>
            <option value="2023-11">Novembro/23</option>
            <option value="2023-12">Dezembro/23</option> -->
        </select>
        </div>
        <!--
        <div class="col-md-2" id="regiao">
            <label for="regiao" class="form-label">Região</label>
            <select name="regiao" class="form-select">
            <option selected value="0">Selecione...</option>
            @foreach($regioes as $r)
                <option value="{{ $r->regiao }}">{{ $r->regiao }}</option>
            @endforeach
            </select>
        </div>
-->

        
        
<!--
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
-->
        
        <div class="col-md-2" id="empresa">
            <label for="empresa" class="form-label">Empresa</label>
            <select name="empresa" class="form-select">
            <option selected value="0">Selecione...</option>
            @foreach($empresas as $e)
                <option value="{{ $e->Empresa }}">{{ $e->Empresa}}</option>
            @endforeach
            </select>
        </div>

        <div class="col-md-2" id="estado">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" class="form-select">
            <option selected value="0">Selecione...</option>
            <option value="AC">Acre</option>
            <option value="AL">Alagoas</option>
            <option value="AP">Amapá</option>
            <option value="AM">Amazonas</option>
            <option value="BA">Bahia</option>
            <option value="CE">Ceará</option>
            <option value="ES">Espírito Santo</option>
            <option value="GO">Goiás</option>
            <option value="MA">Maranhão</option>
            <option value="MT">Mato Grosso</option>
            <option value="MS">Mato Grosso do Sul</option>
            <option value="MG">Minas Gerais</option>
            <option value="PA">Pará</option>
            <option value="PB">Paraíba</option>
            <option value="PR">Paraná</option>
            <option value="PE">Pernambuco</option>
            <option value="PI">Piauí</option>
            <option value="RJ">Rio de Janeiro</option>
            <option value="RN">Rio Grande do Norte</option>
            <option value="RS">Rio Grande do Sul</option>
            <option value="RO">Rondônia</option>
            <option value="RR">Roraima</option>
            <option value="SC">Santa Catarina</option>
            <option value="SP">São Paulo</option>
            <option value="SE">Sergipe</option>
            <option value="TO">Tocantins</option>
            </select>
        </div>


        <div class="col-1 mt-3">
            <!-- <button type="submit" id="buscar" class="btn btn-info text-white" style="margin-right: 250px" formaction="/Filtrar">Filtrar Período</button> -->
            <!-- <button type="submit" id="regiao" class="btn btn-primary mx-3" formaction="/Filtrar-Regiao">Filtrar Região</button> -->
            <!-- <button type="submit" id="estado" class="btn btn-success mx-3" formaction="/Filtrar-Estado">Filtrar Estado</button> -->
            <button type="submit" id="empresa" class="btn btn-danger text-white mx-3" formaction="/Filtrar">Filtrar</button>
        </div>
    </div>
</form>

<!-- <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script>
    var estado = $("[name=estado] option").detach()
    $("[name=regiao]").change(function() {
    var val = $(this).val()
    $("[name=estado] option").detach()
    estado.filter("." + val).clone().appendTo("[name=estado]")
    }).change()

</script> -->

@endsection