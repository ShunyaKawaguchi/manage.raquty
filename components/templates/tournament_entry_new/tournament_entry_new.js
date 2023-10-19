const InsertSubmitButton = document.getElementById("Insert_data");
const InsertForm = document.getElementById("Insert_Form"); 
const requiredInputs = document.querySelectorAll('.required');

// フォームに情報を付け加えて送信(挿入)
InsertSubmitButton.addEventListener("click", function(event) {
    // デフォルトの動作を一時的に無効にする
    event.preventDefault();

    // 条件を満たしているかを確認
    let allInputsFilled = true;
    requiredInputs.forEach(input => {
        if (input.value.trim() === '') {
            allInputsFilled = false;
            // 必須項目が空の場合、フォームフィールドに赤い枠線を追加
            input.style.border = "1px solid red";
        } else {
            // 必須項目が入力されている場合、赤い枠線を削除
            input.style.border = ""; // デフォルトのスタイルに戻す
        }
    });

    // 条件を満たしていればフォームを送信
    if (allInputsFilled) {
        InsertForm.action = "/components/templates/tournament_entry_new/insert_data.php"; 
        InsertForm.submit();
    } else {
        // 必須項目が空の場合、アラートメッセージを表示
        alert('選手名と所属は入力必須項目です。');
        // ここにフォーム送信が中止された場合の処理を追加してもよい
    }
});
