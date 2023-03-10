function saveProfile() {
  // Get form data
  const form = document.getElementById("profile-form");
  const formData = new FormData(form);

  // Send form data to server
  fetch("submit-profile.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      // Handle response from server
      console.log(data);
    })
    .catch((error) => {
      console.error(error);
    });
}
