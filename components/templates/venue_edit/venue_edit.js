function modifyIframeCode(iframeCode) {
  iframeCode = iframeCode.replace(/<iframe(.*?)>/, '<iframe$1 id="map-iframe">');
  iframeCode = iframeCode.replace(/(width|height)=".*?"/g, '');
  return iframeCode;
}

window.addEventListener('DOMContentLoaded', function () {
  var textarea = document.getElementById('venue_map');
  var preview = document.getElementById('preview');
  var submitButton = document.querySelector('input[type="submit"]');
  var venueNotice = document.getElementById('venue_notice');

  updatePreview = () => {
    var iframeCode = textarea.value;
    var modifiedIframeCode = modifyIframeCode(iframeCode);
    preview.innerHTML = modifiedIframeCode;

    // Googleマップの埋め込みコードが空であればsubmitボタンを無効にする
    if (iframeCode.trim() === '') {
      submitButton.disabled = true;
      venueNotice.textContent = 'Googleマップの埋め込みコードを入力してください。';
    } else {
      // Googleマップのiframeが存在しない場合もエラーメッセージを表示
      if (!containsGoogleMapIframe(modifiedIframeCode)) {
        submitButton.disabled = true;
        venueNotice.textContent = 'Googleマップのiframeが含まれていません。';
      } else {
        submitButton.disabled = false;
        venueNotice.textContent = ''; // メッセージをクリア
      }
    }
  }

  updatePreview();

  textarea.addEventListener('input', updatePreview);

  // Googleマップのiframeが含まれているかを検証する関数
  function containsGoogleMapIframe(html) {
    var parser = new DOMParser();
    var doc = parser.parseFromString(html, 'text/html');
    var iframe = doc.querySelector('iframe[src*="google.com/maps"]');
    return !!iframe;
  }
});

//フォームに情報を付け加えて送信(更新)
const insertForm = document.getElementById("Update_Venue_Form"); 
const UpdateSubmitButton = document.getElementById("Update");

UpdateSubmitButton.addEventListener("click", function(event) {
  // コート面数が1以上になっているか確認
  var numberField = document.getElementById('number_of_court');
  var numberValue = parseInt(numberField.value);

  if (isNaN(numberValue) || numberValue < 1) {
      alert('コートの面数は1以上の数字を入力してください。');
      event.preventDefault();
  } else {
      // フォームに情報を付加して送信
      insertForm.action = "/components/templates/venue_edit/update_data.php"; 
      insertForm.submit();
  }
});

//フォームに情報を付け加えて送信(削除)
const DeleteSubmitButton = document.getElementById("Delete");

DeleteSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    insertForm.action = homeUrl('Tournament/Venue/Delete');
    insertForm.submit();
});

