document.addEventListener('DOMContentLoaded', function () {

    /* ---- Mobile sidebar toggle ---- */
    var burger = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('dashSidebar');
    var overlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        sidebar && sidebar.classList.remove('is-open');
        overlay && overlay.classList.remove('show');
    }
    function openSidebar() {
        sidebar && sidebar.classList.add('is-open');
        overlay && overlay.classList.add('show');
    }

    if (burger) {
        burger.addEventListener('click', function () {
            if (sidebar.classList.contains('is-open')) closeSidebar();
            else openSidebar();
        });
    }
    if (overlay) overlay.addEventListener('click', closeSidebar);

    /* ---- Upload dropzones: show selected filename, allow removal ---- */
    document.querySelectorAll('[data-dropzone]').forEach(function (zone) {
        var input = zone.querySelector('input[type=file]');
        var chip = zone.parentElement.querySelector('[data-file-chip]');
        var chipName = chip ? chip.querySelector('[data-file-name]') : null;
        var removeBtn = chip ? chip.querySelector('[data-file-remove]') : null;

        if (!input) return;

        function showFile(file) {
            zone.classList.add('has-file');
            if (chip && chipName) {
                chipName.textContent = file.name;
                chip.classList.add('show');
            }
            checkFormReady();
        }
        function clearFile() {
            input.value = '';
            zone.classList.remove('has-file');
            if (chip) chip.classList.remove('show');
            checkFormReady();
        }

        input.addEventListener('change', function () {
            if (input.files && input.files[0]) showFile(input.files[0]);
        });

        ['dragover', 'dragenter'].forEach(function (evt) {
            zone.addEventListener(evt, function (e) {
                e.preventDefault();
                zone.classList.add('is-dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (evt) {
            zone.addEventListener(evt, function (e) {
                e.preventDefault();
                zone.classList.remove('is-dragover');
            });
        });
        zone.addEventListener('drop', function (e) {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                showFile(e.dataTransfer.files[0]);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                clearFile();
            });
        }
    });

    /* ---- Enable submit only when all required uploads are present ---- */
    function checkFormReady() {
        var form = document.querySelector('[data-upload-form]');
        if (!form) return;
        var submitBtn = form.querySelector('[data-submit-btn]');
        if (!submitBtn) return;
        var requiredInputs = form.querySelectorAll('input[type=file][required]');
        var allFilled = true;
        requiredInputs.forEach(function (inp) {
            if (!inp.files || !inp.files[0]) allFilled = false;
        });
        submitBtn.disabled = !allFilled;
    }
    checkFormReady();

});
