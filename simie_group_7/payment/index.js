const cardNumberInput = document.getElementById("card-number");

cardNumberInput.addEventListener("input", function (e) {
  const input = e.target.value.replace(/\D/g, "");
  const formattedInput = input.replace(/(\d{4})/g, "$1 ");
  e.target.value = formattedInput.trim();
});

const expiryDateInput = document.getElementById("expiry-date");

expiryDateInput.addEventListener("input", function (e) {
  const input = e.target.value.replace(/\D/g, "").substring(0, 6);
  const formattedInput = input.replace(/(\d{2})(\d{4})/, "$1 / $2");
  e.target.value = formattedInput;
});

const cvvNumberInput = document.getElementById("cvv");

cvvNumberInput.addEventListener("input", function (e) {
  const input = e.target.value.replace(/\D/g, "");
  const formattedInput = input.replace(/(\d{4})/g, "$1 ");
  e.target.value = formattedInput.trim();
});

function showCardLogo() {
  const cardNumberInput = document.getElementById("card-number");
  const cardLogoContainer = document.querySelector(".card-logo");
  const visaLogo = document.getElementById("visa-logo");
  const mastercardLogo = document.getElementById("mastercard-logo");
  const verveLogo = document.getElementById("verve-logo");

  const cardNumber = cardNumberInput.value;
  const visaPattern = /^4/;
  const mastercardPattern = /^5[1-5]/;
  const vervePattern =
    /^506[0-9]{2}|^507[0-8][0-9]|^50[6-9][0-9]{3}|^5[1-5][0-9]{4}|^222[1-9]|^22[3-9][0-9]|^2[3-6][0-9]{2}|^27[01][0-9]|^2720/;

  if (visaPattern.test(cardNumber)) {
    cardLogoContainer.style.display = "block";
    visaLogo.style.display = "block";
    mastercardLogo.style.display = "none";
    verveLogo.style.display = "none";
  } else if (mastercardPattern.test(cardNumber)) {
    cardLogoContainer.style.display = "block";
    mastercardLogo.style.display = "block";
    visaLogo.style.display = "none";
    verveLogo.style.display = "none";
  } else if (vervePattern.test(cardNumber)) {
    cardLogoContainer.style.display = "block";
    verveLogo.style.display = "block";
    visaLogo.style.display = "none";
    mastercardLogo.style.display = "none";
  } else {
    cardLogoContainer.style.display = "none";
    if (cardNumber.length > 4) {
      Swal.fire({
        icon: "error",
        title: "Card Type not Supported",
        text: " Please use any of the listed above Card type.",
        confirmButtonText: "OK",
      }).then(() => (cardNumberInput.value = ""));
    }
  }
}

const cardHolderName = document.getElementById("card-holder-name");

const paymentButton = document.getElementById("paymentSubmitted");
paymentButton.addEventListener("click", (event) => {
  event.preventDefault();
  if (
    cardNumberInput.value != "" &&
    cardHolderName.value != "" &&
    expiryDateInput.value != "" &&
    cvvNumberInput.value != ""
  ) {
    const loader = document.createElement("div");
    loader.classList.add("loader");
    document.body.appendChild(loader);

    // Hide the loader after 6 seconds
    setTimeout(() => {
      loader.remove();
      Swal.fire({
        icon: "success",
        title: "Payment Successful",
        text: "Payment received securely",
        confirmButtonText: "OK",
      }).then(() => location.reload());
    }, 6000);
  } else {
    Swal.fire({
      icon: "error",
      title: "Payment Failed",
      text: "Please fill all fields to initiate payment",
      confirmButtonText: "OK",
    }).then(() => location.reload());
  }
});
