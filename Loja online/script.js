function mostrarMensagem(texto) {
    let msg = document.getElementById("msg");

    msg.textContent = texto;
    msg.classList.remove("d-none"); 

    setTimeout(() => {
        msg.classList.add("d-none"); 
    }, 2000);
}
const itensArray = ['item1','item2','item3','item4','item5'];

const container = document.querySelector('.grid-container');