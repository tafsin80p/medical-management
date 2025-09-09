jQuery(document).ready(function ($) {
  // ---------------------------- Tab functionality for admin dashboard ----------------------------
  const $tabs = $(".tab-button");
  const $panels = $(".tab-panel");

  $tabs.on("click", function () {
    const $tab = $(this);

    // Remove active classes from all tabs
    $tabs
      .removeClass("border-blue-600 text-blue-600")
      .addClass("border-transparent text-gray-500");

    // Hide all panels
    $panels.addClass("hidden");

    // Activate clicked tab
    $tab
      .addClass("border-blue-600 text-blue-600")
      .removeClass("border-transparent text-gray-500");

    // Show corresponding panel
    const target = $tab.data("tab");
    $("#" + target).removeClass("hidden");
  });

  // Activate first tab by default
  $tabs.first().click();

  // ---------------------------- Notification Function ----------------------------
  function showNotification(message, type = "success") {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    // Create toast container
    const $toast = $(`
        <div role="alert" class="fixed top-4 mt-8 right-4 p-4 gap-4 flex items-center justify-between w-full max-w-xs text-gray-500 rounded-lg shadow-sm ${
          type === "success" ? "bg-white" : "bg-red-100 text-red-700"
        }">
        </div>
        `);

    // Create icon wrapper
    const $iconWrapper = $(`
            <div style="min-width: 30px;" class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${
              type === "success"
                ? "text-green-500 bg-green-100"
                : "text-red-500 bg-red-100"
            }">
                ${
                  type === "success"
                    ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                    </svg>`
                    : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0Zm1 15h-2v-2h2v2Zm0-4h-2V5h2v6Z"/>
                    </svg>`
                }
                <span class="sr-only">${
                  type === "success" ? "Check icon" : "Error icon"
                }</span>
            </div>
        `);

    // Create message div
    const $messageDiv = $(
      `<div class="ms-3 text-sm font-normal">${cleanedMessage}</div>`
    );

    // Create close button
    const $closeBtn = $(`
            <button type="button" aria-label="Close" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        `);

    $closeBtn.on("click", function () {
      $toast.remove();
    });

    // Append children
    $toast.append($iconWrapper, $messageDiv, $closeBtn);

    // Append toast to body
    $("body").append($toast);

    // Auto-remove after 3 seconds
    setTimeout(() => $toast.remove(), 3000);
  }

  // ---------------------------- Notification Dropdown functionality ----------------------------
  const $btn = $("#dropdownNotificationButton");
  const $dropdown = $("#dropdownNotification");
  const $notificationList = $("#notificationList");
  const $notificationDot = $("#notificationDot");

  // Toggle dropdown on button click
  $btn.on("click", function (e) {
    e.stopPropagation();
    $dropdown.toggleClass("hidden");
  });

  // Hide dropdown when clicking outside
  $(document).on("click", function (e) {
    if (
      !$dropdown.is(e.target) &&
      $dropdown.has(e.target).length === 0 &&
      !$btn.is(e.target) &&
      $btn.has(e.target).length === 0
    ) {
      $dropdown.addClass("hidden");
    }
  });

  // Add new notification dynamically
  function addNotification(
    message,
    type = "success",
    time = "just now",
    status = "unread"
  ) {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    // Create notification item
    const $div = $(`
            <div data class="flex px-6 py-3 hover:bg-gray-100 space-x-4 relative notification-item" data-status="${status}">
                <div class="w-full ps-3">
                    <div class="${
                      status === "unread" ? "text-gray-800" : "text-gray-500"
                    } text-sm mb-1.5 ${
      status === "unread" && "font-bold"
    }">${cleanedMessage}</div>
                    <div class="text-xs text-blue-600 dark:text-blue-500">${time}</div>
                </div>
                ${
                  status === "unread"
                    ? `
                <div class="absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                `
                    : ""
                }
            </div>
        `);
    $notificationList.prepend($div);

    // Show dot if unread
    updateNotificationDot();
  }

  // Function to check and hide dot if no unread
  function updateNotificationDot() {
    const unreadCount = $notificationList.find(
      '.notification-item[data-status="unread"]'
    ).length;
    if (unreadCount === 0) {
      $notificationDot.addClass("hidden");
    } else {
      $notificationDot.removeClass("hidden");
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
      action: "add_dashboard_notification",
      nonce: ajax_object.nonce,
      message: message,
    });
  }

  // ---------------------------- Test DB Button functionality ----------------------------
  $("#test-db-button").on("click", function (e) {
    e.preventDefault();

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "pixelcode_create_tables",
        nonce: ajax_object.nonce,
      },
      success: function (response) {
        if (response.success) {
          $.each(response.data, function (key, val) {
            showNotification(val.message, "success");
          });
        } else {
          showNotification("Failed to execute request", "error");
        }
      },
      error: function (xhr, status, error) {
        showNotification("AJAX request failed: " + error, "error");
      },
    });
  });

  const view_all = $("#view-all-notifications");

  view_all.on("click", function () {
    // Mark all notifications as read in the UI
    $notificationList.find("div").each(function () {
      $(this)
        .find("div:first")
        .removeClass("text-gray-800 font-bold")
        .addClass("text-gray-500");
      $(this).find("div.absolute").remove();
    });

    // Hide the red dot on the bell icon
    $notificationDot.addClass("hidden");
    // Optionally, you can send an AJAX request to mark all notifications as read in the database
    $.post(ajax_object.ajax_url, {
      action: "mark_all_notifications_read",
      nonce: ajax_object.nonce,
    });
  });

  // ---------------------------------------------------Function to open modal and load case details
  function openCaseDetailsModal(caseId) {
    const modal = document.getElementById("caseDetailsModal");
    const content = document.getElementById("caseDetailsModalContent");

    // Show modal
    modal.classList.remove("hidden");
    content.innerHTML = `
        <div class="flex justify-center items-center h-32">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
    `;

    // Fetch case details via AJAX
    $.post(
      ajax_object.ajax_url,
      {
        action: "pixelcode_get_single_case",
        case_id: caseId,
      },
      function (response) {
        if (response.success) {
          const c = response.data.case;

          // Generate HTML dynamically
          const html = `<div class="space-y-5 text-sm text-gray-700">

                    <!-- Case Header -->
                    <div class="border-b border-gray-200 pb-3 flex justify-between items-center">
                        <div>
                            <h1 class="font-normal text-gray-500 inline">CASE ID:</h1>
                            <span class="font-medium text-gray-800">${
                              c.case_id
                            }</span>
                            <p class="mt-1 font-normal text-gray-500">Created: <span class="font-medium text-gray-800">${new Date(
                              c.created_at
                            ).toLocaleString()}</span></p>
                        </div>
                        <div class="space-x-2 flex">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium ${
                              c.case_status === "pending"
                                ? "bg-yellow-100 text-yellow-800"
                                : "bg-green-100 text-green-800"
                            }">${c.case_status}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium ${
                              c.payment_status === "pending"
                                ? "bg-red-100 text-red-800"
                                : "bg-green-100 text-green-800"
                            }">${
            c.payment_status.toLowerCase() === "pending" ? "Unpaid" : "Paid"
          }</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${
                              c.assigned_to && c.assigned_to.trim() !== ""
                                ? c.assigned_to
                                : "N/A"
                            }</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <!-- Personal Information -->
                        <div class="bg-gray-50 p-3 rounded-md space-y-2">
                            <h3 class="font-semibold text-gray-800">Personal Information</h3>
                            <p><span class="font-normal text-gray-500">Name:</span> <span class="font-medium text-gray-800">${
                              c.first_name
                            } ${c.last_name}</span></p>
                            <p><span class="font-normal text-gray-500">Email:</span> <span class="font-medium text-gray-800">${
                              c.email
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Phone:</span> <span class="font-medium text-gray-800">${
                              c.phone
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Birth Date:</span> <span class="font-medium text-gray-800">${
                              c.birth_date
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Address:</span> <span class="font-medium text-gray-800">${
                              c.address
                            }, ${c.city}, ${c.state} ${c.zip}</span></p>
                            <p><span class="font-normal text-gray-500">VA File Number:</span> <span class="font-medium text-gray-800">${
                              c.va_file_number
                            }</span></p>
                        </div>

                        <!-- Package & Payment -->
                        <div class="bg-gray-50 p-3 rounded-md space-y-2">
                            <h3 class="font-semibold text-gray-800">Package & Payment</h3>
                            <p><span class="font-normal text-gray-500">Package Type:</span> <span class="font-medium text-gray-800">${
                              c.package_type || "N/A"
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Package Price:</span> <span class="font-medium text-gray-800">${
                              c.package_price || "N/A"
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Payment Amount:</span> <span class="font-medium text-gray-800">${
                              c.payment_amount || "N/A"
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Payment Date:</span> <span class="font-medium text-gray-800">${
                              c.payment_date || "N/A"
                            }</span></p>
                            <p><span class="font-normal text-gray-500">Payment Method:</span> <span class="font-medium text-gray-800">${
                              c.payment_method || "N/A"
                            }</span></p>
                        </div>
                    </div>

                    <!-- Service History -->
                    <div class="bg-gray-50 p-3 rounded-md space-y-2">
                        <h3 class="font-semibold text-gray-800">Service History</h3>
                        ${
                          c.service_history.length > 0
                            ? c.service_history
                                .map(
                                  (sh) => `
                            <div class="p-2 border border-gray-200 rounded-md space-y-1">
                                <p><span class="font-normal text-gray-500">Branch:</span> <span class="font-medium text-gray-800">${
                                  sh.branch_of_service
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Composition:</span> <span class="font-medium text-gray-800">${
                                  sh.service_composition
                                }</span></p>
                                <p><span class="font-normal text-gray-500">MOS/AOC Rate:</span> <span class="font-medium text-gray-800">${
                                  sh.mos_aoc_rate || "N/A"
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Duty Position:</span> <span class="font-medium text-gray-800">${
                                  sh.duty_position || "N/A"
                                }</span></p>
                            </div>
                        `
                                )
                                .join("")
                            : '<p class="text-gray-500">No service history</p>'
                        }
                    </div>

                    <!-- VA Claims -->
                    <div class="bg-gray-50 p-3 rounded-md space-y-2">
                        <h3 class="font-semibold text-gray-800">VA Claims</h3>
                        ${
                          c.va_claims.length > 0
                            ? c.va_claims
                                .map(
                                  (vc) => `
                            <div class="p-2 border border-gray-200 rounded-md space-y-1">
                                <p><span class="font-normal text-gray-500">Condition:</span> <span class="font-medium text-gray-800">${
                                  vc.condition
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Claim Type:</span> <span class="font-medium text-gray-800">${
                                  vc.claim_type
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Primary Event:</span> <span class="font-medium text-gray-800">${
                                  vc.primary_event || "N/A"
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Service Explanation:</span> <span class="font-medium text-gray-800">${
                                  vc.service_explanation || "N/A"
                                }</span></p>
                                <p><span class="font-normal text-gray-500">MTF Seen:</span> <span class="font-medium text-gray-800">${
                                  vc.mtf_seen === "1" ? "Yes" : "No"
                                }</span></p>
                                <p><span class="font-normal text-gray-500">Current Treatment:</span> <span class="font-medium text-gray-800">${
                                  vc.current_treatment || "N/A"
                                }</span></p>
                            </div>
                        `
                                )
                                .join("")
                            : '<p class="text-gray-500">No VA claims</p>'
                        }
                    </div>

                    <!-- Documents -->
                    <div class="bg-gray-50 p-3 rounded-md space-y-2">
                        <h3 class="font-semibold text-gray-800">Documents</h3>
                        ${
                          c.documents.length > 0
                            ? c.documents
                                .map(
                                  (doc) => `
                            <div class="flex justify-between items-center p-2 border border-gray-200 rounded-md">
                                <p><span class="font-normal text-gray-500">${
                                  doc.document_type
                                }:</span> <span class="font-medium text-gray-800">${doc.file_path
                                    .split("/")
                                    .pop()}</span></p>
                                <div class="space-x-2">
                                    <a href="${
                                      doc.file_path
                                    }" target="_blank" class="text-blue-600 hover:underline text-xs">View</a>
                                    <a href="${
                                      doc.file_path
                                    }" download class="text-green-600 hover:underline text-xs">Download</a>
                                </div>
                            </div>
                        `
                                )
                                .join("")
                            : '<p class="text-gray-500">No documents uploaded</p>'
                        }
                    </div>

                    <!-- Consent -->
                    <div class="bg-gray-50 p-3 rounded-md space-y-1">
                        <h3 class="font-semibold text-gray-800">Consent</h3>
                        <p><span class="font-normal text-gray-500">Data Collection:</span> <span class="font-medium text-gray-800">${
                          c.consent_data_collection === "1"
                            ? "Agreed"
                            : "Not agreed"
                        }</span></p>
                        <p><span class="font-normal text-gray-500">Privacy Policy:</span> <span class="font-medium text-gray-800">${
                          c.consent_privacy_policy === "1"
                            ? "Agreed"
                            : "Not agreed"
                        }</span></p>
                        <p><span class="font-normal text-gray-500">Communication:</span> <span class="font-medium text-gray-800">${
                          c.consent_communication === "1"
                            ? "Agreed"
                            : "Not agreed"
                        }</span></p>
                    </div>

                </div>
                `;

          content.innerHTML = html;
        } else {
          content.innerHTML = `
                <div class="text-center text-red-500 p-8">
                    <i class="fa-solid fa-exclamation-triangle text-4xl mb-4"></i>
                    <p class="text-lg font-semibold">Error loading case details</p>
                </div>
            `;
        }
      }
    );
  }

  // Event delegation for dynamically created buttons
  $(document).on("click", ".admin_view_btn", function () {
    const caseId = $(this).data("case-id");
    if (caseId) openCaseDetailsModal(caseId);
  });

  // Close modal functionality
  $("#closeCaseDetailsModal").on("click", function () {
    $("#caseDetailsModal").addClass("hidden");
    $("#caseDetailsModalContent").html("");
  });

  // Close modal by clicking outside modal content
  $("#caseDetailsModal").on("click", function (e) {
    if ($(e.target).is("#caseDetailsModal")) {
      $(this).addClass("hidden");
      $("#caseDetailsModalContent").html("");
    }
  });

  $(".status-dropdown").on("change", function () {
    let caseId = $(this).data("case-id");
    let newStatus = $(this).val();

    // Here, we simulate update based on status
    let percentMap = {
      "pending initial review": 25,
      "pending provider review": 50,
      signed: 75,
      completed: 100,
      "": 0,
    };

    let newPercent = percentMap[newStatus] || 0;

    // Update progress bar width
    $('.progress-bar[data-case-id="' + caseId + '"]').css(
      "width",
      newPercent + "%"
    );

    // Update progress text
    $('.progress-text[data-case-id="' + caseId + '"]').text(newPercent + "%");
  });

  // ---------------------------------------------------------------------------------- Edit button click

  // Toggle dropdown on edit button click
  $(".cases_edit_button").on("click", function (e) {
    e.stopPropagation();
    const caseId = $(this).data("case-id");

    // Show all dropdowns for this row
    $("#priority-dropdown-" + caseId)
      .removeClass("hidden")
      .focus();
    $("#priority-badge-" + caseId).addClass("hidden");

    $("#status-dropdown-" + caseId)
      .removeClass("hidden")
      .focus();
    $("#status-badge-" + caseId).addClass("hidden");

    $("#assigned-dropdown-" + caseId)
      .removeClass("hidden")
      .focus();
    $("#assigned-badge-" + caseId).addClass("hidden");
  });

  // Priority Change
  $(".priority-dropdown").on("change", function () {
    const caseId = $(this).data("case-id");
    const newPriority = $(this).val();
    const badge = $("#priority-badge-" + caseId);

    badge.text(newPriority.toUpperCase());
    badge
      .removeClass()
      .addClass(
        "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium " +
          (newPriority === "high"
            ? "bg-red-100 text-red-800"
            : newPriority === "premium"
            ? "bg-yellow-100 text-yellow-800"
            : "bg-gray-100 text-gray-800")
      );

    $(this).addClass("hidden");
    badge.removeClass("hidden");

    $("#status-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#status-badge-" + caseId).removeClass("hidden");

    $("#assigned-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#assigned-badge-" + caseId).removeClass("hidden");

    // AJAX
    $.post(ajaxurl, {
      action: "update_case_priority",
      nonce: ajax_object.nonce,
      case_id: caseId,
      priority: newPriority,
    })
      .done(function (response) {
        if (response.success) {
          pushNotification("Priority Update Success", "success");
        } else {
          pushNotification("Failed to execute request", "error");
        }
      })
      .fail(function (xhr, status, error) {
        pushNotification("Failed to execute request", "error");
      });
  });

  // Status Change
  $(".status-dropdown").on("change", function () {
    const caseId = $(this).data("case-id");
    const newStatus = $(this).val();
    const badge = $("#status-badge-" + caseId);

    badge.text(newStatus.toUpperCase());

    $(this).addClass("hidden");
    badge.removeClass("hidden");

    // Show all dropdowns for this row
    $("#priority-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#priority-badge-" + caseId).removeClass("hidden");

    $("#assigned-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#assigned-badge-" + caseId).removeClass("hidden");

    $.post(ajaxurl, {
      action: "update_case_status",
      nonce: ajax_object.nonce,
      case_id: caseId,
      status: newStatus,
    })
      .done(function (response) {
        if (response.success) {
          pushNotification("Status Update Success", "success");
        } else {
          pushNotification("Failed to execute request", "error");
        }
      })
      .fail(function (xhr, status, error) {
        pushNotification("Failed to execute request", "error");
      });
  });

  // Assigned To Change
  $(".assigned-dropdown").on("change", function () {
    const caseId = $(this).data("case-id");
    const newAssigned = $(this).val();
    const drId = $(this).find(":selected").data("dr-id");

    const badge = $("#assigned-badge-" + caseId);

    badge.text(newAssigned.toUpperCase() || "UNASSIGNED");
    $(this).addClass("hidden");
    badge.removeClass("hidden");

    // Show all dropdowns for this row
    $("#priority-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#priority-badge-" + caseId).removeClass("hidden");

    $("#status-dropdown-" + caseId)
      .addClass("hidden")
      .focus();
    $("#status-badge-" + caseId).removeClass("hidden");

    $.post(ajaxurl, {
      action: "update_case_assigned",
      nonce: ajax_object.nonce,
      case_id: caseId,
      assigned_to: newAssigned,
      dr_id: drId,
    })
      .done(function (response) {
        if (response.success) {
          pushNotification("Dr. Assigned Success", "success");
        } else {
          pushNotification("Failed to execute request", "error");
        }
      })
      .fail(function (xhr, status, error) {
        pushNotification("Failed to execute request", "error");
      });
  });

  $("#dashboard-date-range").on("change", function () {
    let days = $(this).val();

    $.get(ajaxurl, {
      action: "update_dashboard",
      nonce: ajax_object.nonce,
      days: days,
    })
      .done(function (response) {
        $("#total-revenue").text(response.total_revenue);
        $("#cases-completed").text(response.cases_completed);
        $("#active-clients").text(response.active_clients);
        $("#avg-processing").text(response.avg_processing);
      })
      .fail(function (xhr, status, error) {
        console.log("AJAX Error:", error);
      });
  });

  // ----------------------------------------------------------------------- Case Search
  $("#case-search").on("keyup", function () {
    const value = $(this).val().toLowerCase();

    $("table tbody tr").filter(function () {
      const caseId = $(this).find(".case-id").text().toLowerCase();
      $(this).toggle(caseId.indexOf(value) > -1);
    });
  });

  // ------------------------------------------------------------------ Case Status Filter
  $("#case-status-filter").on("change", function () {
    const selected = $(this).val().toLowerCase();

    $("table tbody tr").filter(function () {
      if (selected === "") {
        $(this).show();
      } else {
        const status = $(this).find(".status").text().toLowerCase();
        $(this).toggle(status.indexOf(selected) > -1);
      }
    });
  });

  // ------------------------------------------------------------------ Case Priority Filter
  $("#case-priority-filter").on("change", function () {
    const selected = $(this).val().toLowerCase();

    $("table tbody tr").filter(function () {
      if (selected === "") {
        $(this).show();
      } else {
        const status = $(this).find(".priority").text().toLowerCase();
        $(this).toggle(status.indexOf(selected) > -1);
      }
    });
  });

  // --------------------------- case progress ------------------------------------------
  jQuery(document).ready(function ($) {
    $(".status-dropdown").on("change", function () {
      var caseId = $(this).data("case-id");
      var status = $(this).val();

      // map statuses to progress %
      var map = {
        "pending initial review": 25,
        "pending provider review": 50,
        signed: 75,
        completed: 100,
      };

      var progress = map[status] || 0;

      // update progress bar width
      $('.progress-bar[data-case-id="' + caseId + '"]').css(
        "width",
        progress + "%"
      );

      // update progress text
      $('.progress-text[data-case-id="' + caseId + '"]').text(progress + "%");

      // 🔹 Optional: Save change to DB via AJAX
      $.post(
        ajaxurl,
        {
          action: "update_case_status",
          case_id: caseId,
          case_status: status,
        },
        function (response) {
          console.log("Status updated:", response);
        }
      );
    });
  });

  // ---------------------------- Load notifications from DB on page load ----------------------------
  function loadNotifications() {
    $.post(
      ajax_object.ajax_url,
      {
        action: "get_dashboard_notifications",
        nonce: ajax_object.nonce,
      },
      function (response) {
        if (response.success) {
          response.data.forEach(function (notif) {
            addNotification(
              notif.message,
              notif.type,
              notif.time,
              notif.admin_status
            );
          });
        }
      }
    );
  }

  loadNotifications();
});
