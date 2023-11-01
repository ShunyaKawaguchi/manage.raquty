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