const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

// Trigger links inside forms
const sign_up_link = document.querySelector("#sign-up-link-trigger");
const sign_in_link = document.querySelector("#sign-in-link-trigger");
const sign_in_logo = document.querySelector("#sign-in-link-logo-trigger");
const prev_to_login_btns = document.querySelectorAll(".btn-prev-to-login");

// Swap Animation Logic
if (sign_up_btn) {
    sign_up_btn.addEventListener("click", () => {
        container.classList.add("sign-up-mode");
    });
}

prev_to_login_btns.forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.remove("sign-up-mode");
    });
});

if (sign_up_link) {
    sign_up_link.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.add("sign-up-mode");
    });
}

if (sign_in_btn) {
    sign_in_btn.addEventListener("click", () => {
        container.classList.remove("sign-up-mode");
    });
}

if (sign_in_link) {
    sign_in_link.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.remove("sign-up-mode");
    });
}

if (sign_in_logo) {
    sign_in_logo.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.remove("sign-up-mode");
    });
}

// Multi-step Registration Wizard Logic
const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");

let formStepsNum = 0;

// Function to validate inputs in the current step
function validateCurrentStep() {
    const activeStep = formSteps[formStepsNum];

    // Check for scanner errors first (Added per user request to block invalid uploads)
    const errorInputs = activeStep.querySelectorAll(".input-error");
    if (errorInputs.length > 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Please Fix Errors',
                text: 'Some uploaded documents are invalid or do not meet requirements.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        } else {
            alert('Please fix the errors before proceeding.');
        }
        // focus the first error
        errorInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }

    const inputs = activeStep.querySelectorAll("input, select, textarea");
    for (let input of inputs) {
        if (!input.checkValidity()) {
            input.reportValidity();
            return false;
        }
    }
    return true;
}

nextBtns.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();

        // 1. Client-Side Validation
        if (!validateCurrentStep()) {
            console.log("Validation failed for step " + formStepsNum);
            return;
        }

        // 2. Server-Side Document Validation (Step 1: Primary Docs)
        if (formStepsNum === 1) {
            const formData = new FormData();
            const activeStep = formSteps[formStepsNum];
            const fileInputs = activeStep.querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                if (input.files[0]) {
                    formData.append(input.name, input.files[0]);
                }
            });
            formData.append('action', 'validate_step');
            formData.append('step', 'primary_docs');

            // Show loading state
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
            btn.disabled = true;

            try {
                const response = await fetch('../integration/Documents.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'error') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Validation Failed',
                            text: result.message,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alert(result.message);
                    }
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    return; // Stop here
                }
            } catch (err) {
                console.error("API Error:", err);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // 3. Navigation Logic
        const hasSecondaryDocs = document.querySelector('input[name="has_secondary_docs"]:checked')?.value;

        if (formStepsNum === 1 && hasSecondaryDocs === "no") {
            formStepsNum = 3; // Jump to Step 4 (Guardian)
        } else {
            formStepsNum++;
        }

        updateFormSteps();
        updateProgressbar();

        const scrollContainer = document.querySelector(".register-container-scroll");
        if (scrollContainer) scrollContainer.scrollTop = 0;
    });
});

// Form Submission interceptor to handle jump-to-invalid-step
const signUpForm = document.querySelector(".sign-up-form");
if (signUpForm) {
    signUpForm.addEventListener("submit", (e) => {
        const invalidInput = signUpForm.querySelector(":invalid");
        if (invalidInput) {
            e.preventDefault();
            const parentStep = invalidInput.closest(".form-step");
            if (parentStep) {
                const stepIndex = Array.from(formSteps).indexOf(parentStep);
                if (stepIndex !== formStepsNum) {
                    formStepsNum = stepIndex;
                    updateFormSteps();
                    updateProgressbar();
                }
                // Give it a tiny bit of time to become visible before reporting
                setTimeout(() => {
                    invalidInput.reportValidity();
                }, 50);
            }
        }
    });
}

prevBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
        e.preventDefault();

        // Back from Step 4 (Guardian) to Step 2 (Info) if "no" was selected
        const hasSecondaryDocs = document.querySelector('input[name="has_secondary_docs"]:checked')?.value;

        if (formStepsNum === 3 && hasSecondaryDocs === "no") {
            formStepsNum = 1; // Back to Step 2 (Info)
        } else {
            formStepsNum--;
        }

        updateFormSteps();
        updateProgressbar();
    });
});

function updateFormSteps() {
    formSteps.forEach((formStep) => {
        formStep.classList.contains("form-step-active") &&
            formStep.classList.remove("form-step-active");
    });

    formSteps[formStepsNum].classList.add("form-step-active");
}

function updateProgressbar() {
    // Current Step Labels Mapping
    const stepLabels = [
        "Enrollment & Basic Info",
        "Primary Documents",
        "Secondary Documents",
        "Guardian Information",
        "Educational Background",
        "Account Summary & Credentials"
    ];

    // Update Part X of 6 Text
    const stepText = document.getElementById("step-text");
    if (stepText) {
        stepText.innerText = `Part ${formStepsNum + 1} of 6: ${stepLabels[formStepsNum]}`;
    }

    // Update Horizontal Progress Bar
    const horizontalProgress = document.getElementById("progress");
    if (horizontalProgress) {
        const progressPercent = ((formStepsNum + 1) / 6) * 100;
        horizontalProgress.style.width = progressPercent + "%";
    }

    // Update Vertical Progress Bar Highlights
    const vSteps = document.querySelectorAll(".v-step");
    vSteps.forEach((vStep, idx) => {
        if (idx === formStepsNum) {
            vStep.classList.add("active-v-step");
        } else {
            vStep.classList.remove("active-v-step");
        }
    });

    // Update Horizontal Numbered Steps
    const hSteps = document.querySelectorAll(".h-step");
    hSteps.forEach((hStep, idx) => {
        if (idx < formStepsNum) {
            hStep.classList.remove("active");
            hStep.classList.add("completed");
            hStep.innerHTML = '<i class="fas fa-check"></i>';
        } else if (idx === formStepsNum) {
            hStep.classList.add("active");
            hStep.classList.remove("completed");
            hStep.innerHTML = idx + 1;
        } else {
            hStep.classList.remove("active");
            hStep.classList.remove("completed");
            hStep.innerHTML = idx + 1;
        }
    });

    // Retroactive support for dots if visible (mobile/old)
    progressSteps.forEach((progressStep, idx) => {
        if (idx < formStepsNum + 1) {
            progressStep.classList.add("progress-step-active");
        } else {
            progressStep.classList.remove("progress-step-active");
        }
    });
}
