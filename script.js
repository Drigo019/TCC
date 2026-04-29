function configuracao()
    {
        alert("Configuração")
    }
function novoPedido()
    {
        alert("Novo Pedido");
    }
function estoque()  
    {
        alert("Estoque");
    }   
function sair()
    {
        alert("Sair");
    }
function finalizarPedido()    
    {
        var formadepagamento = document.getElementById("pagamento").value;
        alert("Forma de pagamento selecionada: " + formadepagamento);
    }
function Cadastro()
    {
        let resposta = confirm("Cliente Cadastrado com sucesso!! Deseja fazer login?");

        if (resposta) 
            {
                window.location.href='Tela_Login.html';
            } 
    }
function pesquisa()
    {
        
    }
      