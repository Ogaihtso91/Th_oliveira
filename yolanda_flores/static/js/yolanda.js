/**
 * Created by thiagoliveira on 01/12/2016.
 */
(function() {

    var produto_length = 0;

    function calcular() {

        var valor_final = 0;
        try {
            var produtos = $(".produto-item");
            var valor_parcial = 0;
            produto_length = produtos.length;

            for (i = 0; i <= produto_length; i++) {
                if($(produtos[i]).is(':visible')) {
                    var precoaux = $("[name='produto[" + i + "][valor]']").val();
                    if (precoaux != undefined) {
                        var preco = parseFloat(precoaux.replace('.','').replace(',', '.'));
                        var qtd = parseInt($("[name='produto[" + i + "][quantidade]']").val());
                        valor_parcial = preco * qtd;
                        valor_final += parseFloat(valor_parcial);
                        $(produtos[i]).find('.valorParcial').html(valor_parcial.toFixed(2));
                    }
                }
            }

            $(".valorFinal").html(valor_final.toFixed(2));
        } catch(err) { console.log(err); }
    }

    $(".produto-estoque, .quantidade, .valor").on('change keypress', function() {
        calcular();
    });

    var ModelProduto = $("#ProdutoModel").clone();
    $("#ProdutoModel").remove();
    $("#btn-add").on('click', function(e) {
        e.preventDefault();
        var NewProduto = ModelProduto.clone();
        var length = $(".produto-item").length;
        $(NewProduto).find(':input').each(function(k, v){
            $(v).attr('name', $(v).attr('name').replace(':index:', length));
            if($(v).attr('data-produto-index') != undefined) {
                $(v).attr('data-produto-index', length);
            }
        });
        $("#produtos").append(NewProduto);
        $(".btn-remove").on('click', function() {
            $(this).parents('.produto-item').remove();
            calcular();
        });

        $(".produto-estoque").on('change', function(v) {
            var obj = $(this);
            var id = $(obj).val();
            var qtd = $(obj).find(':checked').data('qtd');
            var valor = $(obj).find(':checked').data('valor');
            var index = $(obj).data('produto-index');
            $("[name='produto[" + index + "][valor]").val(valor);
            var options = [];
            for(i = 1; i <= qtd; i++){
                var aux = document.createElement('option'); $(aux).attr('value', i).text(i);
                options.push(aux);
            }
            $("[name='produto[" + index + "][quantidade]").find('option').remove();
            $("[name='produto[" + index + "][quantidade]").append(options);

            $(".produto-estoque, .quantidade, .valor").on('change keypress', function() {
                calcular();
            });

        }).change();

        calcular();

    });

    $("#data").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
    });

    $("#dataa").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
    });






})();

function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e){
    var sep = 0;
    var key = "";
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '0123456789';
    var aux = aux2 = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
    if (whichCode == 13) return true;
    key = String.fromCharCode(whichCode); // Valor para o código da Chave
    if (strCheck.indexOf(key) == -1) return false; // Chave inválida
    len = objTextBox.value.length;
    for(i = 0; i < len; i++)
        if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
    aux = '';
    for(; i < len; i++)
        if (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) aux += objTextBox.value.charAt(i);
    aux += key;
    len = aux.length;
    if (len == 0) objTextBox.value = "";
    if (len == 1) objTextBox.value = "0"+ SeparadorDecimal + "0" + aux;
    if (len == 2) objTextBox.value = "0"+ SeparadorDecimal + aux;
    if (len > 2) {
        aux2 = "";
        for (j = 0, i = len - 3; i >= 0; i--) {
            if (j == 3) {
                aux2 += SeparadorMilesimo;
                j = 0;
            }
            aux2 += aux.charAt(i);
            j++;
        }
        objTextBox.value = "";
        len2 = aux2.length;
        for (i = len2 - 1; i >= 0; i--)
            objTextBox.value += aux2.charAt(i);
        objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
    }
    return false;
}
