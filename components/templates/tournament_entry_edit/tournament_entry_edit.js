const UpdateSubmitButton = document.getElementById("Update_data");
const UpdateForm = document.getElementById("Edit_Player"); 

//フォームに情報を付け加えて送信(更新)
UpdateSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    UpdateForm.method = 'post'
    UpdateForm.action = "/components/templates/tournament_entry_edit/update_data.php"; 
    UpdateForm.submit();
});