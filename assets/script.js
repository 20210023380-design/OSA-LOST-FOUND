/* c:/xampp/htdocs/osa_lost_found/assets/script.js */
(function ($) {
    'use strict';

    function clearFieldErrors(prefix) {
        $('.field-error').text('');
    }

    /* ——— Public registry live search ——— */
    function initRegistrySearch() {
        var $input = $('#registry-search');
        var $rows = $('#registry-table tbody tr');
        if (!$input.length || !$rows.length) {
            return;
        }
        $input.on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            $rows.each(function () {
                var $tr = $(this);
                if ($tr.hasClass('no-results-row')) {
                    return;
                }
                var hay = ($tr.attr('data-search') || '').toLowerCase();
                if (q === '' || hay.indexOf(q) !== -1) {
                    $tr.show();
                } else {
                    $tr.hide();
                }
            });
        });
    }

    /* ——— Report form: validation + image preview ——— */
    function initReportForm() {
        var $form = $('#report-form');
        if (!$form.length) {
            return;
        }

        $('#photo').on('change', function () {
            var input = this;
            var $wrap = $('#image-preview-wrap');
            var $img = $('#image-preview');
            $('#err-photo').text('');
            if (!input.files || !input.files[0]) {
                $wrap.addClass('hidden');
                $img.attr('src', '');
                return;
            }
            var f = input.files[0];
            if (f.size > 2 * 1024 * 1024) {
                $('#err-photo').text('Photo must be 2MB or smaller.');
                $wrap.addClass('hidden');
                $img.attr('src', '');
                input.value = '';
                return;
            }
            var okTypes = ['image/jpeg', 'image/png', 'image/gif'];
            var extOk = /\.(jpe?g|png|gif)$/i.test(f.name || '');
            var typeOk = okTypes.indexOf(f.type) !== -1;
            if (!typeOk && !(extOk && (!f.type || f.type === 'application/octet-stream'))) {
                $('#err-photo').text('Only JPG, PNG, or GIF images are allowed.');
                $wrap.addClass('hidden');
                $img.attr('src', '');
                input.value = '';
                return;
            }
            var prev = $img.data('preview-url');
            if (prev) {
                URL.revokeObjectURL(prev);
            }
            var url = URL.createObjectURL(f);
            $img.data('preview-url', url);
            $img.attr('src', url);
            $wrap.removeClass('hidden');
        });

        $form.on('submit', function (e) {
            clearFieldErrors();
            var ok = true;
            function req(sel, errId, msg) {
                var $el = $(sel);
                if (!$el.val() || ($el.is('select') && $el.val() === '')) {
                    $('#' + errId).text(msg);
                    ok = false;
                }
            }
            req('#item_name', 'err-item_name', 'Item name is required.');
            req('#category', 'err-category', 'Please select a category.');
            req('#color', 'err-color', 'Please select a primary color.');
            req('#location_found', 'err-location_found', 'Please select a location.');
            req('#date_reported', 'err-date_reported', 'Please choose the date found.');

            var fileInput = document.getElementById('photo');
            if (fileInput && fileInput.files && fileInput.files[0]) {
                var f = fileInput.files[0];
                if (f.size > 2 * 1024 * 1024) {
                    $('#err-photo').text('Photo must be 2MB or smaller.');
                    ok = false;
                }
            }

            if (!ok) {
                e.preventDefault();
            }
        });
    }

    /* ——— Claim form: validation + counters ——— */
    function initClaimForm() {
        var $form = $('#claim-form');
        if (!$form.length) {
            return;
        }

        function wireCounter($ta, counterId) {
            var $c = $('#' + counterId);
            function upd() {
                $c.text($ta.val().length);
            }
            $ta.on('input', upd);
            upd();
        }
        wireCounter($('#features'), 'counter-features');
        wireCounter($('#notes'), 'counter-notes');

        $form.on('submit', function (e) {
            clearFieldErrors();
            var ok = true;
            function req(sel, errId, msg) {
                var $el = $(sel);
                if (!$el.val() || ($el.is('select') && $el.val() === '')) {
                    $('#' + errId).text(msg);
                    ok = false;
                }
            }
            req('#item_id', 'err-item_id', 'Please select the found item you are claiming.');
            req('#claim_category', 'err-claim_category', 'Please select a category.');
            req('#claim_color', 'err-claim_color', 'Please select a primary color.');
            req('#item_brand', 'err-item_brand', 'Item name and brand/model are required.');
            req('#last_location', 'err-last_location', 'Please select last known location.');
            req('#size', 'err-size', 'Please select estimated size.');
            req('#date_lost', 'err-date_lost', 'Please choose the date lost.');

            var cn = $('#claimant_name').val().trim();
            var ce = $('#claimant_email').val().trim();
            if (!cn) {
                $('#err-claimant_name').text('Full name is required.');
                ok = false;
            }
            if (!ce) {
                $('#err-claimant_email').text('Email is required.');
                ok = false;
            } else if (ce.indexOf('@') === -1) {
                $('#err-claimant_email').text('Please enter a valid email address.');
                ok = false;
            }

            if (!ok) {
                e.preventDefault();
            }
        });
    }

    /* ——— Login: toggle + client validation ——— */
    function initLogin() {
        var $form = $('#login-form');
        if (!$form.length) {
            return;
        }

        $('#toggle-password').on('click', function () {
            var $pw = $('#password');
            var type = $pw.attr('type') === 'password' ? 'text' : 'password';
            $pw.attr('type', type);
            $(this).text(type === 'password' ? 'Show' : 'Hide');
            $(this).attr('aria-label', type === 'password' ? 'Show password' : 'Hide password');
        });

        $form.on('submit', function (e) {
            $('#err-email').text('');
            $('#err-password').text('');
            var ok = true;
            if (!$('#email').val().trim()) {
                $('#err-email').text('Email is required.');
                ok = false;
            }
            if (!$('#password').val()) {
                $('#err-password').text('Password is required.');
                ok = false;
            }
            if (!ok) {
                e.preventDefault();
            }
        });
    }

    /* ——— Admin: delete confirm ——— */
    function initAdminDelete() {
        $('.form-delete-item').on('submit', function (e) {
            if (!window.confirm('Delete this item permanently? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    }

    $(function () {
        var page = $('body').data('page');
        if (page === 'registry') {
            initRegistrySearch();
        }
        if (page === 'report') {
            initReportForm();
        }
        if (page === 'claim') {
            initClaimForm();
        }
        if (page === 'login') {
            initLogin();
        }
        if (page === 'admin-dashboard') {
            initAdminDelete();
        }
    });
})(jQuery);
