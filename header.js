document.addEventListener('DOMContentLoaded', function() {
    const mobileBreakpoint = 768;

    // NAV MOBILE + DROPDOWN LOGIC
    // Seleziona gli elementi della lista che potrebbero avere sottomenu
    const navLiElements = document.querySelectorAll('.main-nav > ul > li');
    
    navLiElements.forEach(li => {
        const link = li.querySelector('a:first-child');
        const dropdown = li.querySelector('ul.dropdown');
        
        // Se non c'è un link o un dropdown, salta
        if (!link || !dropdown) return;

        link.setAttribute('aria-haspopup', 'true');
        link.setAttribute('aria-expanded', 'false');

        link.addEventListener('click', function(e) {
            if (window.innerWidth <= mobileBreakpoint) {
                e.preventDefault(); // Previene il link se è un trigger di menu su mobile
                const isOpen = dropdown.classList.toggle('show');
                link.setAttribute('aria-expanded', isOpen);

                // Chiude tutti gli altri menu a discesa aperti [cite: 1]
                navLiElements.forEach(otherLi => {
                    const od = otherLi.querySelector('ul.dropdown');
                    if (od && od !== dropdown) {
                        od.classList.remove('show');
                        otherLi.querySelector('a:first-child')
                            .setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    });

    // HAMBURGER MENU LOGIC
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navMenu = document.querySelector('.main-nav ul');

    // Toggle menu al click [cite: 2, 3]
    if (hamburgerBtn && navMenu) {
        hamburgerBtn.addEventListener('click', () => {
            navMenu.classList.toggle('show');
            hamburgerBtn.classList.toggle('active');
        });
    }

    // CLICK FUORI DAL NAV (Chiude il menu se si clicca fuori su mobile)
    document.addEventListener('click', e => {
        if (window.innerWidth <= mobileBreakpoint) {
            const mainNav = document.querySelector('.main-nav');
            // Se il click non è dentro la nav
            if (mainNav && !mainNav.contains(e.target)) {
                // Chiude eventuali dropdown aperti
                navLiElements.forEach(li => {
                    const dd = li.querySelector('ul.dropdown');
                    if (dd) {
                        dd.classList.remove('show');
                        li.querySelector('a:first-child')
                          .setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Opzionale: Chiude anche il menu principale se aperto
                if (navMenu && navMenu.classList.contains('show') && !hamburgerBtn.contains(e.target)) {
                     navMenu.classList.remove('show');
                     hamburgerBtn.classList.remove('active');
                }
            }
        }
    });

    // ADMIN MENU 
    const adminMenuBtn = document.getElementById('adminMenuBtn');
    const adminDropdown = document.getElementById('adminDropdown');
    
    if (adminMenuBtn && adminDropdown) {
        adminMenuBtn.addEventListener('click', e => {
            e.stopPropagation(); // evita il click al documento
            adminDropdown.classList.toggle('show');
        });
        
        window.addEventListener('click', e => {
            if (adminDropdown.classList.contains('show') && !adminMenuBtn.contains(e.target)) {
                adminDropdown.classList.remove('show');
            }
        });
    }
});