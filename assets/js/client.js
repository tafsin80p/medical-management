jQuery(document).ready(function ($) {

  // ---------------------------- Notification Function ----------------------------
  function showNotification(message, type = "success") {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    let toast = $(`
      <div role="alert" class="fixed top-10 right-4 z-50 p-4 gap-4 flex items-center justify-between w-full max-w-xs text-gray-500 rounded-lg shadow-sm pixelcodeNotification ${
        type === "success" ? "bg-white" : "bg-red-100 text-red-700"
      }">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${
          type === "success"
            ? "text-green-500 bg-green-100"
            : "text-red-500 bg-red-100"
        }">
          ${
            type === "success"
              ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
               </svg>`
              : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0Zm1 15h-2v-2h2v2Zm0-4h-2V5h2v6Z"/>
               </svg>`
          }
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
  function addNotification(message, status = "unread", time = "just now") {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    let div = $(`
      <div class="flex px-6 py-3 hover:bg-gray-100 space-x-4 relative notification-item" data-status="${status}">
        <div class="w-full ps-3">
          <div class="${
            status === "unread" ? "text-gray-800 font-bold" : "text-gray-500"
          } text-sm mb-1.5">${cleanedMessage}</div>
          <div class="text-xs text-blue-600 dark:text-blue-500">${time}</div>
        </div>
        ${
          status === "unread"
            ? `<div class="absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full"></div>`
            : ""
        }
      </div>
    `);

    $("#clientNotificationList").prepend(div);

    if (typeof updateNotificationDot === "function") {
      updateNotificationDot();
    }
  }

  const $clientNotificationList = $("#clientNotificationList");
  const $clientNotificationDot = $("#clientNotificationDot");
  const $viewAll = $("#clientViewAllNotifications");

  $viewAll.on("click", function () {
    // 1️⃣ Mark all notifications as read in the UI
    $clientNotificationList.find(".notification-item").each(function () {
      const $item = $(this);

      // Update message text style
      $item
        .find(".w-full > div:first")
        .removeClass("text-gray-800 font-bold")
        .addClass("text-gray-500");

      // Remove unread dot
      $item.find(".absolute").remove();

      // Update data-status
      $item.attr("data-status", "read");
    });

    // 2️⃣ Hide the red dot on the bell icon
    $clientNotificationDot.addClass("hidden");

    $.post(ajax_object.ajax_url, {
      action: "mark_all_notifications_read",
    });
  });

  // ---------------------------- Push Notification ----------------------------
  function pushNotification(message, type) {
    showNotification(message, type);
    addNotification(message);

    $.post(ajax_object.ajax_url, {
      action: "add_dashboard_notification",
      nonce: ajax_object.nonce,
      message: message,
      type: type,
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
    if (
      !$(e.target).closest(
        "#clientDropdownNotification, #clientDropdownNotificationButton"
      ).length
    ) {
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
        input
          .addClass("border-red-500")
          .after(
            `<p class="error-message text-xs text-red-500 mt-1">${input.attr("placeholder")} is required</p>`
          );
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
          loadCases();
        } else {
          pushNotification(data.data.message, "error");
        }
      },
      error: function () {
        $("#submit-btn").prop("disabled", false).text("Submit");
        pushNotification("An error occurred. Please try again.", "error");
      },
    });
  });

  function getAssignedColor(name) {
    const assignedColors = {
      'unassigned': "bg-gray-100 text-gray-800",
    };

    if (assignedColors[name.toLowerCase()]) {
      return assignedColors[name.toLowerCase()];
    }

    // Simple hash function to generate a color
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
      hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    // For simplicity, we'll just use a few predefined colors
    const colors = [
      "bg-pink-100 text-pink-800",
      "bg-indigo-100 text-indigo-800",
      "bg-teal-100 text-teal-800",
    ];
    return colors[Math.abs(hash) % colors.length];
  }

  // ---------------------------- Cases Table ----------------------------
  function loadCases() {
    console.log('ok');
    
    const tbody = $("#case-table-body");
    tbody.html(
      '<tr><td colspan="7" class="px-6 py-4 text-center"><div class="flex justify-center items-center space-x-2"><svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-gray-600">Loading cases...</span></div></td></tr>'
    );

    $.post(
      ajax_object.ajax_url,
      { action: "pixelcode_get_all_cases" },
      function (response) {
        if (response.success) {
          const cases = response.data.cases;

          console.log(cases);
          
          
          tbody.empty();

          if (!cases || cases.length === 0) {
            tbody.html(
              '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No cases found</td></tr>'
            );
            return;
          }

          // Progress Mapping
          const progressMap = {
            "pending initial review": 25,
            "pending provider review": 50,
            signed: 75,
            completed: 100,
          };

          const priorityColors = {
            low: "bg-gray-100 text-gray-800",
            high: "bg-red-100 text-red-800",
            premium: "bg-yellow-100 text-yellow-800",
          };

          const statusColors = {
            'pending initial review': "bg-yellow-100 text-yellow-800",
            'pending provider review': "bg-blue-100 text-blue-800",
            signed: "bg-green-100 text-green-800",
            completed: "bg-purple-100 text-purple-800",
          };

          cases.forEach((c) => {
            const progress = progressMap[c.case_status] ?? 0;
            const priorityClass = priorityColors[c.priority.toLowerCase()] || priorityColors['low'];
            const statusClass = statusColors[c.case_status.toLowerCase()] || "bg-gray-100 text-gray-800";
            const assignedTo = c.assigned_to || "unassigned";
            const assignedClass = getAssignedColor(assignedTo);

            const row = `
                  <tr id="${
                    c.case_id
                  }" class="odd:bg-white even:bg-b-50 border-b">

                    <!-- Case Info -->
                    <td class="px-6 py-4">
                        <div class="text-md font-medium text-gray-900">
                            MD NEXUSPROS CASES - ${c.first_name ?? "N/A"} ${
              c.last_name ?? "N/A"
            }
                        </div>
                        <div class="text-sm text-gray-500 mt-2">
                            Case ID: <span class="text-gray-800 font-medium case-id">${
                              c.case_id ?? "N/A"
                            }</span>
                        </div>
                    </td>

                    <!-- Priority -->
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${priorityClass} priority">
                            ${(c.priority || "N/A").toUpperCase()}
                        </span>
                    </td>

                    <!-- Case Status -->
                    <td class="px-6 py-4">
                      <span class="status-span inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass} status">
                        ${(c.case_status ?? "N/A").toUpperCase()}
                      </span>
                    </td>

                    <!-- Assigned To -->
                    <td class="px-6 py-4">
                      <span class="assigned-span inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${assignedClass}">
                        ${assignedTo.toUpperCase()}
                      </span>
                    </td>

                    <!-- Progress -->
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full progress-bar" data-case-id="${c.case_id}"
                                    style="width: ${progress}%; transition: width 0.5s;">
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 progress-text" data-case-id="${c.case_id}">
                                ${progress}%
                            </span>
                        </div>
                    </td>

                    <!-- Created At -->
                    <td class="px-6 py-4 text-sm text-gray-900">
                      ${c.created_at ?? "N/A"}
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-sm font-medium space-x-4">
                      <button class="text-blue-600 hover:text-blue-900 client_view_btn" data-case-id="${
                        c.case_id
                      }">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                    </td>

                  </tr>
                  `;
            tbody.append(row);
          });
        } else {
          tbody.html(
            '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading cases</td></tr>'
          );
        }
      }
    );
  }

  // Function to open modal and load case details
  function openCaseModal(caseId) {
    const modal = document.getElementById("caseModal");
    const content = document.getElementById("modalContent");

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
                    <span class="font-medium text-gray-800">${c.case_id}</span>
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
                              window.location.origin + doc.file_path
                            }" target="_blank" class="text-blue-600 hover:underline text-xs">View</a>
                            <a href="${
                              window.location.origin + doc.file_path
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
                  c.consent_data_collection === "1" ? "Agreed" : "Not agreed"
                }</span></p>
                <p><span class="font-normal text-gray-500">Privacy Policy:</span> <span class="font-medium text-gray-800">${
                  c.consent_privacy_policy === "1" ? "Agreed" : "Not agreed"
                }</span></p>
                <p><span class="font-normal text-gray-500">Communication:</span> <span class="font-medium text-gray-800">${
                  c.consent_communication === "1" ? "Agreed" : "Not agreed"
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
  $(document).on("click", ".client_view_btn", function () {
    const caseId = $(this).data("case-id");
    if (caseId) openCaseModal(caseId);
  });

  // Close modal functionality
  $("#closeCaseModal").on("click", function () {
    $("#caseModal").addClass("hidden");
    $("#modalContent").html("");
  });

  // Close modal by clicking outside modal content
  $("#caseModal").on("click", function (e) {
    if ($(e.target).is("#caseModal")) {
      $(this).addClass("hidden");
      $("#modalContent").html("");
    }
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
        const priority = $(this).find(".priority").text().toLowerCase();
        $(this).toggle(priority.indexOf(selected) > -1);
      }
    });
  });

  // --- Add Deployment ---
  function addDeployment(e) {
    const deploymentContainer = e.target.closest(".deployments-container");

    const template = deploymentContainer.querySelector(".deployment-entry");

    const clone = template.cloneNode(true);
    clone.querySelectorAll("input").forEach((i) => (i.value = ""));

    // Insert before the button, so new entries appear above it
    deploymentContainer.insertBefore(clone, e.target);
  }

  // Attach listener
  document.querySelectorAll(".add-deployment").forEach((btn) => {
    btn.addEventListener("click", addDeployment);
  });

  // --- Add VA Claim ---
  document.getElementById("add-va-claim").addEventListener("click", () => {
    const container = document.getElementById("va-claims-container");
    const entry = container.querySelector(".va-claim-entry").cloneNode(true);
    entry
      .querySelectorAll("input,textarea,select")
      .forEach((el) => (el.value = ""));
    container.appendChild(entry);
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
            addNotification(notif.message, notif.user_status, notif.time);
          });
        }
      }
    );
  }

  loadNotifications();
  loadCases();
});
