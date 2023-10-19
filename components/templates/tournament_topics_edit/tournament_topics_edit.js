const TopicsForm = document.getElementById("Topics_Edit"); 
const UpdateButton = document.getElementById("Update");
const UpdateStatusButton = document.getElementById("Update_Status");
const DeleteButton = document.getElementById("Delete");

UpdateButton.addEventListener("click", function(event) {
    event.preventDefault();
    TopicsForm.action = "/components/templates/tournament_topics_edit/update_content_data.php"; 
    TopicsForm.submit();
});

UpdateStatusButton.addEventListener("click" , function(event){
    event.preventDefault();
    TopicsForm.action = "/components/templates/tournament_topics_edit/update_status_data.php";
    TopicsForm.submit();
});

DeleteButton.addEventListener("click" , function(event){
    event.preventDefault();
    TopicsForm.action = "/components/templates/tournament_topics_edit/delete_topics.php";
    TopicsForm.submit();
});