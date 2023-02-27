const FacultyAndDept = [
  {
    name: "Communication And Information Sciences",
    key: "CIS",
    departments: [
      {
        name: "--Choose Department--",
        key: "null",
      },
      {
        name: "Information and Communication Science",
        key: "ICS",
      },
      {
        name: "Telecommunication and Communication Science",
        key: "TCS",
      },
      {
        name: "Library and Information Science",
        key: "LIS",
      },
      {
        name: "Mass Communication",
        key: "MAC",
      },
      {
        name: "Computer Science",
        key: "CSC",
      },
    ],
  },
  {
    name: "Engineering and Technology",
    key: "ENGR",
    departments: [
      {
        name: "--Choose Department--",
        key: "null",
      },
      {
        name: "Computer Engineering",
        key: "CPENG",
      },
      {
        name: "Food Engineering",
        key: "FENG",
      },
      {
        name: "Civil Engineering",
        key: "CENGR",
      },
      {
        name: "Chemical Engineering",
        key: "CHENGR",
      },
      {
        name: "Agricultural Engineering",
        key: "AENGR",
      },
    ],
  },
  {
    name: "Agriculture",
    key: "AGRIC",
    departments: [
      {
        name: "--Choose Department--",
        key: "null",
      },
      {
        name: "Home Science",
        key: "HSC",
      },
      {
        name: "Forestry and Wildlife",
        key: "FRW",
      },
      {
        name: "Agronomy",
        key: "AGR",
      },
      {
        name: "Animal Production",
        key: "APR",
      },
      {
        name: "Crop Protection",
        key: "CRP",
      },
    ],
  },
  {
    name: "Education",
    key: "EDU",
    departments: [
      {
        name: "--Choose Department--",
        key: "null",
      },
      {
        name: "Biology Education",
        key: "BEDU",
      },
      {
        name: "Computer Education",
        key: "COEDU",
      },
      {
        name: "Physics Education",
        key: "PEDU",
      },
      {
        name: "Chemistry Education",
        key: "CEDU",
      },
      {
        name: "Adult Education",
        key: "AEDU",
      },
    ],
  },
  {
    name: "Law",
    key: "LAW",
    departments: [
      {
        name: "--Choose Department--",
        key: "null",
      },
      {
        name: "Public and Private International Law",
        key: "PPIL",
      },
      {
        name: "Private and Islamic Law",
        key: "PIL",
      },
      {
        name: "Criminology and Security Studies",
        key: "CSS",
      },
      {
        name: "Common Law",
        key: "COL",
      },
      {
        name: "Civil Law",
        key: "CIL",
      },
    ],
  },
];

const facultySelect = document.getElementById("faculty");
const deptSelect = document.getElementById("department");

FacultyAndDept.forEach((faculty) => {
  const option = document.createElement("option");
  option.text = faculty.name;
  option.value = faculty.name;
  option.key = faculty.key;
  console.log(faculty);
  facultySelect.appendChild(option);
});

// Add an event listener to the faculty select input
facultySelect.addEventListener("change", () => {
  // Get the selected faculty
  const selectedFaculty = FacultyAndDept.find(
    (faculty) => faculty.name === facultySelect.value
  );
  console.log(selectedFaculty);

  // Clear the Department select input
  deptSelect.innerHTML = "";

  // Filter the list of Departments to only include Departments in the selected faculty
  const filteredDept = selectedFaculty.departments;
  console.log(filteredDept);
  // Update the Department select input with the filtered list of Departments
  filteredDept.map((dept) => {
    const option = document.createElement("option");
    option.text = dept.name;
    option.value = dept.name;
    option.key = dept.key;
    console.log(dept);
    deptSelect.appendChild(option);
  });
});

// Add an event listener to the dept select input
deptSelect.addEventListener("change", () => {
  // Get the selected Dept.
  console.log(deptSelect.value);
});

const countrySelect = document.getElementById("country");

require(".env").config();

const apiKey = process.env.API_KEY;
var headers = new Headers();
headers.append("X-CSCAPI-KEY", `${apiKey}`);
console.log(apiKey);

var requestOptions = {
  method: "GET",
  headers: headers,
  redirect: "follow",
};

fetch("https://api.countrystatecity.in/v1/countries", requestOptions)
  .then((response) => response.json())
  .then((result) => {
    const json = JSON.stringify(result);
    const data = JSON.parse(json);

    data.forEach((country) => {
      const option = document.createElement("option");
      option.text = country.name;
      option.value = country.iso2;
      option.key = country.id;
      console.log(country);
      countrySelect.appendChild(option);
    });
  })
  .catch((error) => console.log("error", error));

console.log(countrySelect.value);
const stateSelect = document.getElementById("state");

countrySelect.addEventListener("change", () => {
  stateSelect.innerHTML = "";
  lgaSelect.innerHTML = "";
  var requestOptions = {
    method: "GET",
    headers: headers,
    redirect: "follow",
  };

  fetch(
    `https://api.countrystatecity.in/v1/countries/${countrySelect.value}/states`,
    requestOptions
  )
    .then((response) => response.json())
    .then((result) => {
      const json = JSON.stringify(result);
      const data = JSON.parse(json);

      data.forEach((state) => {
        const option = document.createElement("option");
        option.text = state.name;
        option.value = state.iso2;
        option.key = state.id;
        console.log(state);
        stateSelect.appendChild(option);
      });
    })
    .catch((error) => console.log("error", error));
});

const lgaSelect = document.getElementById("lga");

stateSelect.addEventListener("change", () => {
  lgaSelect.innerHTML = "";
  var requestOptions = {
    method: "GET",
    headers: headers,
    redirect: "follow",
  };

  fetch(
    `https://api.countrystatecity.in/v1/countries/${countrySelect.value}/states/${stateSelect.value}/cities`,
    requestOptions
  )
    .then((response) => response.json())
    .then((result) => {
      const json = JSON.stringify(result);
      const data = JSON.parse(json);

      data.forEach((lga) => {
        const option = document.createElement("option");
        option.text = lga.name;
        option.value = lga.name;
        option.key = lga.id;
        console.log(lga);
        lgaSelect.appendChild(option);
      });
    })
    .catch((error) => console.log("error", error));
});

const signupForm = document.getElementById("signupForm");
signupForm.addEventListener("submit", (event) => {
  event.preventDefault();
  const firstName = document.getElementById("firstName").value;
  const middleName = document.getElementById("middleName").value;
  const lastName = document.getElementById("lastName").value;
  const age = document.getElementById("age").value;
  const phoneNumber = document.getElementById("phoneNumber").value;
  const email = document.getElementById("email").value;
  const gender = document.getElementById("gender").value;
  const country = document.getElementById("country").value;
  const state = document.getElementById("state").value;
  const lga = document.getElementById("lga").value;
  const faculty = document.getElementById("faculty").value;
  const department = document.getElementById("department").value;
  const password = document.getElementById("password").value;
  const data = {
    firstName,
    middleName,
    lastName,
    age,
    phoneNumber,
    email,
    gender,
    country,
    state,
    lga,
    faculty,
    department,
    password,
  };
  fetch("api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (response.ok) {
        Swal.fire({
          icon: "success",
          title: "Sign-up successful",
          text: "Subsequently check if you have been granted Admission. If so you can now log in with your new account",
          confirmButtonText: "OK",
        }).then(() => {
          // redirect to login page or other appropriate page
          window.location.href = "signIn/index.html";
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Sign-up failed",
          text: "Please try again later",
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
