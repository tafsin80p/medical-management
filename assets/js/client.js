document.addEventListener("DOMContentLoaded", function () {


  // ---------------------------- Notification Function ----------------------------
  function showNotification(message, type = 'success') {

    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    // Create toast container
    const toast = document.createElement("div");
    toast.setAttribute("role", "alert");
    toast.className =
      `fixed top-10 right-4 z-50 p-4 gap-4 flex items-center justify-between w-full max-w-xs text-gray-500 rounded-lg shadow-sm ${type === 'success' ? 'bg-white' : 'bg-red-100 text-red-700'
      }`;

    // Create icon wrapper
    const iconWrapper = document.createElement("div");
    iconWrapper.style.minWidth = "30px";
    iconWrapper.className =
      `inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${type === 'success' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100'
      }`;

    iconWrapper.innerHTML = type === 'success'
      ? `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
           <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
         </svg>`
      : `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
           <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0Zm1 15h-2v-2h2v2Zm0-4h-2V5h2v6Z"/>
         </svg>`;

    // Create message div
    const messageDiv = document.createElement("div");
    messageDiv.className = "ms-3 text-sm font-normal";
    messageDiv.textContent = cleanedMessage;

    // Create close button
    const closeBtn = document.createElement("button");
    closeBtn.type = "button";
    closeBtn.setAttribute("aria-label", "Close");
    closeBtn.className =
      "ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700";

    closeBtn.innerHTML = `
      <span class="sr-only">Close</span>
      <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
      </svg>`;

    closeBtn.addEventListener("click", () => {
      toast.remove();
    });

    // Append children
    toast.append(iconWrapper, messageDiv, closeBtn);

    // Append toast to body
    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => toast.remove(), 3000);
  }


  function addNotification(message, type = 'success', time = 'just now', status = 'unread') {
    const cleanedMessage = message.replace(/[ _?\/]/g, " ");

    // wrapper div
    const div = document.createElement("div");
    div.className = "flex px-6 py-3 hover:bg-gray-100 space-x-4 relative notification-item";
    div.dataset.status = status;

    // message + time container
    const innerDiv = document.createElement("div");
    innerDiv.className = "w-full ps-3";

    // message text
    const msgDiv = document.createElement("div");
    msgDiv.className = `${status === 'unread' ? 'text-gray-800 font-bold' : 'text-gray-500'} text-sm mb-1.5`;
    msgDiv.textContent = cleanedMessage;

    // time text
    const timeDiv = document.createElement("div");
    timeDiv.className = "text-xs text-blue-600 dark:text-blue-500";
    timeDiv.textContent = time;

    innerDiv.appendChild(msgDiv);
    innerDiv.appendChild(timeDiv);
    div.appendChild(innerDiv);

    // unread dot
    if (status === 'unread') {
      const dot = document.createElement("div");
      dot.className = "absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full";
      div.appendChild(dot);
    }

    // Prepend to notification list
    const clientNotificationList = document.getElementById("clientNotificationList");
    if (clientNotificationList) {
      clientNotificationList.prepend(div);
    }

    // Show dot if unread
    if (typeof updateNotificationDot === "function") {
      updateNotificationDot();
    }
  }


  // ---------------------------- Add notification locally AND save to DB ----------------------------
  function pushNotification(message, type) {
    // 1. Show toast
    showNotification(message, type);

    // 2. Add to dropdown
    addNotification(message, type);

    // 3. Save to DB via AJAX (fetch)
    fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
      },
      body: new URLSearchParams({
        action: "add_dashboard_notification",
        nonce: ajax_object.nonce,
        message: message,
        type: type
      })
    })
      .then(res => res.json())
      .then(response => {
        console.log(response);
      })
      .catch(error => {
        console.error("❌ AJAX request failed:", error);
      });
  }




  // ------------------------ tab functionality ----------------------------
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabPanels = document.querySelectorAll(".tab-panel");

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const target = this.dataset.tab;

      // Hide all panels
      tabPanels.forEach((panel) => panel.classList.add("hidden"));

      // Remove active styles from all buttons
      tabButtons.forEach((btn) =>
        btn.classList.remove("border-blue-600", "text-blue-600")
      );

      // Show target panel
      document.getElementById(target).classList.remove("hidden");

      // Add active styles to clicked button
      this.classList.add("border-blue-600", "text-blue-600");
    });
  });

  // Optional: make first tab active on page load
  if (tabButtons.length) {
    tabButtons[0].click();
  }

  // ------------------------ Notification functionality ----------------------------
  const btn = document.getElementById("clientDropdownNotificationButton");
  const panel = document.getElementById("clientDropdownNotification");

  // Toggle panel on button click
  btn.addEventListener("click", function (e) {
    e.stopPropagation();
    panel.classList.toggle("hidden");
  });

  // Close panel if clicked outside
  document.addEventListener("click", function (e) {
    if (!panel.contains(e.target) && !btn.contains(e.target)) {
      panel.classList.add("hidden");
    }
  });

  // Optional: Close with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      panel.classList.add("hidden");
    }
  });

  // ------------------ Show/Hide Form on Button Click ------------------------------
  const addFormBtn = document.getElementById("add-form-button");
  const caseFormContainer = document.getElementById("case-form-container");

  if (addFormBtn && caseFormContainer) {
    addFormBtn.addEventListener("click", () => {
      caseFormContainer.classList.toggle("hidden");
    });

    caseFormContainer.addEventListener("click", (e) => {
      if (e.target === caseFormContainer) {
        caseFormContainer.classList.add("hidden");
      }
    });
  }

  // --- Step Management ---
  const steps = document.querySelectorAll(".step");
  let currentStep = 0;

  const nextBtn = document.getElementById("next-btn");
  const prevBtn = document.getElementById("prev-btn");
  const submitBtn = document.getElementById("submit-btn");

  const stepCircles = document.querySelectorAll(".step-circle");
  const stepLines = document.querySelectorAll(".step-line");

  function showStep(step) {
    steps.forEach((s, i) => s.classList.toggle("hidden", i !== step));
    prevBtn.classList.toggle("disabled", step === 0);
    nextBtn.style.display = step === steps.length - 1 ? "none" : "inline-block";
    submitBtn.classList.toggle("hidden", step !== steps.length - 1);

    updateProgressBar(step + 1);
  }

  function updateProgressBar(step) {
    stepCircles.forEach((circle, i) => {
      if (i < step) {
        circle.classList.add("active");
      } else {
        circle.classList.remove("active");
      }
    });

    stepLines.forEach((line, i) => {
      if (i < step - 1) {
        line.classList.add("active");
      } else {
        line.classList.remove("active");
      }
    });
  }

  // --- Form Validation ---
  function validateStep(step) {
    let valid = true;
    const inputs = steps[step].querySelectorAll("input, select, textarea");

    inputs.forEach((input) => {
      input.classList.remove("border-red-500");

      let oldError = input.parentElement.querySelector(".error-message");
      if (oldError) oldError.remove();

      if (input.hasAttribute("required")) {
        // checkbox check
        if (input.type === "checkbox" && !input.checked) {
          valid = false;
          input.classList.add("border-red-500");
          showError(input, "This field is required");
        }
        // file input check
        else if (input.type === "file" && input.files.length === 0) {
          valid = false;
          const label = input.parentElement.querySelector("label");
          if (label) {
            label.classList.add("border-red-500");
          }
          showError(input, "This file is required");
        }
        // normal text/select/textarea check
        else if (input.type !== "checkbox" && input.type !== "file" && !input.value.trim()) {
          valid = false;
          input.classList.add("border-red-500");
          showError(input, "This field is required");
        }
      }
    });

    return valid;
  }

  // Helper function: error message দেখায়
  function showError(input, message) {
    const error = document.createElement("p");
    error.className = "error-message text-xs text-red-500 mt-1";
    error.innerText = message;

    // file input হলে label-এর পরে বসাও, না হলে input-এর পরে
    if (input.type === "file") {
      const label = input.parentElement.querySelector("label");
      if (label) {
        label.insertAdjacentElement("afterend", error);
      }
    } else {
      input.insertAdjacentElement("afterend", error);
    }
  }




  nextBtn.addEventListener("click", () => {
    if (validateStep(currentStep) && currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  });

  prevBtn.addEventListener("click", () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  showStep(currentStep);

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




  // ------------------ File Input Preview and Removal ------------------------------
  function setupFilePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    input.addEventListener("change", () => {
      preview.innerHTML = "";

      Array.from(input.files).forEach((file, index) => {
        const li = document.createElement("li");
        li.className =
          "flex items-center justify-between bg-gray-100 px-3 py-1 rounded mt-4 text-sm w-full";

        const fileInfo = document.createElement("span");
        fileInfo.textContent = file.name;

        const removeBtn = document.createElement("button");
        removeBtn.innerHTML = `<i class="fa-solid fa-xmark"></i>`;
        removeBtn.className = "ml-2 text-white";

        removeBtn.addEventListener("click", () => {
          // remove file from FileList (workaround: rebuild FileList)
          const dt = new DataTransfer();
          Array.from(input.files).forEach((f, i) => {
            if (i !== index) dt.items.add(f);
          });
          input.files = dt.files;

          li.remove();
        });

        li.appendChild(fileInfo);
        li.appendChild(removeBtn);
        preview.appendChild(li);
      });
    });
  }

  // Attach previews
  setupFilePreview("dd214", "dd214-preview");
  setupFilePreview("medical", "medical-preview");
  setupFilePreview("rating", "rating-preview");
  setupFilePreview("decision", "decision-preview");
  setupFilePreview("optional", "optional-preview");




  // ------------------ AJAX Form Submission ------------------------------

  const form = document.getElementById('intake-form');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    // Add WordPress AJAX action & nonce
    formData.append('action', 'pixelcode_submit_form');
    formData.append('nonce', ajax_object.nonce);

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    fetch(ajax_object.ajax_url, {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';

        if (data.success) {
          form.reset();
          document.querySelectorAll('.preview-list').forEach(ul => ul.innerHTML = '');
          currentStep = 0;
          showStep(currentStep);
          caseFormContainer.classList.toggle("hidden");
          pushNotification(data.data.message, 'success');
        } else {
          pushNotification('Something went wrong. Please try again.', 'error');
        }
      })
      .catch(error => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
        pushNotification('An error occurred. Please try again.', 'error');
      });
  });
















































});

