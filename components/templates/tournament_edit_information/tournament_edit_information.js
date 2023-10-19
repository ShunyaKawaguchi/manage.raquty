document.addEventListener("DOMContentLoaded", function () {
    const calendarBody = document.getElementById("calendarBody");
    const currentMonthDisplay = document.getElementById("currentMonth");
    const prevMonthButton = document.getElementById("prevMonth");
    const nextMonthButton = document.getElementById("nextMonth");
    const dateInput = document.getElementById("date");

    // input要素に設定された日付を取得
    let defaultDates = dateInput.value.split(",").map(date => date.trim());
    let currentDate;

    // input要素に日付が設定されている場合はその日付を初期値として使用
    if (defaultDates.length > 0 && defaultDates[0] !== "") {
        currentDate = new Date(defaultDates[0]);
    } else {
        // 日付が設定されていない場合は現在の月を初期値として使用
        currentDate = new Date();
        defaultDates = [];
    }

    let selectedDates = defaultDates;

    function renderCalendar() {
        // カレンダーの日付セルをクリア
        calendarBody.innerHTML = "";

        // 現在の月の最初の日を取得
        const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

        // 現在の月の最終日を取得
        const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

        // 現在の月の日数を取得
        const daysInMonth = lastDayOfMonth.getDate();

        // 現在の月の1日が何曜日かを取得 (0 = 日曜日, 1 = 月曜日, ...)
        const firstDayOfWeek = firstDayOfMonth.getDay();

        // カレンダーの最初の週を作成
        let week = document.createElement("tr");

        // 前月の最終日を取得
        const lastDayOfPrevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
        const daysInPrevMonth = lastDayOfPrevMonth.getDate();

        // 前月の日付セルを埋める
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayCell = document.createElement("td");
            dayCell.textContent = daysInPrevMonth - i;
            dayCell.classList.add("prev-month");
            week.appendChild(dayCell);
        }

        // 現在の月の日付セルを埋める
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement("td");
            dayCell.textContent = day;

            const selectedDateStr = `${currentDate.getFullYear()}/${currentDate.getMonth() + 1}/${day}`;
            if (selectedDates.includes(selectedDateStr)) {
                dayCell.classList.add("selected");
            }

            // 日付をクリックしたときの処理
            dayCell.addEventListener("click", function () {
                const selectedDate = selectedDateStr;
                if (selectedDates.includes(selectedDate)) {
                    // 既に選択済みの場合は選択を解除
                    selectedDates = selectedDates.filter(date => date !== selectedDate);
                    dayCell.classList.remove("selected");
                } else {
                    // 新たに選択された日を追加
                    selectedDates.push(selectedDate);
                    dayCell.classList.add("selected");
                }

                // 選択した日付を<input>要素に表示
                dateInput.value = selectedDates.join(",");
            });

            week.appendChild(dayCell);

            // 週の終わりになったら新しい週を作成
            if (week.children.length === 7) {
                calendarBody.appendChild(week);
                week = document.createElement("tr");
            }
        }

        // 残りの日付セルを埋める
        let nextMonthDay = 1;
        while (week.children.length < 7) {
            const dayCell = document.createElement("td");
            dayCell.textContent = nextMonthDay;
            dayCell.classList.add("next-month");
            week.appendChild(dayCell);
            nextMonthDay++;
        }

        // カレンダーの日付セルを追加
        calendarBody.appendChild(week);

        // カレンダーが描画された後に日付入力を更新
        updateDateInput();

        // 現在の月を表示
        currentMonthDisplay.textContent = `${currentDate.getFullYear()}年${currentDate.getMonth() + 1}月`;
    }

    function updateDateInput() {
        const dateArray = dateInput.value.split(",").map(date => date.trim()).filter(date => date !== "");
        dateArray.sort((a, b) => {
            // 日付の文字列を比較するために日付オブジェクトに変換
            const dateA = new Date(a);
            const dateB = new Date(b);
            return dateA - dateB;
        });

        // 選択されていない日付を削除する
        dateInput.value = dateArray.filter(date => selectedDates.includes(date)).join(",");
    }

    // 初期カレンダーを描画
    renderCalendar();

    // 前月への移動
    prevMonthButton.addEventListener("click", function () {
        event.preventDefault();
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        renderCalendar(); // renderCalendar() を呼び出し、日付入力を更新
    });

    // 次月への移動
    nextMonthButton.addEventListener("click", function () {
        event.preventDefault();
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        renderCalendar(); // renderCalendar() を呼び出し、日付入力を更新
    });

    // テキストボックスの値が変更されたときに日付をソートして更新
    dateInput.addEventListener("input", function () {
        updateDateInput();
    });
});


//フォームに情報を付け加えて送信(更新)
const UpdateForm = document.getElementById("Update_Form"); 
const UpdateSubmitButton = document.getElementById("Update");

UpdateSubmitButton.addEventListener("click", function(event) {
    event.preventDefault();
    UpdateForm.action = "/components/templates/tournament_edit_information/update_data.php"; 
    UpdateForm.submit();
});