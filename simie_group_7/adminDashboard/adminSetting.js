function saveProfile() {
  // Get form data
  const form = document.getElementById("profile-form");
  const formData = new FormData(form);

  // Send form data to server
  fetch("submit-profile.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (response.ok) {
        Swal.fire({
          icon: "success",
          title: "Profile Updated",
          text: "Your Profile has been updated Successfully",
          timer: 1500,
        }).then(() => {
          window.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Failed",
          text: "Profile Failed to update",
          confirmButtonText: "OK",
        });
      }
    })
    .then((data) => {
      // Handle response from server
      console.log(data);
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
}
