export const ModalEditEmployee = (() => {
    let pracownikId = null;

    const initialize = () => {
        $('#example').on('click', '.open-modal-btn', (event) => {
            const clickedBtn = event.currentTarget;
            pracownikId = $(clickedBtn).data('id');
            const index = $(clickedBtn).data('index');

            const pracownicyData = document.getElementById("pracownicy-data");
            if (pracownicyData) {
                const pracownicy = JSON.parse(pracownicyData.textContent);
                const pracownik = pracownicy[index];

                document.getElementById("pracownik_id").value = pracownik.id_pracownik;
                document.getElementById("imie").value = pracownik.imie;
                document.getElementById("nazwisko").value = pracownik.nazwisko;
                document.getElementById("stanowisko").value = pracownik.stanowisko;
                document.getElementById("status").value = pracownik.status;

                $('#editModal').modal('show');
            }
        });
    };

    return { initialize };
})();
