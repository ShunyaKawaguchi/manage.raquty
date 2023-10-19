const StatusForm = document.getElementById("Change_Status"); 
const ChangeButton = document.getElementById("Change");

ChangeButton.addEventListener("click", function(event) {
    event.preventDefault();
    StatusForm.action = "/components/templates/tournament_edit/update_data.php"; 
    StatusForm.submit();
});
