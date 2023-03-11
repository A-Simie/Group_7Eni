let departments = [];

function addDepartment() {
  const departmentInput = document.getElementById("department");
  const departmentList = document.getElementById("departmentList");

  // Add department to array and clear input field
  departments.push(departmentInput.value);
  departmentInput.value = "";

  // Clear previous list items
  departmentList.innerHTML = "";

  // Add new list items for each department
  departments.forEach((department) => {
    const listItem = document.createElement("li");
    listItem.textContent = department;
    departmentList.appendChild(listItem);
  });
}

// Function to send data to PHP API
function sendData() {
  const facultyInput = document.getElementById("faculty");
  const data = {
    faculty: facultyInput.value,
    departments: departments,
  };

  // Send data to PHP API using fetch()
  fetch("path/to/api.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (response.ok) {
        Swal.fire({
          icon: "success",
          title: "Successfully Added",
          text: "Faculty and Department added Successfully",
          timer: 1500,
        }).then(() => {
          window.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Failed",
          text: "Adding Faculty and Department Failed",
          confirmButtonText: "OK",
        });
      }
    })
    .then((data) => console.log(data))
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
