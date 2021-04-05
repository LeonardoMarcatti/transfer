let w = $(window).width();
$(document).ready(function(){
    $('.global').css('height', $(window).height());
    $('body').css('background-size', $(window).width());
});

function GetEstados(data) {
    let estados;
    $.each(data, (key, value) => {
        estados += '<option value="' + value.sigla + '">' + value.nome + '</option>';
        }
    );
    $('#estados').html($('#estados').html() + estados);
    $('#estados_modal').html($('#estados_modal').html() + estados);
 };

$('#r2').on('click', () =>{
    $('#div_estado_modal').css('display', 'inline');
    $('#label_modal').html('Nome do Município');
    $('#cod_nome_modal').attr('name', 'nome').attr('type', 'text');

});

$('#r1').on('click', () =>{
    $('#div_estado_modal').css('display', 'none');
    $('#label_modal').html('Código do Município');
    $('#cod_nome_modal').attr('name', 'cod').attr('type', 'number')
});

$('#form_modal').on('submit', function(e){
    e.preventDefault();
    getMunicipios($(this))
});

function getMunicipios(dados) {
    $.ajax({
        type: "post",
        url: "getMunicipios.php",
        data: dados.serialize(),
        beforeSend:  () => {
            if ($('tbody tr').length >0) {
                $('tbody tr').remove(); 
            };
            alert('Por favor aguarde');
            console.log(dados.serialize());
        },
        success: function (response) {
            let lista = $.parseJSON(response);
            for (let index = 0; index < lista.length; index++) {
                $('tbody').eq(0).append('<tr>');
                $('tr').eq(index+1).append('<td>' + lista[index].municipio);
                $('tr').eq(index+1).append('<td id="cod">' + lista[index].cod_municipio);
                $('tr').eq(index+1).append('<td>' + lista[index].uf);
                $('tr').eq(index+1).append('<td>' + '<button type="button" class="btn btn-success"><i class="fas fa-plus"></i>');
            };
            getCode();
        },
        error: e => console.log(e)
    });   
};

function getCode() {
    $('table button').on('click', function(){
        if ($('#municipios').val().indexOf($(this).closest('tr').find('#cod')[0].innerHTML) == -1) {
            $('#municipios').val($('#municipios').val().trim());
            if ( $('#municipios').val().substr($('#municipios').val().length - 1) == "," || $('#municipios').val() == "") {
                $('#municipios').val($('#municipios').val() + $(this).closest('tr').find('#cod')[0].innerHTML);
            } else {
                $('#municipios').val($('#municipios').val() + "," + $(this).closest('tr').find('#cod')[0].innerHTML);
            };  
        };
    });
};

$('#fechar').on('click', () => $('tbody tr').remove());


$('#myform').on('submit', function(e){
    e.preventDefault();
    getAgente($(this));
    //getCnaeSecundario($(this));
});

function getAgente(dados) {
    $.ajax({
        type: "post",
        url: "getAgente.php",
        data: dados.serialize(),
        beforeSend:  () => {
            $('#resultado').find('a').remove();
            $('#resultado').find('button').remove();
            alert('Por favor aguarde');
            console.log(dados.serialize());
        },
        success: function (resp) {
            console.log(resp);
            $('#resultado').append('<button type="button" class="btn btn-danger"><a href="agente.zip">Agente');
        }
    });  
};


function getCnaeSecundario(dados) {
    $.ajax({
        type: "post",
        url: "getCnaeSecundario.php",
        data: dados.serialize(),
        success: function (resp) {
            console.log($.parseJSON(resp));
            //$('#resultado').append('<button type="button" class="btn btn-danger"><a href="cnaesecundario.zip">CNAE Secundario');
        }
    });
};