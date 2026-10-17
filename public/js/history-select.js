/**
 * Maintenance History – pilih data (checkbox) untuk ekspor PDF / Excel.
 *
 * Cakupan: hanya baris pada halaman yang sedang dibuka. Pilihan sengaja
 * tidak disimpan lintas halaman/filter; server tetap memvalidasi ulang semuanya.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('[data-history-export-form]');
        var selectAll = document.querySelector('[data-select-all]');
        var rowBoxes = Array.prototype.slice.call(
            document.querySelectorAll('[data-row-checkbox]')
        );

        if (!form) {
            return;
        }

        var countEl = form.querySelector('[data-selected-count]');
        var labelEl = form.querySelector('[data-selected-label]');
        var badgeEl = form.querySelector('[data-selected-badge]');
        var clearBtn = form.querySelector('[data-clear-selection]');
        var exportBtns = Array.prototype.slice.call(
            form.querySelectorAll('[data-export-button]')
        );

        var ACTIVE_BADGE = ['bg-blue-600', 'text-white'];
        var IDLE_BADGE = ['bg-slate-100', 'text-slate-500'];

        function selectedCount() {
            return rowBoxes.filter(function (box) { return box.checked; }).length;
        }

        function refresh() {
            var total = rowBoxes.length;
            var count = selectedCount();

            if (countEl) { countEl.textContent = String(count); }

            if (labelEl) {
                labelEl.textContent = count === 0
                    ? 'Belum ada data dipilih'
                    : count + ' dari ' + total + ' data di halaman ini dipilih';
            }

            if (badgeEl) {
                var add = count > 0 ? ACTIVE_BADGE : IDLE_BADGE;
                var remove = count > 0 ? IDLE_BADGE : ACTIVE_BADGE;
                remove.forEach(function (c) { badgeEl.classList.remove(c); });
                add.forEach(function (c) { badgeEl.classList.add(c); });
            }

            exportBtns.forEach(function (btn) { btn.disabled = count === 0; });
            if (clearBtn) { clearBtn.disabled = count === 0; }

            if (selectAll) {
                selectAll.disabled = total === 0;
                selectAll.checked = total > 0 && count === total;
                selectAll.indeterminate = count > 0 && count < total;
            }

            rowBoxes.forEach(function (box) {
                var row = box.closest('tr');
                if (row) { row.classList.toggle('bg-blue-50', box.checked); }
            });
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowBoxes.forEach(function (box) { box.checked = selectAll.checked; });
                refresh();
            });
        }

        rowBoxes.forEach(function (box) {
            box.addEventListener('change', refresh);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                rowBoxes.forEach(function (box) { box.checked = false; });
                refresh();
            });
        }

        // Cegah submit tanpa pilihan (server tetap menolak jika dilewati).
        form.addEventListener('submit', function (event) {
            if (selectedCount() === 0) {
                event.preventDefault();
                return;
            }

            // Cegah klik ganda saat file sedang dibuat; aktifkan lagi setelah jeda.
            // setTimeout 0: tombol pengirim baru dinonaktifkan setelah submit diproses browser.
            setTimeout(function () {
                exportBtns.forEach(function (btn) { btn.disabled = true; });
            }, 0);
            setTimeout(refresh, 4000);
        });

        // Tombol Back/Forward bisa memulihkan status checkbox dari cache browser.
        window.addEventListener('pageshow', refresh);

        refresh();
    });
})();
