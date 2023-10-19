// document.addEventListener("DOMContentLoaded", function() {
//     $(document).ready(function() {
//         // 初期状態ではすべてのテーブルを非表示にし、アニメーション速度を0.3秒に設定
//         $(".Entry_Listing").hide();

//         // ▼/▲がクリックされたときの処理
//         $(".Display_List").click(function() {
//             // クリックされた要素の親要素内にある ".Entry_Listing" をトグルするアニメーション
//             var closestEntryListing = $(this).closest(".Entry_List").find(".Entry_Listing");
//             closestEntryListing.fadeToggle(300);
        
//             // ▼/▲を切り替える
//             $(this).text(function(_, text) {
//                 return text === '▼' ? '▲' : '▼';
//             });
//         });
//     });
// });

