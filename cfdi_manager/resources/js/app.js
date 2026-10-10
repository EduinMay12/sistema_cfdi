import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import DataTable from 'datatables.net-bs5';
import Chart from 'chart.js/auto';

// ============================================
// CFDI MANAGER — FINANCIAL SUITE
// ============================================

window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
window.Chart = Chart;

// Registrar DataTables con jQuery
//DataTable(window, $);

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // MODO OSCURO
    // ========================================

    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    function applyTheme(theme) {

        const normalizedTheme = theme === 'dark'
            ? 'dark'
            : 'light';

        document.documentElement.setAttribute(
            'data-bs-theme',
            normalizedTheme
        );

        localStorage.setItem('cfdi-theme', normalizedTheme);

        if (themeIcon) {
            themeIcon.className = normalizedTheme === 'dark'
                ? 'bi bi-sun-fill'
                : 'bi bi-moon-stars-fill';
        }

        if (themeToggle) {
            themeToggle.setAttribute(
                'aria-label',
                normalizedTheme === 'dark'
                    ? 'Activar modo claro'
                    : 'Activar modo oscuro'
            );
        }
    }

    const savedTheme = localStorage.getItem('cfdi-theme');

    applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

    themeToggle?.addEventListener('click', function () {

        const currentTheme =
            document.documentElement.getAttribute('data-bs-theme');

        applyTheme(currentTheme === 'dark' ? 'light' : 'dark');

    });

    // ========================================
    // SIDEBAR RESPONSIVE
    // ========================================

    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');

    function setSidebarOpen(open) {

        if (!sidebar) return;

        sidebar.classList.toggle('show', open);
        overlay?.classList.toggle('show', open);

        sidebarToggle?.setAttribute(
            'aria-expanded',
            String(open)
        );

        sidebarToggle?.setAttribute(
            'aria-label',
            open ? 'Cerrar menú' : 'Abrir menú'
        );

        document.body.classList.toggle(
            'sidebar-mobile-open',
            open
        );
    }

    function closeSidebar() {
        setSidebarOpen(false);
    }

    sidebarToggle?.addEventListener('click', function () {

        const isOpen = sidebar?.classList.contains('show');

        setSidebarOpen(!isOpen);

    });

    sidebarClose?.addEventListener('click', closeSidebar);

    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('.sidebar-link')
        .forEach(function (link) {

            link.addEventListener('click', closeSidebar);

        });

    document.addEventListener('keydown', function (event) {

        if (
            event.key === 'Escape' &&
            sidebar?.classList.contains('show')
        ) {
            closeSidebar();
            sidebarToggle?.focus();
        }

    });

    // Cerrar menú al regresar a escritorio.
    const desktopMedia = window.matchMedia('(min-width: 992px)');

    function handleDesktopChange(event) {

        if (event.matches) {
            closeSidebar();
        }

    }

    desktopMedia.addEventListener(
        'change',
        handleDesktopChange
    );

    // ========================================
    // DATATABLES
    // ========================================

    document.querySelectorAll('.datatable')
        .forEach(function (table) {

            if ($.fn.DataTable.isDataTable(table)) {
                return;
            }

            $(table).DataTable({

                pageLength: 10,

                autoWidth: false,

                language: {
                    emptyTable: 'No hay registros disponibles',
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'Sin registros',
                    zeroRecords: 'No se encontraron resultados',

                    paginate: {
                        previous: 'Anterior',
                        next: 'Siguiente'
                    }
                },

                initComplete: function () {

                    const wrapper = table.closest('.dt-container');

                    if (wrapper) {
                        wrapper.classList.add('executive-datatable');
                    }

                }

            });

        });

    // ========================================
    // LOADER EJECUTIVO
    // ========================================

    // Se mantiene una única implementación aquí.
    // El layout solamente debe ocultarlo al cargar.

});

// ============================================
// API GLOBAL PARA LOADER SAT
// ============================================

window.mostrarLoaderSat = function (
    mensaje = 'Procesando operación SAT...'
) {

    const loader = document.getElementById('executiveLoader');

    if (!loader) return;

    const texto = loader.querySelector(
        '.executive-loader-message'
    );

    if (texto) {
        texto.textContent = mensaje;
    }

    loader.setAttribute('aria-hidden', 'false');
    loader.classList.remove('loader-hidden');

};

window.ocultarLoaderSat = function () {

    const loader = document.getElementById('executiveLoader');

    if (!loader) return;

    loader.classList.add('loader-hidden');
    loader.setAttribute('aria-hidden', 'true');

};