//フォームに情報を付け加えて送信
const DeleteSubmitButton = document.getElementById("Delete");
const DeleteForm = document.getElementById("Delete_Event_Form"); 

DeleteSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    DeleteForm.method = "post";
    DeleteForm.action = "/components/templates/tournament_delete/delete_data.php"; 
    DeleteForm.submit();
});