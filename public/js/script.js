$(document).ready(function () {
  // URL of the REST API
  const apiUrl = "/api/search";

  // Handle form submission
  $("#api-search").on("submit", function (e) {
    e.preventDefault();

    // Collect form data
    const formData = {
      query: $("#query").val(),
    };

    // Send POST request to the API
    $.ajax({
      url: apiUrl,
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(formData),
      success: function (data) {
        if (data && data.length > 0) {
          // Get keys from the first object to generate headers
          const headers = ["author_name", "name"];

          // Populate table rows
          const tbody = $("#data-table tbody");
          tbody.empty();
          let count = 0;
          data.forEach((item) => {
            let row = '<tr class="hidden-row">';
            headers.forEach((header) => {
              row += `<td>${item[header] ?? ""}</td>`;
            });
            row += "</tr>";
            const newRow = $(row);
            tbody.append(newRow);
            setTimeout(() => {
              newRow.removeClass("hidden-row").addClass("visible-row");
            }, count * 100);
            count++;
          });
        } else {
          $("#data-table").after("<p>No data available</p>");
        }
      },
      error: function () {
        $("#data-table").after("<p>Failed to fetch data from the API.</p>");
      },
    });
  });
});
