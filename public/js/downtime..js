document.addEventListener('DOMContentLoaded', () => {

    const searchInput =
        document.getElementById('downtimeSearch');

    const statusFilter =
        document.getElementById('statusFilter');

    const tableBody =
        document.getElementById('downtimeTableBody');


    /* =====================================================
       FILTER TABLE
       ===================================================== */

    function filterTable() {

        const searchValue =
            searchInput?.value
                .toLowerCase()
                .trim() || '';

        const statusValue =
            statusFilter?.value || '';

        const rows =
            tableBody?.querySelectorAll('tr[data-search]') || [];


        rows.forEach(row => {

            const searchData =
                row.dataset.search || '';

            const status =
                row.dataset.status || '';


            const matchesSearch =
                searchData.includes(searchValue);

            const matchesStatus =
                !statusValue ||
                status === statusValue;


            if (matchesSearch && matchesStatus) {

                row.style.display = '';

            } else {

                row.style.display = 'none';

            }

        });

    }


    /* =====================================================
       SEARCH
       ===================================================== */

    searchInput?.addEventListener(
        'input',
        filterTable
    );


    /* =====================================================
       STATUS FILTER
       ===================================================== */

    statusFilter?.addEventListener(
        'change',
        filterTable
    );


    /* =====================================================
       DELETE CONFIRMATION
       ===================================================== */

    const deleteForms =
        document.querySelectorAll('.delete-form');


    deleteForms.forEach(form => {

        form.addEventListener('submit', event => {

            const confirmed =
                confirm(
                    'Are you sure you want to delete this downtime record?'
                );


            if (!confirmed) {

                event.preventDefault();

            }

        });

    });


    /* =====================================================
       SORTING
       ===================================================== */

    const sortButtons =
        document.querySelectorAll('.sort-btn');

    let sortAscending = true;


    sortButtons.forEach(button => {

        button.addEventListener('click', () => {

            const sortType =
                button.dataset.sort;

            if (sortType !== 'equipment') {
                return;
            }


            const rows =
                Array.from(
                    tableBody.querySelectorAll('tr[data-search]')
                );


            rows.sort((a, b) => {

                const equipmentA =
                    a
                        .querySelector('.equipment-info strong')
                        ?.textContent
                        .trim()
                        .toLowerCase() || '';

                const equipmentB =
                    b
                        .querySelector('.equipment-info strong')
                        ?.textContent
                        .trim()
                        .toLowerCase() || '';


                return sortAscending
                    ? equipmentA.localeCompare(equipmentB)
                    : equipmentB.localeCompare(equipmentA);

            });


            rows.forEach(row => {
                tableBody.appendChild(row);
            });


            sortAscending = !sortAscending;

        });

    }
    )
});

/* =========================================================
CREATE DOWNTIME FORM
========================================================= */

const downtimeForm =
document.getElementById('downtimeForm');

const startTimeInput =
document.getElementById('start_time');

const endTimeInput =
document.getElementById('end_time');

const submitButton =
document.getElementById('submitDowntime');

/* =========================================================
VALIDATE DATE
========================================================= */

if (downtimeForm) {


downtimeForm.addEventListener('submit', event => {

    const startTime =
        startTimeInput?.value;

    const endTime =
        endTimeInput?.value;


    /*
     * End time tidak boleh lebih awal
     * dari start time.
     */

    if (
        startTime &&
        endTime &&
        new Date(endTime) < new Date(startTime)
    ) {

        event.preventDefault();

        alert(
            'End Downtime cannot be earlier than Start Downtime.'
        );

        endTimeInput.focus();

        return;
    }


    /*
     * Disable button setelah submit
     * supaya tidak terjadi double submit.
     */

    if (submitButton) {

        submitButton.disabled = true;

        submitButton.textContent =
            'Creating...';

    }

});

}


