function menu()
    {
        var tabela = document.getElementById("minhaTabela");
        if (tabela.style.display === "none" || tabela.style.display === "") 
            {
                tabela.style.display = "table"; // Mostra
            } 
        else 
            {
                tabela.style.display = "none"; // Esconde
            }
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
function mostrarTabela() {
           
        }
