document.querySelectorAll('.slides').forEach(slider => {
    let currentSlide = 0
    const slides = slider.querySelectorAll('.slide'); /*"slider." seleciona todos os elementos dentro do slider, e não do documento inteiro*/
    const dots = slider.parentElement.querySelectorAll('.dot');
    let slideInterval = setInterval(nextSlide,3000); /*Define de quanto em quanto tempo os slides serão mudados*/

    function showSlide(index){
        const slideWidth = 33.33333 //porcentagem
        slider.style.transform = `translateX(-${index * slideWidth}%)`;
        dots.forEach(dot => dot.classList.remove("active"));
        dots[index].classList.add("active")
        currentSlide = index
    }

    //faz com que os slides passem sozinhos
    function nextSlide(){
        let nextIndex = (currentSlide + 1) % slides.length; //Retorna o resto da divisão do slide atual pela quantidade de slides totais, para que quando os
        // slides cheguem ao final, o próximo slide que será retornado será "0", e não um valor que quebraria a função */
        showSlide(nextIndex);
    }


    //Mudar slide manualmente
    dots.forEach(dot => { 
        dot.addEventListener('click', () =>{
            let index = parseInt(dot.getAttribute('data-index')) 
            showSlide(index);
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 3000);
        });
        dot.addEventListener('mouseover', () => {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 3000);
        }); 
    })

    //fazer slides pararem de passar quando colocar o mouse em cima
    slides.forEach(slide => { 
        slide.addEventListener('mouseover', () =>{
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 3000);
        }); 
    })
});

