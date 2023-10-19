const NewEventSubmitButton = document.getElementById("Create_New_Event");
const NewEventForm = document.getElementById("NewEvenet_Form"); 

//フォームに情報を付け加えて送信(新規大会)
NewEventSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    NewEventForm.method = 'post'
    NewEventForm.action = "/components/templates/tournament_event_new/insert_data.php"; 
    NewEventForm.submit();
});