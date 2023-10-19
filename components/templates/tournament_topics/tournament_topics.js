const NewTopicsForm = document.getElementById("New_Topics_Form"); 
const NewTopicsButton = document.getElementById("New_Topics");

NewTopicsButton.addEventListener("click", function(event) {
    event.preventDefault();

    // ユーザーが「Yes」を選択した場合の処理
    const isConfirmed = confirm('新規トピックスを作成しますか？');
    if (isConfirmed) {
        NewTopicsForm.action = "/components/templates/tournament_topics/insert_data.php"; 
        NewTopicsForm.submit();
    }
});
