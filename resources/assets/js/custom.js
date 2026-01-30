"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".poll-form");
    if (forms) {
        forms.forEach(function (form) {
            form.addEventListener("submit", function (event) {
                const buttons = document.querySelectorAll(".poll-option");
                buttons.forEach(function (button) {
                    button.disabled = true;
                });
            });
        });
    }

    const answerContainer = document.getElementById("answers-container");
    const form = document.getElementById("questionForm");
    if (answerContainer && form) {
        const isMultipleChoice =
            answerContainer.dataset.isMultipleChoice == "1";
        const limit = parseInt(answerContainer.dataset.answerLimit || 0, 10);
        const errorMessage =
            answerContainer.dataset.noSelectedErrorMessage ||
            "Please select at least one answer before continuing.";
        const limitError =
            answerContainer.dataset.limitErrorMessage ||
            `You can only select up to ${limit} options.`;
        const isEnabledTimer = form.dataset.isEnabledTimer == "1"; // boolean
        const timerType = parseInt(form.dataset.timeType, 10); // 1 = time_question | 2 = time_quiz
        const timerCountDown = parseInt(form.dataset.countdown, 10); // in seconds
        const questionId = document.getElementById("questionId").value || 1;
        const quizUserId = document.getElementById("quizUserId").value || 1;
        const storageKey =
            timerType == 1 ?
                `quiz_end_time_${quizUserId}_${questionId}` :
                `quiz_global_end_time_${quizUserId}`;
        const quizMode = answerContainer.dataset.quizMode;


        let endTime = null;

        if (isEnabledTimer) {
            let timerInterval;

            endTime = localStorage.getItem(storageKey);
            if (!endTime) {
                endTime = Date.now() + timerCountDown * 1000;
                localStorage.setItem(storageKey, endTime);
            } else {
                endTime = parseInt(endTime);
            }

            function updateCountdown() {
                const now = Date.now();
                const timeLeft = Math.floor((endTime - now) / 1000);

                if (timeLeft <= 0) {
                    if (timerInterval) clearInterval(timerInterval);
                    document.getElementById("time_expired").value = timerType;
                    localStorage.removeItem(storageKey);
                    form.submit();
                } else {
                    const timerDisplay =
                        document.getElementById("time-remaining");
                    if (timerDisplay)
                        timerDisplay.textContent = formatTime(timeLeft);
                    updateProgressBar(timeLeft, timerCountDown);
                }
            }

            function formatTime(seconds) {
                const min = Math.floor(seconds / 60);
                const sec = seconds % 60;
                return `${min.toString().padStart(2, "0")}:${sec
                    .toString()
                    .padStart(2, "0")}`;
            }

            function updateProgressBar(timeLeft, totalDuration) {
                const percent = Math.max((timeLeft / totalDuration) * 100, 0);
                const bar = document.getElementById("timer-progress-bar");
                if (bar) {
                    bar.style.width = `${percent}%`;
                    let gradient =
                        "linear-gradient(to right, #f3c6fd, #ac4be0, #651fae)";
                    if (percent < 30)
                        gradient =
                            "linear-gradient(to right, #ff8a8a, #d94d4d)";
                    else if (percent < 60)
                        gradient =
                            "linear-gradient(to right, #ffd480, #ffb347)";
                    bar.style.backgroundImage = gradient;
                }
            }

            updateCountdown();
            timerInterval = setInterval(updateCountdown, 1000);
        }

        const checkboxes = form.querySelectorAll("input[type='checkbox']");
        let selectedOrder = [];
        if (isMultipleChoice && checkboxes.length > 0) {
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener("change", function () {
                    if (limit === 1 && this.checked) {
                        const previouslyChecked = Array.from(checkboxes).find(cb => cb.checked && cb !== this);
                        if (previouslyChecked) {
                            previouslyChecked.checked = false;
                            showAlert(limitError);
                        }
                    }

                    selectedOrder = Array.from(checkboxes).filter(cb => cb.checked);
                    const checkedCount = selectedOrder.length;

                    if (this.checked) {
                        const label = this.closest("label");
                        if (label && !label.classList.contains("Tg7LZd")) {
                            label.classList.add("aS7d2e");
                        }
                    }

                    if (this.checked) {
                        if (checkedCount >= limit) {
                            if (quizMode == 1) {
                                $(".Tg7LZd").each(function () {
                                    this.style.setProperty("background-color", "#ecfdf5", "important");
                                    this.style.setProperty("border", "1px solid #10b981", "important");
                                    this.style.setProperty("border-radius", "0.5rem", "important");

                                    const span = this.querySelector("span");
                                    if (span) {
                                        span.style.setProperty("color", "#10b981", "important");
                                    }

                                    const svgs = this.querySelectorAll("svg path, svg circle");
                                    svgs.forEach((svgEl) => {
                                        if (svgEl.tagName.toLowerCase() === "circle") {
                                            svgEl.setAttribute("stroke", "#10b981");
                                        } else {
                                            svgEl.setAttribute("fill", "#10b981");
                                            svgEl.setAttribute("stroke", "#10b981");
                                        }
                                    });

                                });
                                const correctCheckboxes = Array.from(document.querySelectorAll(".Tg7LZd")).map(el =>
                                    el.querySelector("input[type='checkbox']")
                                ).filter(Boolean);

                                const allCorrectSelected = correctCheckboxes.every(cb => cb.checked);

                                if (!allCorrectSelected) {
                                    $(".aS7d2e").each(function () {
                                        this.style.setProperty("background-color", "#fef2f2", "important");
                                        this.style.setProperty("border", "1px solid #ef4444", "important");
                                        this.style.setProperty("border-radius", "0.5rem", "important");

                                        const span = this.querySelector("span");
                                        if (span) {
                                            span.style.setProperty("color", "#ef4444", "important");
                                        }

                                        const svgs = this.querySelectorAll("svg path, svg circle");
                                        svgs.forEach((svgEl) => {
                                            if (svgEl.tagName.toLowerCase() === "circle") {
                                                svgEl.setAttribute("stroke", "#ef4444");
                                            } else {
                                                svgEl.setAttribute("fill", "#ef4444");
                                                svgEl.setAttribute("stroke", "#ef4444");
                                            }
                                        });

                                    });

                                    $("#question-explanation").removeClass("d-none");
                                }

                                checkboxes.forEach((checkbox) => {

                                    if (checkbox.checked) {
                                        const hiddenInput = document.createElement("input");
                                        hiddenInput.type = "hidden";
                                        hiddenInput.name = checkbox.name;
                                        hiddenInput.value = checkbox.value;
                                        form.appendChild(hiddenInput);
                                    }

                                    checkbox.disabled = true;
                                });
                            } else {
                                if (checkedCount > limit) {
                                    const selectedBeforeThis = Array.from(checkboxes).filter(cb => cb.checked && cb !== this);
                                    const firstSelected = selectedBeforeThis[0];

                                    if (firstSelected) {
                                        firstSelected.checked = false;
                                        firstSelected.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                    showAlert(limitError);
                                }
                            }
                        }
                    }
                });
            });
        } else {
            if (quizMode == 1) {
                const radiobuttons = form.querySelectorAll("input[type='radio']");
                radiobuttons.forEach((checkbox) => {
                    checkbox.addEventListener("change", function () {
                        const checkedCount = form.querySelectorAll(
                            "input[type='radio']:checked"
                        ).length;

                        if (this.checked) {
                            const label = this.closest("label");
                            if (label) {
                                label.classList.remove("aS7d2e");
                                if (!label.classList.contains("Tg7LZd")) {
                                    label.classList.add("aS7d2e");
                                }
                            }
                        }

                        if (checkedCount == 1) {
                            const el = document.querySelector(".Tg7LZd");
                            if (el) {
                                el.style.setProperty("background-color", "#ecfdf5", "important");
                                el.style.setProperty("border", "1px solid #10b981", "important");
                                el.style.setProperty("border-radius", "0.5rem", "important");

                                const span = el.querySelector("span");
                                if (span) {
                                    span.style.setProperty("color", "#10b981", "important");
                                }

                                const svgs = el.querySelectorAll("svg path, svg circle");
                                svgs.forEach((svgEl) => {
                                    if (svgEl.tagName.toLowerCase() === "circle") {
                                        svgEl.setAttribute("stroke", "#10b981");
                                    } else {
                                        svgEl.setAttribute("fill", "#10b981");
                                        svgEl.setAttribute("stroke", "#10b981");
                                    }
                                });

                            }


                            const correctCheckboxes = Array.from(document.querySelectorAll(".Tg7LZd")).map(el =>
                                el.querySelector("input[type='radio']")
                            ).filter(Boolean);

                            const allCorrectSelected = correctCheckboxes.every(cb => cb.checked);

                            if (!allCorrectSelected) {
                                $("#question-explanation").removeClass("d-none");
                                const elements = document.querySelectorAll(".aS7d2e");

                                elements.forEach((el) => {
                                    el.style.setProperty("background-color", "#fef2f2", "important");
                                    el.style.setProperty("border", "1px solid #ef4444", "important");
                                    el.style.setProperty("border-radius", "0.5rem", "important");

                                    const span = el.querySelector("span");
                                    if (span) {
                                        span.style.setProperty("color", "#ef4444", "important");
                                    }

                                    const svgs = el.querySelectorAll("svg path, svg circle");
                                    svgs.forEach((svgEl) => {
                                        if (svgEl.tagName.toLowerCase() === "circle") {
                                            svgEl.setAttribute("stroke", "#ef4444");
                                        } else {
                                            svgEl.setAttribute("fill", "#ef4444");
                                            svgEl.setAttribute("stroke", "#ef4444");
                                        }
                                    });
                                });

                            }


                            radiobuttons.forEach((radio) => {
                                if (radio.checked) {
                                    const hiddenInput = document.createElement("input");
                                    hiddenInput.type = "hidden";
                                    hiddenInput.name = checkbox.name;
                                    hiddenInput.value = checkbox.value;
                                    form.appendChild(hiddenInput);
                                }

                                radio.disabled = true;
                            });
                        }
                    });
                });
            }
        }

        form.addEventListener("submit", function (event) {
            const now = Date.now();
            if (isEnabledTimer && endTime) {
                const timeLeft = Math.floor((endTime - now) / 1000);
                if (timeLeft <= 0) {
                    if (timerInterval) clearInterval(timerInterval);
                    document.getElementById("time_expired").value = timerType;
                    localStorage.removeItem(storageKey);
                    return;
                }
            }

            if (isMultipleChoice && checkboxes.length > 0) {
                const anyChecked = Array.from(checkboxes).some(
                    (checkbox) => checkbox.checked
                );
                if (!anyChecked) {
                    event.preventDefault();
                    showAlert(errorMessage);
                    return;
                }
            } else {
                const selectedAnswer = form.querySelector(
                    'input[name="answer_id"]:checked'
                );
                if (!selectedAnswer) {
                    event.preventDefault();
                    showAlert(errorMessage);
                    return;
                }
            }

            const button = form.querySelector(".submitAnswerButton");
            if (button) {
                button.disabled = true;
            }
        });

        function showAlert(message) {
            const existing = document.getElementById("validation-error");
            if (existing) existing.remove();

            const alertDiv = document.createElement("div");
            alertDiv.id = "validation-error";
            alertDiv.style.color = "#991b1b";
            alertDiv.style.backgroundColor = "#fef2f2";
            alertDiv.className = "p-3 ps-4 mb-1 text-start text-md rounded-lg";
            alertDiv.innerText = message;

            answerContainer.prepend(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    }
});

const container = document.getElementById("answers-container");
if (container) {
    container.addEventListener("click", (event) => {
        const radio = event.target.closest('input[type="radio"]');
        if (radio) {
            container.querySelectorAll("label").forEach((label) => {
                label.style.backgroundColor = "#f3f4f6";
            });

            const label = radio.closest("label");
            if (label) {
                label.style.backgroundColor = "#c66bff3b";
            }
        }
    });
}
