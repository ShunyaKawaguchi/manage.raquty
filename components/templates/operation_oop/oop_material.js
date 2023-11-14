document.addEventListener("DOMContentLoaded", function() {
    // すべてのresult要素を取得
    var resultElements = document.querySelectorAll(".result");

    // すべてのbackボタン要素を取得
    var backButtons = document.querySelectorAll(".back");

    // result要素ごとにクリックイベントリスナーを追加
    resultElements.forEach(function(resultElement) {
        resultElement.addEventListener("click", toggleSections);
    });

    // backボタンごとにクリックイベントリスナーを追加
    backButtons.forEach(function(backButton) {
        backButton.addEventListener("click", toggleSections);
    });

    // クリックされた要素のIDを取得し、menu_sectionとplayer_sectionの表示状態をトグルする関数
    function toggleSections() {
        var id;
        if (this.classList.contains("back")) {
            // クリックされた要素がbackボタンの場合
            id = this.getAttribute("data-target-id");
        } else {
            // クリックされた要素がresult要素の場合
            id = this.getAttribute("id");
        }

        var menuSection = document.querySelector(".menu_section[id='" + id + "']");
        var playerSection = document.querySelector(".player_section[id='" + id + "']");

        if (menuSection) {
            if (menuSection.style.display === "block") {
                menuSection.style.display = "none";
                playerSection.style.display = "block";
            } else {
                menuSection.style.display = "block";
                playerSection.style.display = "none";
            }
        }
    }
});


$(document).ready(function() {
    // ラジオボタンの初期状態をチェック
    $('input[type="radio"]').each(function() {
        var id = $(this).attr('id');
        updateWinClass(id, $(this).is(':checked'));
    });

    // ラジオボタンの変更時に処理を実行
    $('input[type="radio"]').change(function() {
        var id = $(this).attr('id');
        updateWinClass(id, $(this).is(':checked'));
    });

    // winクラスを追加または削除する関数
    function updateWinClass(id, checked) {
        // 全てのラップ要素から "win" クラスを一旦削除
        $('.wrap').removeClass('win');
        
        if (checked) {
            $('#' + id).addClass('win');
        }
    }
});


document.addEventListener("DOMContentLoaded", function() {
// ボタンとモーダルの要素を取得
const addMatchButton = document.getElementById('addMatch');
const back = document.getElementById('backScreen');
const addMatchModal = document.getElementById('addMatch_Modal');

// マッチ追加ボタンがクリックされたときの処理
if (addMatchButton) {
    addMatchButton.addEventListener('click', showAddMatchModal);
}

// 'back'要素がクリックされたときの処理
if (back) {
    back.addEventListener('click', hideAddMatchModal);
}

// モーダルを表示する関数
function showAddMatchModal() {
    addMatchModal.style.display = 'block';
    back.style.display = 'block';
}

// モーダルを非表示にする関数
function hideAddMatchModal() {
    back.style.display = 'none';
    addMatchModal.style.display = 'none';
}
});

document.addEventListener('DOMContentLoaded', function() {
 // ラジオボタンが変更されたときのイベントハンドラ
 document.querySelectorAll('input[type="radio"][name="type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      // シングルスが選択された場合
      if (this.value === "1") {
        document.getElementById('player_1').style.display = 'block';
        document.getElementById('player_3').style.display = 'block';
        document.getElementById('player_2').style.display = 'none';
        document.getElementById('player_4').style.display = 'none';
      } else {
        // ダブルスまたはその他が選択された場合
        document.getElementById('player_1').style.display = 'block';
        document.getElementById('player_3').style.display = 'block';
        document.getElementById('player_2').style.display = 'block';
        document.getElementById('player_4').style.display = 'block';
      }
    });
  });
});

 // ページが読み込まれた後に実行されるコード
 document.addEventListener('DOMContentLoaded', function() {
    // フォーム要素を取得
    const forms = document.querySelectorAll('form');

    // 各フォームに対する処理
    forms.forEach(function(form) {
        const tiebreakCheckbox = form.querySelector('input[name="tiebreak"]');
        const submitButton = form.querySelector('input[type="submit"]');

        // フォームの送信ボタンがクリックされたときの処理
        submitButton.addEventListener('click', function(event) {
            if (tiebreakCheckbox.checked) {
                // チェックボックスがチェックされている場合
                event.preventDefault();
                const inputNumber = prompt('数字を入力してください:');
                
            }
            // チェックボックスがチェックされていない場合、フォームが通常通り送信される
        });
    });
});

 // ページが読み込まれた後に実行されるコード
 document.addEventListener('DOMContentLoaded', function() {
    // すべてのフォーム要素を取得
    const forms = document.querySelectorAll('form');

    // 各フォームに対する処理
    forms.forEach(function(form) {
        // フォームのIDを取得
        const formId = form.getAttribute('id');
        if (formId && formId.startsWith('Result_')) {
            form.addEventListener('submit', function(event) {
                // フォーム内の送信ボタンを取得
                const submitButton = form.querySelector('input[type="submit"]');
                
                // 送信ボタンの名前（name）を取得
                const submitButtonName = submitButton.getAttribute('name');
                
                if (submitButtonName === 'back') {
                    // "back" ボタンが押された場合はアラートなしでフォームを送信
                    return;
                }
                
                // "result_submit" ボタンが押された場合はアラートを表示
                const confirmation = confirm('結果を確定します。この操作は取り消せません。');
                if (!confirmation) {
                    // 確認ダイアログでキャンセルが選択された場合、フォームの送信を中止
                    event.preventDefault();
                }
            });
        }
    });
});