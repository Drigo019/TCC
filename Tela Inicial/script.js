function menu()
    {
// Obter referências dos elementos
        var select = document.getElementById("pagamento");
        var tabela = document.getElementById("minhaTabela");

// 4. Lógica JavaScript para mostrar/esconder
        if (select.value === "Dinheiro") 
            {
                tabela.style.display = "table"; // Exibe a tabela
            } 
        else 
            {
                tabela.style.display = "none"; // Oculta a tabela
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
