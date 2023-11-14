function displaySelectAgain(){
    const select_again = document.getElementById('select-again');
    select_again.style.display = 'block';
    const selectAlert = document.getElementsByClassName('court-select-alert');
    selectAlert[0].style.display = '';
}
function displayGame(){
    const search = document.getElementById('search');
    const value = search.value;
    const value2 = search.options[search.selectedIndex].text;
    const game_card = document.getElementsByClassName('game_card');
    //もしvalueがallなら
    if(value == 'all'){
        for(let i = 0; i < game_card.length; i++){
            game_card[i].style.display = 'block';
        }
    }else{
        for(let i = 0; i < game_card.length; i++){
            if(game_card[i].classList.contains('event-'+value)){
                game_card[i].style.display = 'block';
            }else{
                game_card[i].style.display = 'none';
            }
        }
    }
    const selector_value = document.getElementsByClassName('selector-value');
    for(let i = 0; i < selector_value.length; i++){
        selector_value[i].value = value2;
    }
}
function gameFocus(game_id){
    //まず、game_cardのスタイルをすべて変更する
    const game_card = document.getElementsByClassName('game_card');
    for(let i = 0; i < game_card.length; i++){
        game_card[i].style.display = 'none';
    }
    const game = document.getElementById('game-'+game_id);
    console.log(game);
    //gameのスタイルを変更するborder:3px solid #000033;
    game.style.display = 'block';
    game.style.border  = '3px solid #000033';
    //selectorを非表示にする
    const selector = document.getElementById('search');
    selector.style.display = 'none';
}

// suggestDivを取得
const suggestDiv = document.getElementById('suggest');

    
    
function selectCourt(court_num){
    document.getElementById("court-"+court_num).click();
}
function selectSuggest(game_id){
    document.getElementById("submit-"+game_id).click();
}
function getSelectorValue(){
    const selector = document.getElementById('search');
    const value = selector.value;
    
}
function createCourtSelector(){
    //何もしない
}

document.addEventListener('DOMContentLoaded', function() {
    //classがgame_cardかつ、child_event_nameが10のものを非表示にする
const search = document.getElementById('search');
    search.addEventListener('change', function(){
        const value = search.value;
        const value2 = search.options[search.selectedIndex].text;
        const game_card = document.getElementsByClassName('game_card');
        //もしvalueがallなら
        if(value == 'all'){
            for(let i = 0; i < game_card.length; i++){
                game_card[i].style.display = 'block';
            }
        }else{
            for(let i = 0; i < game_card.length; i++){
                if(game_card[i].classList.contains('event-'+value)){
                    game_card[i].style.display = 'block';
                }else{
                    game_card[i].style.display = 'none';
                }
            }
        }
        const selector_value = document.getElementsByClassName('selector-value');
        for(let i = 0; i < selector_value.length; i++){
            selector_value[i].value = value2;
        }
    });
});