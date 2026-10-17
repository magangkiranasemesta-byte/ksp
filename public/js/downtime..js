
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('downtimeForm');

    if (!form) return;

    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const statusSelect = document.getElementById('status');

    const statusInfo = document.getElementById('statusInfo');
    const statusDescription = document.getElementById('statusDescription');

    const durationPreview = document.getElementById('durationPreview');
    const durationValue = document.getElementById('durationValue');

    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');

    const submitButton = document.getElementById('submitDowntime');
    const submitButtonText = document.getElementById('submitDowntimeText');

    // Format durasi menjadi hari, jam, dan menit.
    function formatDuration(milliseconds) {
        const totalMinutes = Math.floor(milliseconds / 60000);

        const days = Math.floor(totalMinutes / 1440);
        const hours = Math.floor((totalMinutes % 1440) / 60);
        const minutes = totalMinutes % 60;

        const parts = [];

        if (days > 0) parts.push(`${days} hari`);
        if (hours > 0) parts.push(`${hours} jam`);
        if (minutes > 0 || parts.length === 0) {
            parts.push(`${minutes} menit`);
        }

        return parts.join(' ');
    }

    // Atur batas waktu selesai berdasarkan waktu mulai.
    function validateTimes() {
        const start = startInput.value;
        const end = endInput.value;

        startInput.setCustomValidity('');
        endInput.setCustomValidity('');

        if (start) {
            endInput.min = start;
        } else {
            endInput.removeAttribute('min');
        }

        if (start && end && new Date(end) < new Date(start)) {
            endInput.setCustomValidity(
                'Waktu selesai tidak boleh lebih awal daripada waktu mulai.'
            );
        }

        updateDuration();
    }

    // Tampilkan perkiraan durasi jika kedua waktu sudah diisi.
    function updateDuration() {
        if (!startInput.value || !endInput.value) {
            durationPreview.hidden = true;
            durationValue.textContent = '—';
            return;
        }

        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const difference = end.getTime() - start.getTime();

        if (
            Number.isNaN(start.getTime()) ||
            Number.isNaN(end.getTime()) ||
            difference < 0
        ) {
            durationPreview.hidden = true;
            return;
        }

        durationValue.textContent = formatDuration(difference);
        durationPreview.hidden = false;
    }

    // Perbarui informasi berdasarkan status.
    function updateStatusInfo() {
        const isCompleted = statusSelect.value === 'COMPLETED';

        statusInfo.classList.toggle('is-completed', isCompleted);

        if (isCompleted) {
            statusDescription.textContent =
                'Downtime ditandai selesai. Pastikan waktu selesai telah diisi sesuai kondisi aktual.';
        } else {
            statusDescription.textContent =
                'Downtime aktif akan tercatat sebagai gangguan yang masih berlangsung.';
        }
    }

    // Hitung jumlah karakter deskripsi.
    function updateDescriptionCount() {
        descriptionCount.textContent = description.value.length;
    }

    startInput.addEventListener('change', validateTimes);
    endInput.addEventListener('change', validateTimes);
    startInput.addEventListener('input', validateTimes);
    endInput.addEventListener('input', validateTimes);

    statusSelect.addEventListener('change', updateStatusInfo);
    description.addEventListener('input', updateDescriptionCount);

    // Inisialisasi sesuai nilai awal form.
    validateTimes();
    updateStatusInfo();
    updateDescriptionCount();

    // Cegah submit berulang dan validasi sebelum pengiriman.
    form.addEventListener('submit', function (event) {
        validateTimes();

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        if (submitButton.disabled) {
            event.preventDefault();
            return;
        }

        submitButton.disabled = true;
        submitButtonText.textContent = 'Saving...';
    });
});


/* ==========================================
   DOWNTIME INDEX — SEARCH, FILTER & SORT
   ========================================== */

document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.downtime-list-page');

    if (!page) return;

    const searchInput = page.querySelector('#downtimeSearch');
    const statusFilter = page.querySelector('#statusFilter');
    const tableBody = page.querySelector('#downtimeTableBody');
    const noResults = page.querySelector('#downtimeNoResults');
    const resultsInfo = page.querySelector('#downtimeResultsInfo');
    const sortButton = page.querySelector('[data-sort="equipment"]');

    if (!tableBody) return;

    const rows = Array.from(tableBody.querySelectorAll('tr[data-status]'));

    let sortAscending = true;

    function filterRows() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const selectedStatus = (statusFilter?.value || '').toUpperCase();

        let visibleCount = 0;

        rows.forEach(function (row) {
            const searchableText = row.dataset.search || '';
            const status = (row.dataset.status || '').toUpperCase();

            const matchesSearch = searchableText.includes(query);

            const matchesStatus =
                !selectedStatus ||
                status === selectedStatus ||
                (
                    selectedStatus === 'ONGOING' &&
                    status === 'ACTIVE'
                );

            const visible = matchesSearch && matchesStatus;

            row.hidden = !visible;

            if (visible) visibleCount++;
        });

        if (noResults) {
            noResults.hidden = visibleCount !== 0;
        }

        if (resultsInfo) {
            if (query || selectedStatus) {
                resultsInfo.textContent =
                    `Showing ${visibleCount} matching record${visibleCount === 1 ? '' : 's'} on this page`;
            } else {
                resultsInfo.textContent =
                    `Showing ${rows.length} record${rows.length === 1 ? '' : 's'} on this page`;
            }
        }
    }

    searchInput?.addEventListener('input', filterRows);
    statusFilter?.addEventListener('change', filterRows);

    sortButton?.addEventListener('click', function () {
        sortAscending = !sortAscending;

        const sortedRows = [...rows].sort(function (a, b) {
            const first = a.dataset.equipment || '';
            const second = b.dataset.equipment || '';

            return sortAscending
                ? first.localeCompare(second)
                : second.localeCompare(first);
        });

        sortedRows.forEach(function (row) {
            tableBody.insertBefore(row, noResults);
        });

        const directionIcon = sortButton.querySelector('span');

        if (directionIcon) {
            directionIcon.textContent = sortAscending ? '↑' : '↓';
        }

        filterRows();
    });

    filterRows();
});

