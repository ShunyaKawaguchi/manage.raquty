const UpdateSubmitButton = document.getElementById("Update_data");
const DeleteSubmitButton = document.getElementById("Delete_data");
const EventForm = document.getElementById("Evenet_Form"); 
const DeleteForm = document.getElementById("delete"); 

//フォームに情報を付け加えて送信(更新)
UpdateSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    EventForm.method = 'post'
    EventForm.action = "/components/templates/tournament_event_edit/update_data.php"; 
    EventForm.submit();
});

//フォームに情報を付け加えて送信(削除)
DeleteSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    DeleteForm.method = 'get'
    DeleteForm.action = homeUrl('Tournament/View/Event/Delete');
    DeleteForm.submit();
});