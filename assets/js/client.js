jQuery(document).ready(function ($) {

  // ---------------------------- Notification Function ----------------------------
  function showNotification(message, type = 'success') {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    let toast = $(`
      <div role="alert" class="fixed top-10 right-4 z-50 p-4 gap-4 flex items-center justify-between w-full max-w-xs text-gray-500 rounded-lg shadow-sm ${type === 'success' ? 'bg-white' : 'bg-red-100 text-red-700'}">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100'}">
          ${type === 'success'
        ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
               </svg>`
        : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0Zm1 15h-2v-2h2v2Zm0-4h-2V5h2v6Z"/>
               </svg>`}
        </div>
        <div class="ms-3 text-sm font-normal">${cleanedMessage}</div>
        <button type="button" aria-label="Close" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8">
          <span class="sr-only">Close</span>
          <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </button>
      </div>
    `);

    toast.find("button").on("click", function () {
      toast.remove();
    });

    $("body").append(toast);

    setTimeout(() => toast.remove(), 3000);
  }






  // ---------------------------- Add Notification to Dropdown ----------------------------
  function addNotification(message, status = 'unread', time = 'just now') {

    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    let div = $(`
      <div class="flex px-6 py-3 hover:bg-gray-100 space-x-4 relative notification-item" data-status="${status}">
        <div class="w-full ps-3">
          <div class="${status === 'unread' ? 'text-gray-800 font-bold' : 'text-gray-500'} text-sm mb-1.5">${cleanedMessage}</div>
          <div class="text-xs text-blue-600 dark:text-blue-500">${time}</div>
        </div>
        ${status === 'unread' ? `<div class="absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full"></div>` : ''}
      </div>
    `);

    $("#clientNotificationList").prepend(div);

    if (typeof updateNotificationDot === "function") {
      updateNotificationDot();
    }
  }




  const $clientNotificationList = $('#clientNotificationList');
  const $clientNotificationDot = $('#clientNotificationDot');
  const $viewAll = $('#clientViewAllNotifications');

  $viewAll.on('click', function () {

    // 1️⃣ Mark all notifications as read in the UI
    $clientNotificationList.find('.notification-item').each(function () {
      const $item = $(this);

      // Update message text style
      $item.find('.w-full > div:first')
        .removeClass('text-gray-800 font-bold')
        .addClass('text-gray-500');

      // Remove unread dot
      $item.find('.absolute').remove();

      // Update data-status
      $item.attr('data-status', 'read');
    });

    // 2️⃣ Hide the red dot on the bell icon
    $clientNotificationDot.addClass('hidden');
    
    $.post(ajax_object.ajax_url, {
      action: 'mark_all_notifications_read',
    })
  });






  // ---------------------------- Push Notification ----------------------------
  function pushNotification(message, type) {
    showNotification(message, type);
    addNotification(message);

    $.post(ajax_object.ajax_url, {
      action: "add_dashboard_notification",
      nonce: ajax_object.nonce,
      message: message,
      type: type
    });
  }




  // ---------------------------- Tabs ----------------------------
  $(".tab-button").on("click", function () {
    let target = $(this).data("tab");

    $(".tab-panel").addClass("hidden");
    $(".tab-button").removeClass("border-blue-600 text-blue-600");

    $("#" + target).removeClass("hidden");
    $(this).addClass("border-blue-600 text-blue-600");
  });
  $(".tab-button").first().click();





  // ---------------------------- Notification Dropdown ----------------------------
  $("#clientDropdownNotificationButton").on("click", function (e) {
    e.stopPropagation();
    $("#clientDropdownNotification").toggleClass("hidden");
  });

  $(document).on("click", function (e) {
    if (!$(e.target).closest("#clientDropdownNotification, #clientDropdownNotificationButton").length) {
      $("#clientDropdownNotification").addClass("hidden");
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $("#clientDropdownNotification").addClass("hidden");
    }
  });

  // ---------------------------- Show/Hide Form ----------------------------
  $("#add-form-button").on("click", function () {
    $("#case-form-container").toggleClass("hidden");
  });
  $("#case-form-container").on("click", function (e) {
    if (e.target === this) {
      $(this).addClass("hidden");
    }
  });

  // ---------------------------- Steps ----------------------------
  let steps = $(".step");
  let currentStep = 0;

  function showStep(step) {
    steps.addClass("hidden").eq(step).removeClass("hidden");
    $("#prev-btn").toggleClass("disabled", step === 0);
    $("#next-btn").toggle(step !== steps.length - 1);
    $("#submit-btn").toggleClass("hidden", step !== steps.length - 1);

    updateProgressBar(step + 1);
  }

  function updateProgressBar(step) {
    $(".step-circle").each(function (i) {
      $(this).toggleClass("active", i < step);
    });
    $(".step-line").each(function (i) {
      $(this).toggleClass("active", i < step - 1);
    });
  }

  function validateStep(step) {
    let valid = true;
    let inputs = steps.eq(step).find("input, select, textarea");

    inputs.each(function () {
      let input = $(this);
      input.removeClass("border-red-500");
      input.siblings(".error-message").remove();

      if (input.prop("required") && !input.val().trim()) {
        valid = false;
        input.addClass("border-red-500").after(`<p class="error-message text-xs text-red-500 mt-1">This field is required</p>`);
      }
    });

    return valid;
  }

  $("#next-btn").on("click", function () {
    if (validateStep(currentStep) && currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  });
  $("#prev-btn").on("click", function () {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });
  showStep(currentStep);

  // ---------------------------- File Preview ----------------------------
  function setupFilePreview(inputId, previewId) {
    $("#" + inputId).on("change", function () {
      let input = this;
      let preview = $("#" + previewId);
      preview.empty();

      $.each(input.files, function (index, file) {
        let li = $(`
          <li class="flex items-center justify-between bg-gray-100 px-3 py-1 rounded mt-4 text-sm w-full">
            <span>${file.name}</span>
            <button class="ml-2 text-white"><i class="fa-solid fa-xmark"></i></button>
          </li>
        `);

        li.find("button").on("click", function () {
          let dt = new DataTransfer();
          $.each(input.files, function (i, f) {
            if (i !== index) dt.items.add(f);
          });
          input.files = dt.files;
          li.remove();
        });

        preview.append(li);
      });
    });
  }

  setupFilePreview("dd214", "dd214-preview");
  setupFilePreview("medical", "medical-preview");
  setupFilePreview("rating", "rating-preview");
  setupFilePreview("decision", "decision-preview");
  setupFilePreview("optional", "optional-preview");

  // ---------------------------- Form Submit ----------------------------
  $("#intake-form").on("submit", function (e) {
    e.preventDefault();
    let form = $(this)[0];
    let formData = new FormData(form);

    formData.append("action", "pixelcode_submit_form");
    formData.append("nonce", ajax_object.nonce);

    $("#submit-btn").prop("disabled", true).text("Submitting...");

    $.ajax({
      url: ajax_object.ajax_url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data) {
        $("#submit-btn").prop("disabled", false).text("Submit");

        if (data.success) {
          form.reset();
          $(".preview-list").empty();
          currentStep = 0;
          showStep(currentStep);
          $("#case-form-container").addClass("hidden");
          pushNotification(data.data.message, "success");
        } else {
          pushNotification("Something went wrong. Please try again.", "error");
        }
      },
      error: function () {
        $("#submit-btn").prop("disabled", false).text("Submit");
        pushNotification("An error occurred. Please try again.", "error");
      }
    });
  });





























  // ---------------------------- Load notifications from DB on page load ----------------------------
  function loadNotifications() {
    $.post(ajax_object.ajax_url, {
      action: 'get_dashboard_notifications',
      nonce: ajax_object.nonce
    }, function (response) {
      if (response.success) {
        response.data.forEach(function (notif) {
          addNotification(notif.message, notif.user_status, notif.time);
        });
      }
    });
  }

  loadNotifications();

});
