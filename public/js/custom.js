var elements = $(".voyager.users #dataTable tbody tr td:nth-child(2) span");

elements.each(function () {
    var value = $(this).html();
    if (value == "1") {
        // $(this).parent().closest('tr').css("display", "none");
    }
});


// get the email from the header
var email = $(".navbar .dropdown.profile .dropdown-menu h6").text();

// loop through the table rows and compare the email
$("#dataTable tbody tr").each(function() {
  var rowEmail = $(this).find("td:nth-child(4) div").text();
  if (rowEmail == email) {
    // hide the delete element using parent
    $(this).find('.delete').hide();
  }
});
