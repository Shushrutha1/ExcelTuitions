
    // Get the modal, buttons, form, and close elements
    const modal = document.getElementById("loginModal");
    const openModalButtons = document.querySelectorAll(".openModalButton");
    const closeButton = document.querySelector(".close");
    const loginForm = document.getElementById("loginForm");

    // Add click event listeners to all buttons with the class "openModalButton"
    openModalButtons.forEach((button) => {
      button.onclick = function (event) {
        event.preventDefault(); // Prevent default anchor behavior
        loginForm.reset(); // Clear the form data
        modal.style.display = "block";
      };
    });

    // Close the modal when the close button is clicked
    closeButton.onclick = function () {
      modal.style.display = "none";
    };

    // Close the modal when clicking outside the modal content
    window.onclick = function (event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    };

    // Close the modal when pressing the "Escape" key
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" || event.key === "Esc") {
        modal.style.display = "none";
      }
    });
