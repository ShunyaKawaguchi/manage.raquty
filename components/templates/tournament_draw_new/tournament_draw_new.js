const NewSubmitButton = document.getElementById("NewSubmit");
const NewForm = document.getElementById("NewChildEvent");

NewSubmitButton.addEventListener("click", function() {
    NewForm.action = "/components/templates/tournament_draw_new/insert_data.php";
    NewForm.submit();
});
