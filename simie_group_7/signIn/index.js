const loginForm = document.getElementById("loginForm");
loginForm.addEventListener("submit", (event) => {
  event.preventDefault();
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const data = { email, password };
  fetch("api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (response.ok) {
        Swal.fire({
          icon: "success",
          title: "Login successful",
          timer: 1500,
        }).then(() => {
          // redirect to dashboard or other page
          window.location.href = "dashboard/index.html";
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Login failed",
          text: "Please check your email and password",
          confirmButtonText: "OK",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "An error occurred",
        text: "Please try again later",
        confirmButtonText: "OK",
      });
    });
});
