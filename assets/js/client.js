document.addEventListener("DOMContentLoaded", function () {
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
  let currentStep = 3;

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
      if (i < step) {
        line.classList.add("active");
      } else {
        line.classList.remove("active");
      }
    });
  }

  function validateStep(step) {
    let valid = true;
    const inputs = steps[step].querySelectorAll("input,select,textarea");
    inputs.forEach((input) => {
      input.classList.remove("border-red-500");
      if (
        input.hasAttribute("required") &&
        ((input.type === "checkbox" && !input.checked) ||
          (!input.value.trim() && input.type !== "checkbox"))
      ) {
        valid = false;
        input.classList.add("border-red-500");
      }
    });
    return valid;
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
});

// ------------------ File Input Preview and Removal ------------------------------
function setupFilePreview(inputId, previewId) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewId);

  input.addEventListener("change", () => {
    preview.innerHTML = ""; // reset preview

    Array.from(input.files).forEach((file, index) => {
      const li = document.createElement("li");
      li.className =
        "flex items-center justify-between bg-gray-100 px-3 py-1 rounded";

      const fileInfo = document.createElement("span");
      fileInfo.textContent = file.name;

      const removeBtn = document.createElement("button");
      removeBtn.textContent = "❌";
      removeBtn.className = "ml-2 text-red-600 hover:text-red-800";

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
