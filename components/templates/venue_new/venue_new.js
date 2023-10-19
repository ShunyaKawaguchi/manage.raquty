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
  var insertForm = document.getElementById('Add_Venue_Form');
  var submitButtonInsert = document.getElementById('Insert');

  function updatePreview() {
    var iframeCode = textarea.value;
    var modifiedIframeCode = modifyIframeCode(iframeCode);
    preview.innerHTML = modifiedIframeCode;

    // Googleマップの埋め込みコードが空であればsubmitボタンを無効にする
    if (iframeCode.trim() === '') {
      submitButton.disabled = true;
      venueNotice.textContent = 'Googleマップの埋め込みコードを入力してください.';
    } else {
      // Googleマップのiframeが存在しない場合もエラーメッセージを表示
      if (!containsGoogleMapIframe(modifiedIframeCode)) {
        submitButton.disabled = true;
        venueNotice.textContent = 'Googleマップのiframeが含まれていません.';
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

  submitButtonInsert.addEventListener('click', function(event) {
    event.preventDefault();
    var numberOfCourtField = document.getElementById('number_of_court');
    var numberOfCourt = parseInt(numberOfCourtField.value);

    if (isNaN(numberOfCourt) || numberOfCourt < 1) {
      alert('コートの数は1以上の整数を入力してください.');
    } else {
      insertForm.action = '/components/templates/venue_new/insert_data.php';
      insertForm.submit();
    }
  });
});
