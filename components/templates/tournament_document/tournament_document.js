function checkFileSize(input) {
    if (input.files[0].size > 5 * 1024 * 1024) {
        alert("ファイルサイズは5MB以下にしてください。");
        input.value = null;
    }
}

// ファイルが選択されたときの処理
function checkFileSize(inputElement) {
    // 対応するアップロードボタンのIDを取得
    const fileId = inputElement.id.replace("fileInput", "");
    const uploadButton = document.getElementById(`Upload${fileId}`);
  
    if (inputElement.files.length > 0) {
      // ファイルが選択された場合、アップロードボタンを有効にする
      uploadButton.disabled = false;
    } else {
      // ファイルが選択されていない場合、アップロードボタンを無効にする
      uploadButton.disabled = true;
    }
  }

  
const DocumentForm = document.getElementById("Document"); 
const UploadButton = document.getElementById("Upload1");
const Upload2Button = document.getElementById("Upload2");
const DeleteButton = document.getElementById("Delete1");
const Delete2Button = document.getElementById("Delete2");

UploadButton.addEventListener("click", function(event) {
    event.preventDefault();
    DocumentForm.action = "/components/templates/tournament_document/upload_outline.php"; 
    DocumentForm.submit();
});

Upload2Button.addEventListener("click", function(event) {
    event.preventDefault();
    DocumentForm.action = "/components/templates/tournament_document/upload_timetable.php"; 
    DocumentForm.submit();
});

// 削除ボタン1のクリックイベント
DeleteButton.addEventListener("click", function(event) {
    event.preventDefault();
    // 確認アラートを表示
    const confirmDelete = confirm("大会要綱を削除しますか？");
    if (confirmDelete) {
        // OKを選択した場合、フォームを送信
        DocumentForm.action = "/components/templates/tournament_document/delete_outline.php";
        DocumentForm.submit();
    }
});

// 削除ボタン2のクリックイベント
Delete2Button.addEventListener("click", function(event) {
    event.preventDefault();
    // 確認アラートを表示
    const confirmDelete = confirm("日程表を削除しますか？");
    if (confirmDelete) {
        // OKを選択した場合、フォームを送信
        DocumentForm.action = "/components/templates/tournament_document/delete_timetable.php";
        DocumentForm.submit();
    }
});