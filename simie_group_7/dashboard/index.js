document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = $("#calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    events: [
      {
        title: "Event 1",
        start: "2023-03-01",
      },
      {
        title: "Event 2",
        start: "2023-03-05",
        end: "2023-03-07",
      },
    ],
  });
  calendar.render();
});

//line
var ctx = document.getElementById("myChart").getContext("2d");
var myChart = new Chart(ctx, {
  type: "line",
  data: {
    labels: [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday",
    ],
    datasets: [
      {
        label: "work load",
        data: [2, 9, 3, 17, 6, 3, 7],
        backgroundColor: "#f5a425",
      },
      {
        label: "free hours",
        data: [2, 2, 5, 5, 2, 1, 10],
        backgroundColor: "#f5f225",
      },
    ],
  },
});

var timer = setTimeout(function () {}, 60000);
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keypress", resetTimer);

function resetTimer() {
  clearTimeout(timer);
  timer = setTimeout(function () {
    window.location.href = "../signIn/index.html"; // Redirect user to logout page
  }, 60000);
}
