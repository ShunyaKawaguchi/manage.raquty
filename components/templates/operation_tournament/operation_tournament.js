let staticData = null;
let dynamicData = null;
let empty = false;
let playerList = [];
let gameList = [];
let maxLength = 0;
let currentTime = null;
let tournamentScale;
const lineLength = 100;
// URLからクエリ文字列（GETパラメータ）を取得
let queryString = window.location.search;

// URLSearchParamsオブジェクトを使用して、GETパラメータを解析
let urlParams = new URLSearchParams(queryString);

// 特定のGETパラメータを取得
let tournament_id = urlParams.get('tournament_id');
let event_id= urlParams.get('event_id');
let currentType;
const yyyymmdd = new Intl.DateTimeFormat(
  undefined,
  {
    year:   'numeric',
    month:  '2-digit',
    day:    '2-digit',
    hour:   '2-digit',
    minute: '2-digit',
    second: '2-digit'
  }
)
document.addEventListener('staticDataUpdated', function(event){
    staticData = event.detail;
    playerList.length = 0;
    //現在の時間を取得して、それをcurrentTimeに格納する
    currentTime = yyyymmdd.format(new Date());
    currentType = Object.values(staticData.get_event_type)[0].type;
    console.log(currentType);
    makeBase();
});
document.addEventListener('dynamicDataUpdated', function(event){
    dynamicData = event.detail;
    //現在の時間を取得して、それをcurrentTimeに格納する
    currentTime = yyyymmdd.format(new Date());
    console.log(dynamicData);
    resultSet();
});
//
const canvas = document.getElementById('tournament');
const ctx = canvas.getContext('2d');

//表示サイズを設定（cssにおけるピクセル数）
const size_w = 1000;
const size_h = innerHeight*2;

const scale = window.devicePixelRatio;
canvas.style.width = `${size_w}px`;
canvas.style.height = `${size_h}px`;
canvas.width = Math.floor(size_w * scale);
canvas.height = Math.floor(size_h * scale);

//css上のピクセル数を前提としているシステムに合わせる
ctx.scale(scale, scale);

//makeBase()は、エントリーリストの変更を取得したときに行う
function makeBase(){
  // console.log(data);
  const entryList = Object.values(staticData.get_entry_list);
  maxLength = entryList.length;
  if(maxLength == 0){
    console.log('空です');
    empty = true;
  }else{
    empty = false;
    console.log(maxLength);
    const drawIdArray = entryList.map(value => value.draw_id);
    const maxDrawId = Math.max(...drawIdArray);
    const maxNumberOfGame = Math.ceil(Math.log2(maxDrawId));//最小限の大きさの2のx乗を求める
    // const maxNumberOfGame = Math.ceil(Math.log2(maxLength));
    tournamentScale = Math.pow(2, maxNumberOfGame);//32トーナメントなど、xトーナメントというふうにトーナメントの規模感を算出する
    playerList.length = tournamentScale;
    Object.keys(entryList).forEach(key => {
      const value = entryList[key];
      if(currentType == 'シングルス'){
        const draw_id = value.draw_id;
        const user_belonging = value.user1_belonging;
        const user_name = value.user1_name;
        const player = new SinglePlayer(draw_id, user_belonging, user_name);
        playerList[draw_id] = player;
        console.log(playerList[draw_id]);
      }else if(currentType == 'ダブルス'){
        const draw_id = value.draw_id;
        const user_belonging1 = value.user1_belonging;
        const user_name1 = value.user1_name;
        const user_belonging2 = value.user2_belonging;
        const user_name2 = value.user2_name;
        const player = new DoublesPlayer(draw_id, user_belonging1, user_name1, user_belonging2, user_name2);
        playerList[draw_id] = player;
      }
    })
    //byeの処理
    for (let i = 1; i <= tournamentScale; i++) {
      if(playerList[i]==undefined){
        if(currentType == 'シングルス'){
          playerList[i] = new SinglePlayer(i, null, null);
        }else if(currentType == 'ダブルス'){
          playerList[i] = new DoublesPlayer(i, null, null, null, null);
        }
      }
    }
    //Gameクラスのインスタンスを作成する
    gameList.length = tournamentScale-1;
    let round = 0;
    let NumberOfGame = tournamentScale;
    for(let i = 1; i <= Math.log2(tournamentScale); i++){
      if(NumberOfGame == 1){ 
        break;
      }else{
        NumberOfGame = NumberOfGame/2;
        round += 1;
      }
      for(let j = 1; j <= NumberOfGame; j++){
        if(round == 1){
          const player1_id = j*2-1;
          const player2_id = j*2;
          const game = new Game();
          game.setPlayer1(player1_id);
          console.log(playerList[player1_id]);
          console.log(playerList[player2_id]);
          game.setPlayer2(player2_id);
          game.setLeftPosition(playerList[player1_id].getRightPosition(), playerList[player2_id].getRightPosition());
          game.setMatchDefine([round, j]);
          gameList.push(game);
        }else if(round == maxNumberOfGame){
          const game1 = [round-1, 2*j-1];
          const game2 = [round-1, 2*j];
          const game = new FinalGame();
          const targetMatchDefine1 = game1;
          const targetMatchDefine2 = game2;
          const foundGame1 = gameList.filter(game => {
            return game.getMatchDefine()[0] == targetMatchDefine1[0] && game.getMatchDefine()[1] == targetMatchDefine1[1];
          })
          const foundGame2 = gameList.filter(game => {
            return game.getMatchDefine()[0] == targetMatchDefine2[0] && game.getMatchDefine()[1] == targetMatchDefine2[1];
          })
          game.setLeftPosition(foundGame1[0].getRightPosition(), foundGame2[0].getRightPosition());
          game.setMatchDefine([round, j]);
          gameList.push(game);
        }else{
          const game1 = [round-1, 2*j-1];
          const game2 = [round-1, 2*j];
          const game = new Game();
          const targetMatchDefine1 = game1;
          const targetMatchDefine2 = game2;
          const foundGame1 = gameList.filter(game => {
            return game.getMatchDefine()[0] == targetMatchDefine1[0] && game.getMatchDefine()[1] == targetMatchDefine1[1];
          })
          const foundGame2 = gameList.filter(game => {
            return game.getMatchDefine()[0] == targetMatchDefine2[0] && game.getMatchDefine()[1] == targetMatchDefine2[1];
          })
          game.setLeftPosition(foundGame1[0].getRightPosition(), foundGame2[0].getRightPosition());
          game.setMatchDefine([round, j]);
          gameList.push(game);
        }
      }
    }
    console.log(gameList.length);
  }
  
}
function resultSet(){
  const result = Object.values(dynamicData.get_result);
  const resultArray = result.filter(value => value.event_id == event_id);
  Object.keys(resultArray).forEach(key => {
    const value = resultArray[key];
    //まず、データから何ラウンド目の何試合目かを計算する
    const identify = NumberOfGameDefine(value.draw_id_1, value.draw_id_2, maxLength);
    const round = identify[0];
    const numberOfGame = identify[1];
    const targetMatchDefine = [round, numberOfGame];
    //gameListから該当する試合を探す
    const foundGame = gameList.filter(game => {
      return game.getMatchDefine()[0] == targetMatchDefine[0] && game.getMatchDefine()[1] == targetMatchDefine[1];
    });
    //試合結果を格納する
    foundGame[0].setResult(value.score1, value.score2, value.tiebreak);
    //勝者を格納する
    foundGame[0].setWinner(value.winner_id);
    console.log(foundGame[0].getWinner());
    //次の試合に勝者をセットする
    const nextNumberOfGame = NextNumberOfGameDefine(value.winner_id, targetMatchDefine, maxLength);
    //もし、次の試合がない場合は、何もしない
    if(nextNumberOfGame[0] == null){
      console.log('次の試合はありません');
    }else{
      const nextTargetMatchDefine = [nextNumberOfGame[0], nextNumberOfGame[1]];//次の試合のラウンド数と、そのラウンドの中で何試合目かを表す
      const foundNextGame = gameList.filter(game => {//次の試合を探す
        return game.getMatchDefine()[0] == nextTargetMatchDefine[0] && game.getMatchDefine()[1] == nextTargetMatchDefine[1];
      });
      if(nextNumberOfGame[2] == 'early'){
        foundNextGame[0].setPlayer1(value.winner_id);
      }else if(nextNumberOfGame[2] == 'late'){
        foundNextGame[0].setPlayer2(value.winner_id);
      }
    }
  });
}

class Player{
  constructor(draw_id, user_belonging, user_name){
    this.draw_id = draw_id;
    this.user_belonging = user_belonging;
    this.user_name = user_name;
    this.fontSize = 10;
  }
  getRectSize(){
    //ここでは、一つの四角形の大きさを決める
    //横幅、縦幅の順で返す
    return [250, 20];
  }
  getLeftPosition(){
    //ここでは、四角形の左側の中心の座標を返す
    const size = this.getRectSize();
    const position = [30,this.draw_id*size[1]+20];
    return position;
  }
  
  getRightPosition(){
    //ここでは、四角形の右側の中心の座標を返す
    //getLeftPosition()とgetRectSize()を参照し、計算する
    const size = this.getRectSize();
    const leftPosition = this.getLeftPosition();
    const position = [leftPosition[0]+size[0], leftPosition[1]];
    return position;
  }

  display(){
    //箱を描画
    const size = this.getRectSize();
    const position = this.getLeftPosition();
    if(this.draw_id % 2 == 1){
      centerLeftFillRect(position[0], position[1],size[0], size[1], "rgba(200,200,200,1)");
    }else{
      centerLeftFillRect(position[0], position[1],size[0], size[1], "rgba(255,255,255,1)");
    }
    ctx.fillStyle = 'rgba(0,0,0,1)';
    centerLeftText(this.draw_id, this.fontSize, position[0]-20, position[1]);
    if(this.user_name == null && this.user_belonging == null){
      const textContent = 'bye';
      ctx.fillStyle = 'rgba(0,0,0,1)';
      centerLeftText(textContent, this.fontSize, position[0] + this.fontSize, position[1]);
    }else{
      const textContent = `${this.user_name} (${this.user_belonging})`;
      ctx.fillStyle = 'rgba(0,0,0,1)';
      centerLeftText(textContent, this.fontSize, position[0] + this.fontSize, position[1]);
    }
  }
}

class SinglePlayer extends Player{
  constructor(draw_id, user_belonging, user_name){
    super(draw_id, user_belonging, user_name);
  }
}

class DoublesPlayer extends Player{
  constructor(draw_id, user_belonging1, user_name1, user_belonging2, user_name2) {
    super();
    this.draw_id = draw_id;
    this.user_belonging1 = user_belonging1;
    this.user_name1 = user_name1;
    this.user_belonging2 = user_belonging2;
    this.user_name2 = user_name2;
  }
  getRectSize(){
    //ここでは、一つの四角形の大きさを決める
    //横幅、縦幅の順で返す
    return [250, 40];
  }
  
  display(){
    const size = this.getRectSize();
    const position = this.getLeftPosition();
    if(this.draw_id % 2 == 1){
      centerLeftFillRect(position[0], position[1],size[0], size[1], "rgba(200,200,200,1)");
    }else{
      centerLeftFillRect(position[0], position[1],size[0], size[1], "rgba(255,255,255,1)");
    }
    ctx.fillStyle = 'rgba(0,0,0,1)';
    centerLeftText(this.draw_id, this.fontSize, position[0]-20, position[1]);
    if(this.user_name1 == null && this.user_belonging1 == null && this.user_name2 == null && this.user_belonging2 == null){
      const textContent = 'bye';
      ctx.fillStyle = 'rgba(0,0,0,1)';
      centerLeftText(textContent, this.fontSize, position[0] + this.fontSize, position[1]);
    }else{
      const textContent = `${this.user_name1} (${this.user_belonging1})`;
      ctx.fillStyle = 'rgba(0,0,0,1)';
      centerLeftText(textContent, this.fontSize, position[0] + this.fontSize, position[1]-10);
      const textContent2 = `${this.user_name2} (${this.user_belonging2})`;
      ctx.fillStyle = 'rgba(0,0,0,1)';
      centerLeftText(textContent2, this.fontSize, position[0] + this.fontSize, position[1]+10);
    }
  }
}

class Game{
  /*
    要件
    ・勝者番号を返す、returnWinner
    ・若番、遅番を内包する
    ・試合を一意に識別する番号を内包する配列を返す
      ー[0]ラウンド数
      ー[1]ラウンドの内何試合目か
    ・
  */
  constructor(){
    this.player1_id;
    this.player2_id;
    this.leftPosition1 = [];
    this.leftPosition2 = [];
    this.matchDefine = [null,null];
    this.score1 = null;
    this.score2 = null;
    this.tiebreak = null;
    this.winner = null;
  }
  setMatchDefine(matchDefine){
    this.matchDefine = matchDefine;
  }
  setPlayer1(player1_id){
    this.player1_id = player1_id;
  }
  setPlayer2(player2_id){
    this.player2_id = player2_id;
  }
  //一旦結果のみを格納できるようにする
  setResult(score1, score2, tiebreak){
    this.score1 = score1;
    this.score2 = score2;
    this.tiebreak = tiebreak;
  }
  setWinner(winner){
    this.winner = winner;
  }
  getWinner(){

    return this.winner;
  }
  setLeftPosition(early_position, late_position){
    this.leftPosition1 = early_position;
    this.leftPosition2 = late_position;
  }
  getMatchDefine(){
    return this.matchDefine;
  }
  getRightPosition(){
    return [(this.leftPosition1[0]+lineLength), (this.leftPosition1[1]+this.leftPosition2[1])/2];
  }
  display(){
    if(this.winner == null && this.matchDefine[0]!=1){//もし、勝者が決まっていない場合、かつ一回戦でない場合
      if(this.player1_id != null){
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition1[0], this.leftPosition1[1], this.leftPosition1[0]+lineLength, this.leftPosition1[1]);
      }else{
        line(this.leftPosition1[0], this.leftPosition1[1], this.leftPosition1[0]+lineLength, this.leftPosition1[1]);
      }
      if(this.player2_id != null){
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition2[0], this.leftPosition2[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
      }else{
        line(this.leftPosition2[0], this.leftPosition2[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
      }
      line(this.leftPosition1[0]+lineLength, this.leftPosition1[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
    }else{
      line(this.leftPosition1[0], this.leftPosition1[1], this.leftPosition1[0]+lineLength, this.leftPosition1[1]);
      line(this.leftPosition2[0], this.leftPosition2[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
      line(this.leftPosition1[0]+lineLength, this.leftPosition1[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
      if(this.winner == this.player1_id){
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition1[0], this.leftPosition1[1], this.leftPosition1[0]+lineLength, this.leftPosition1[1]);
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition1[0]+lineLength, this.leftPosition1[1]-1.5, this.leftPosition1[0]+lineLength, (this.leftPosition1[1]+this.leftPosition2[1])/2+1.5);
        
      }else if(this.winner == this.player2_id){
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition2[0], this.leftPosition2[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
        customLine(3, 'rgba(255,0,0,1)', this.leftPosition2[0]+lineLength, this.leftPosition2[1]+1.5, this.leftPosition2[0]+lineLength, (this.leftPosition1[1]+this.leftPosition2[1])/2-1.5);
      }
    }
  }
}
class FinalGame extends Game{
  display(){
    line(this.leftPosition1[0], this.leftPosition1[1], this.leftPosition1[0]+lineLength, this.leftPosition1[1]);
    line(this.leftPosition2[0], this.leftPosition2[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
    line(this.leftPosition1[0]+lineLength, this.leftPosition1[1], this.leftPosition2[0]+lineLength, this.leftPosition2[1]);
    line(this.leftPosition1[0]+lineLength, (this.leftPosition1[1]+this.leftPosition2[1])/2, this.leftPosition1[0]+lineLength+lineLength, (this.leftPosition1[1]+this.leftPosition2[1])/2);
  }
}

function NumberOfGameDefine(player1_id, player2_id, numberOfPlayer){
  const maxNumberOfGame = Math.ceil(Math.log2(numberOfPlayer));//最小限の大きさの2のx乗を求める
  const tournamentScale = Math.pow(2, maxNumberOfGame);//32トーナメントなど、xトーナメントというふうにトーナメントの規模感を算出する
  for (let index = 1; index <= maxNumberOfGame; index++) {
    const numberInBox = Math.pow(2, index);//試合の組み合わせの山を一つの箱という表現にする
    const player1_numberInNumberOfGame = Math.ceil(player1_id/numberInBox);
    const player2_numberInNumberOfGame = Math.ceil(player2_id/numberInBox);
    if(player1_numberInNumberOfGame == player2_numberInNumberOfGame){
      return [index, player1_numberInNumberOfGame];
    }
  }
}
function NextNumberOfGameDefine(player_id,numberOfGame, numberOfPlayer){
  //この関数は、次の試合のラウンド数を求め、その次のラウンドの中で何試合目かを返し、かつplayer_idが次の試合において早番か遅番かを返す
  //numberOfGameは、現在の試合のラウンド数と、そのラウンドの中で何試合目かを表す
  //numberOfPlayerは、エントリー人数を表す
  //手順を以下に示す
  // 1. まず、現在の試合のラウンド数を取得する
  // 2. 次の試合のラウンド数を取得する
  // 3. 次の試合のラウンド数が、最終ラウンドであるかどうかを判定する
  // 4. 最終ラウンドである場合、何試合目かを返す
  // 5. 最終ラウンドでない場合、早番か遅番かを判定する
  // 6. 早番である場合、'early'を返す
  // 7. 遅番である場合、'late'を返す
  const maxNumberOfGame = Math.ceil(Math.log2(numberOfPlayer));//最小限の大きさの2のx乗を求める
  const tournamentScale = Math.pow(2, maxNumberOfGame);//32トーナメントなど、xトーナメントというふうにトーナメントの規模感を算出する
  const currentRound = numberOfGame[0];
  const currentNumberOfGame = numberOfGame[1];
  const nextRound = currentRound + 1;
  const nextNumberOfGame = Math.ceil(currentNumberOfGame/2);
  if(nextRound == maxNumberOfGame){
    return [nextRound, nextNumberOfGame, null];
  }
  if(currentNumberOfGame % 2 == 1){
    return [nextRound, nextNumberOfGame, 'early'];
  }else{
    return [nextRound, nextNumberOfGame, 'late'];
  }
}
function centerStrokeRect(x, y, width, height){
  const _x = x - width/2;
  const _y = y - height/2;
  ctx.strokeRect(_x, _y, width, height);
}
function centerFillRect(x, y, width, height, color){
  ctx.fillStyle = color;
  const _x = x - width/2;
  const _y = y - height/2;
  ctx.fillRect(_x, _y, width, height);
}
function centerLeftFillRect(x, y, width, height, color){
  ctx.fillStyle = color;
  const _x = x;
  const _y = y - height/2;
  ctx.fillRect(_x, _y, width, height);
}
function centerLeftText(context,fontSize, x, y){
  ctx.font = `${fontSize}px Arial`;
  ctx.fillText(context, x, y+fontSize/2-1);
}
function line(x1, y1, x2, y2){
  ctx.strokeStyle = 'rgba(0,0,0,1)';
  ctx.lineWidth = 1;
  ctx.beginPath();
  ctx.moveTo(x1, y1);
  ctx.lineTo(x2, y2);
  ctx.stroke();
}
function customLine(strokeWeight, color, x1, y1, x2, y2){
  ctx.strokeStyle = color;
  ctx.lineWidth = strokeWeight;
  ctx.beginPath();
  ctx.moveTo(x1, y1);
  ctx.lineTo(x2, y2);
  ctx.stroke();
}
function draw(){
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  if(empty){
    ctx.fillStyle = 'rgba(0,0,0,1)';
    centerLeftText(`${currentTime} 更新`,10,20,10);
    centerLeftText(`現在、${currentEventName}のエントリー人数は０人です。`, 10, 20, 40);
  }else{
    ctx.fillStyle = 'rgba(0,0,0,1)';
    centerLeftText(`${currentTime} 更新`,10,20,10);
    playerList.forEach(element => {
      element.display();
    });
    gameList.forEach(element => {
      element.display();
    });
  }
  window.requestAnimationFrame(draw);
}
draw();