function mostrarMensagem(texto) {
    let msg = document.getElementById("msg");

    msg.textContent = texto;
    msg.classList.remove("d-none"); 

    setTimeout(() => {
        msg.classList.add("d-none"); 
    }, 2000);
}