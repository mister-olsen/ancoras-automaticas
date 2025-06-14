/**
 * Script para o plugin Âncoras Automáticas
 * Versão 3.1
 */
document.addEventListener('DOMContentLoaded', function () {

    // --- LÓGICA DE CLIQUE PARA COPIAR ---
    const anchorLinks = document.querySelectorAll('.ancora-elemento-link');
    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const clickedLink = event.currentTarget;
            const urlToCopy = clickedLink.getAttribute('href');
            
            const textToCopy = urlToCopy;

            navigator.clipboard.writeText(textToCopy).then(
                function () {
                    const copiedIcon = ancoras_vars.copied_icon;
                    const originalIcon = ancoras_vars.original_icon;
                    
                    const tempOriginalIcon = clickedLink.innerHTML;

                    clickedLink.innerHTML = copiedIcon;

                    setTimeout(function () {
                        clickedLink.innerHTML = tempOriginalIcon;
                    }, 2000);
                },
                function (err) {
                    console.error('Erro ao copiar para a área de transferência: ', err);
                }
            );
        });
    });

    // --- LÓGICA PARA GARANTIR O SCROLL COM MARGEM DINÂMICA ---
    function scrollToAnchor() {
        const hash = window.location.hash;
        
        if (hash) {
            try {
                const targetElement = document.querySelector(hash);
                if (targetElement) {
                    setTimeout(function() {
                        const header = document.querySelector('header, .site-header, #masthead');
                        let headerHeight = 0;

                        if (header) {
                            headerHeight = header.offsetHeight;
                        }
                        
                        // O seu ajuste pessoal de 120px
                        const marginTop = headerHeight + 120;
                        
                        const elementAbsoluteTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                        const targetScrollPosition = elementAbsoluteTop - marginTop;
                      
                        window.scrollTo({
                             top: targetScrollPosition,
                             behavior: "auto"
                        });
                    }, 250); 
                }
            } catch (e) {
                console.warn('Não foi possível fazer scroll para a âncora:', hash);
            }
        }
    }

    scrollToAnchor();
});
