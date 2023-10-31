$(function () {
    // ...（以前のコードをここに配置）
    // 現在のURLからクエリ文字列を取得
    const queryString = window.location.search;

    // APIエンドポイント
    const origin = window.location.origin;
    const getDynamicData = origin+'/components/templates/operation_tournament/oop_dynamic_data.php'+queryString;
    const getStaticData = origin+'/components/templates/operation_tournament/oop_static_data.php'+queryString;
  
    

    function fetchStaticData() {
      // 非同期通信を行うコードを記述
      // ここでAPIリクエストなどを行う
      // 例: fetch APIを使用した場合
      fetch(getStaticData)
        .then(response => response.json())
        .then(data => {
          const event = new CustomEvent('staticDataUpdated',  {detail: data});
          document.dispatchEvent(event);
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    }
    function fetchDynamicData() {
      // 非同期通信を行うコードを記述
      // ここでAPIリクエストなどを行う
      // 例: fetch APIを使用した場合
      fetch(getDynamicData)
        .then(response => response.json())
        .then(data => {
          const event = new CustomEvent('dynamicDataUpdated',  {detail: data});
          document.dispatchEvent(event);
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    }
    
    // 初回の静的データ取得
    fetchStaticData();
    // 初回の動的データ取得
    fetchDynamicData();
    // 50000ミリ秒(5min)ごとに静的データを取得する
    setInterval(fetchStaticData, 50000);
    // 10000ミリ秒(10sec)ごとに動的データを取得する
    setInterval(fetchDynamicData, 10000);
  });
  
  