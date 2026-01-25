import { getBaseUrl } from './utils.js';

export const RedirectStatus = (() => {
    const initialize = () => {
        const informButtons = document.querySelectorAll('.redirect-btn');

        informButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const clickedBtn = event.currentTarget;

                const pracownikId = clickedBtn.getAttribute('data-pracownik-id');
                const pracownikImie = clickedBtn.getAttribute('data-pracownik-imie');
                const pracownikNazwisko = clickedBtn.getAttribute('data-pracownik-nazwisko');
                const pracownikStanowisko = clickedBtn.getAttribute('data-pracownik-stanowisko');
                const baseUrl = getBaseUrl();

                window.location.href = `${baseUrl}/issue-clothing?pracownikId=${pracownikId}&imie=${pracownikImie}&nazwisko=${pracownikNazwisko}&stanowisko=${pracownikStanowisko}&fromRaport=1`;
            });
        });
    };

    return { initialize };
})();