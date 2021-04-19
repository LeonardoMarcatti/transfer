<!DOCTYPE html>
<html lang="pt_BR">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="icon" href="https://pt.seaicons.com/wp-content/uploads/2016/03/Apps-HTML-5-Metro-icon.png" type="image/png" sizes="16x16">
        <link rel="stylesheet" href="style.css">
        <title>PHP - Teste</title>
    </head>
    <body>
        <div class="container-fluid">
            <div id="left" class="col-3 float-left">
                <div class="global"></div>
                    <form action="" method="post" id="myform">
                        <div class="row">
                            <div class=" form-group col-12">
                                <label for="estados">Escolha o Estado</label>
                                <select class="form-control" name="estados" id="estados">
                                    <option value="0" selected>Todos</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-10">
                                <label for="municipio">Lista de Municípios</label>
                                <textarea name="municipios" id="municipios" cols="50" rows="10" class="form-control" value="0"></textarea>
                            </div>
                            <div class="form-group col-2">
                            <button type="button" class="btn btn-warning" id="btnsearch" data-toggle="modal" data-target="#modal"><img src="img/lupa.png" alt="Pesquisar" sizes="" srcset="">Busca</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" form-group col-8">
                                <button type="submit" class="btn btn-success btn-block" id="btn_agente" >Gravar</button>
                            </div>
                        </div>
                    </form>
                <!-- Modal -->
                <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Pesquisa Município</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="radio" id="r1" value="codigo" checked>
                                <label class="form-check-label" for="inlineRadio1">Pesquisa por Código</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="radio" id="r2" value="nome">
                                <label class="form-check-label" for="inlineRadio2">Pesquisa por Nome</label>
                            </div>
                            <form action="" method="post" id="form_modal">
                                <div class="row">
                                    <div class="form-group col-8" id="div_estado_modal">
                                        <label for="estados_modal">Escolha o Estado</label>
                                        <select class="form-control" name="estados_modal" id="estados_modal">
                                            <option value="0" selected>Todos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-7">
                                        <label for="municipio_modal" id="label_modal">Código do Município</label>
                                        <input type="number" name="cod" id="cod_nome_modal" class="form-control">
                                    </div>
                                    <div class="form-group col-1">
                                        <button type="submit" class="btn btn-success" id="pesquisar">Pesquisar</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <table class="table table-sm table-striped text-center">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Municipio</th>
                                                <th>Código</th>
                                                <th>UF</th>
                                                <th>Adicionar</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                        
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="fechar" >Fechar</button>
                        </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div id="resultado">
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/ec29234e56.js" crossorigin="anonymous"></script>
        <script src="script.js" ></script>
        <script src="getEstados.php"></script>
    </body>
</html>