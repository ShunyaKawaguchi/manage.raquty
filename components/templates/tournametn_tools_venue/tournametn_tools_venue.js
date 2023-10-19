//フォームに情報を付け加えて送信
const UpdateButton = document.getElementById("Update");
const UpdateForm = document.getElementById("Update_Venue"); 

UpdateButton.addEventListener("click", function(event) {
    event.preventDefault();
    UpdateForm.method = "post";
    UpdateForm.action = "/components/templates/tournametn_tools_venue/update_data.php"; 
    UpdateForm.submit();
});