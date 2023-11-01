document.addEventListener('DOMContentLoaded', function () {

// スクロール位置を記録
const listElement = document.getElementById('List');
let tournamentElement = document.getElementById('tournament');

// 'tournamentElement' が存在しない場合に代替の要素を取得
if (!tournamentElement) {
  tournamentElement = document.getElementById('RoundRobin');
}

listElement.addEventListener('scroll', () => {
  localStorage.setItem('listScrollPosition', listElement.scrollTop);
});

tournamentElement.addEventListener('scroll', () => {
  localStorage.setItem('tournamentScrollPosition', tournamentElement.scrollTop);
});

// ページが読み込まれたときに保存されたスクロール位置を復元
window.addEventListener('load', () => {
  const listScrollPosition = localStorage.getItem('listScrollPosition');
  if (listScrollPosition) {
    listElement.scrollTop = listScrollPosition;
  }

  const tournamentScrollPosition = localStorage.getItem('tournamentScrollPosition');
  if (tournamentScrollPosition) {
    tournamentElement.scrollTop = tournamentScrollPosition;
  }
});


  // ボタンクリック時の共通処理
  function handleButtonClick(value, confirmationMessage) {
    // 確認メッセージを表示
    if (confirm(confirmationMessage)) {
      // パスワード入力フィールドを表示
      document.getElementById('passwordInputContainer').style.display = 'block';
      document.getElementById('back').style.display = 'block';

      // パスワード確認ボタンのクリック処理
      document.getElementById('confirmPassword').addEventListener('click', function() {
        var passwordInput = document.getElementById('password');
        var password = passwordInput.value;

        if (password) {
          // パスワードが入力された場合、フォームに追加して送信
          var form = document.getElementById('PublishStatus');
          var passwordInput = document.createElement('input');
          passwordInput.type = 'hidden';
          passwordInput.name = 'user_password';
          passwordInput.value = password;
          form.appendChild(passwordInput);

          // ステータス値を設定
          var statusInput = document.getElementById('status');
          statusInput.value = value;

          // フォームを送信
          // form.submit();
        }
      });

      // 'back'をクリックしたときの処理
      document.getElementById('back').addEventListener('click', function() {
        // 'back'と 'passwordInputContainer' を非表示にする
        document.getElementById('back').style.display = 'none';
        document.getElementById('passwordInputContainer').style.display = 'none';
      });
    }
  }

  // ボタン要素を取得
  const deleteButton = document.getElementById('delete');
  const publishButton = document.getElementById('publish');
  const draftButton = document.getElementById('draft');

  // ボタンが存在する場合のみ処理を設定
  if (deleteButton) {
    deleteButton.addEventListener('click', function (event) {
      event.preventDefault();
      handleButtonClick(9999, '本当にドローを削除しますか?');
    });
  }

  if (publishButton) {
    publishButton.addEventListener('click', function (event) {
      event.preventDefault();
      handleButtonClick(1, 'ドローを公開しますか?');
    });
  }

  if (draftButton) {
    draftButton.addEventListener('click', function (event) {
      event.preventDefault();
      handleButtonClick(0, 'ドローを非公開にしますか?');
    });
  }

});