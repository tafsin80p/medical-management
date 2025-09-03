jQuery(document).ready(function ($) {
    // ---------------------------- Tab functionality for admin dashboard ----------------------------
    const $tabs = $('.tab-button');
    const $panels = $('.tab-panel');

    $tabs.on('click', function () {
        const $tab = $(this);

        // Remove active classes from all tabs
        $tabs.removeClass('border-blue-600 text-blue-600')
            .addClass('border-transparent text-gray-500');

        // Hide all panels
        $panels.addClass('hidden');

        // Activate clicked tab
        $tab.addClass('border-blue-600 text-blue-600')
            .removeClass('border-transparent text-gray-500');

        // Show corresponding panel
        const target = $tab.data('tab');
        $('#' + target).removeClass('hidden');
    });

    // Activate first tab by default
    $tabs.first().click();



    // ---------------------------- Notification Function ----------------------------
    function showNotification(message, type = 'success') {

        const cleanedMessage = message.replace(/[ _?\/]/g, " ");

        // Create toast container
        const $toast = $(`
        <div role="alert" class="fixed top-4 mt-8 right-4 p-4 gap-4 flex items-center justify-between w-full max-w-xs text-gray-500 rounded-lg shadow-sm ${type === 'success' ? 'bg-white' : 'bg-red-100 text-red-700'}">
        </div>
        `);

        // Create icon wrapper
        const $iconWrapper = $(`
            <div style="min-width: 30px;" class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100'}">
                ${type === 'success'
                ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                    </svg>`
                : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0Zm1 15h-2v-2h2v2Zm0-4h-2V5h2v6Z"/>
                    </svg>`}
                <span class="sr-only">${type === 'success' ? 'Check icon' : 'Error icon'}</span>
            </div>
        `);

        // Create message div
        const $messageDiv = $(`<div class="ms-3 text-sm font-normal">${cleanedMessage}</div>`);

        // Create close button
        const $closeBtn = $(`
            <button type="button" aria-label="Close" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        `);

        $closeBtn.on('click', function () {
            $toast.remove();
        });

        // Append children
        $toast.append($iconWrapper, $messageDiv, $closeBtn);

        // Append toast to body
        $('body').append($toast);

        // Auto-remove after 3 seconds
        setTimeout(() => $toast.remove(), 3000);
    }



    // ---------------------------- Notification Dropdown functionality ----------------------------
    const $btn = $('#dropdownNotificationButton');
    const $dropdown = $('#dropdownNotification');
    const $notificationList = $('#notificationList');
    const $notificationDot = $('#notificationDot');

    // Toggle dropdown on button click
    $btn.on('click', function (e) {
        e.stopPropagation(); // prevent the document click handler
        $dropdown.toggleClass('hidden');
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0 &&
            !$btn.is(e.target) && $btn.has(e.target).length === 0) {
            $dropdown.addClass('hidden');
        }
    });




    // Add new notification dynamically
    function addNotification(message, type = 'success', time = 'just now', status = 'unread',) {

        const cleanedMessage = message.replace(/[ _?\/]/g, " ");

        // Create notification item
        const $div = $(`
            <div data class="flex px-6 py-3 hover:bg-gray-100 space-x-4 relative notification-item" data-status="${status}">
                <div class="w-full ps-3">
                    <div class="${status === 'unread' ? 'text-gray-800' : 'text-gray-500'} text-sm mb-1.5 ${status === 'unread' && 'font-bold'}">${cleanedMessage}</div>
                    <div class="text-xs text-blue-600 dark:text-blue-500">${time}</div>
                </div>
                ${status === 'unread' ? `
                <div class="absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                ` : ''}
            </div>
        `);
        $notificationList.prepend($div);

        // Show dot if unread
        updateNotificationDot();
    }



    // Function to check and hide dot if no unread
    function updateNotificationDot() {
        const unreadCount = $notificationList.find('.notification-item[data-status="unread"]').length;
        if (unreadCount === 0) {
            $notificationDot.addClass('hidden');
        } else {
            $notificationDot.removeClass('hidden');
        }
    }



    // Add notification locally AND save to DB
    function pushNotification(message, type) {
        // 1. Show toast
        showNotification(message, type);

        // 2. Add to dropdown
        addNotification(message, type);

        // 3. Save to DB
        $.post(ajax_object.ajax_url, {
            action: 'add_dashboard_notification',
            nonce: ajax_object.nonce,
            message: message,
        });
    }



    // ---------------------------- Test DB Button functionality ----------------------------
    $('#test-db-button').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'pixelcode_create_tables',
                nonce: ajax_object.nonce
            },
            success: function (response) {
                if (response.success) {
                    $.each(response.data, function (key, val) {
                        showNotification(val.message, 'success');
                    });
                } else {
                    showNotification('Failed to execute request', 'error');
                }
            },
            error: function (xhr, status, error) {
                showNotification('AJAX request failed: ' + error, 'error');
            }
        });
    });




    const view_all = $('#view-all-notifications');

    view_all.on('click', function () {
        // Mark all notifications as read in the UI
        $notificationList.find('div').each(function () {
            $(this).find('div:first').removeClass('text-gray-800 font-bold').addClass('text-gray-500');
            $(this).find('div.absolute').remove();
        });

        // Hide the red dot on the bell icon
        $notificationDot.addClass('hidden');
        // Optionally, you can send an AJAX request to mark all notifications as read in the database
        $.post(ajax_object.ajax_url, {
            action: 'mark_all_notifications_read',
            nonce: ajax_object.nonce
        });
    })



    // ---------------------------- Load notifications from DB on page load ----------------------------
    function loadNotifications() {
        $.post(ajax_object.ajax_url, {
            action: 'get_dashboard_notifications',
            nonce: ajax_object.nonce
        }, function (response) {
            if (response.success) {
                response.data.forEach(function (notif) {
                    addNotification(notif.message, notif.type, notif.time, notif.admin_status);
                });
            }
        });
    }

    loadNotifications();
});
