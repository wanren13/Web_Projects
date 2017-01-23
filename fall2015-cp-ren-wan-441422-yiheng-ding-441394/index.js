var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);


var anonymous = ['alligator', 'anteater', 'armadillo', 'auroch', 'axolotl', 'badger', 'bat', 'beaver', 'buffalo',
 'camel', 'chameleon', 'cheetah', 'chipmunk', 'chinchilla', 'chupacabra', 'cormorant', 'coyote', 'crow', 'dingo', 
 'dinosaur', 'dolphin', 'duck', 'elephant', 'ferret', 'fox', 'frog', 'giraffe', 'gopher', 'grizzly', 'hedgehog', 
 'hippo', 'hyena', 'jackal', 'ibex', 'ifrit', 'iguana', 'koala', 'kraken', 'lemur', 'leopard', 'liger', 'llama', 
 'manatee', 'mink', 'monkey', 'narwhal', 'nyan cat', 'orangutan', 'otter', 'panda', 'penguin', 'platypus', 'python', 
 'pumpkin', 'quagga', 'rabbit', 'raccoon', 'rhino', 'sheep', 'shrew', 'skunk', 'slow loris', 'squirrel', 'turtle', 
 'walrus', 'wolf', 'wolverine', 'wombat'];

var players = {};

var curPlayerIndex = -1;

var curQuestionIndex = -1;

var CORRECT_POINT = 2;
var TIME_PER_TURN = 60;

var questions;// = ['Soccer', 'Lady Gaga', 'Microsoft', 'Obama', 'Selfie', 'Burger King', 'Tsunami', 'Meatball', 'Hearthstone'];

var curAns;

var inGamePlayersSid = [];

var isGameStart = false;

var correctIdInThisQuestion = {};

var correctNumberInThisTurn = 0;

var timer;



//------------------------------------------

var MongoClient = require('mongodb').MongoClient;

// Connection URL. This is where your mongodb server is running.
var url = 'mongodb://localhost:27017/Draw_Guess';

// Use connect method to connect to the Server

function drawQuestions(){
  console.log('drawQuestions');
  MongoClient.connect(url, function (err, db) {
    if (err) {
      console.log('Unable to connect to the mongoDB server. Error:', err);
    } else {
      //HURRAY!! We are connected. :)
      console.log('Connection established to', url);

      // Get the documents collection
      var collection = db.collection('words');

      
      collection.find({name: 'sport'}).toArray(function (err, result) {
        if (err) {
          console.log(err);
        } else if (result.length) {
          var wordlist = result[0].wordlist;
          var rand = randomIntInc(0, wordlist.length-1);
          console.log(wordlist);
          questions = wordlist;
        } else {
          console.log('No document(s) found with defined "find" criteria!');
        }
        db.close();
      });
    }
  });
}


// function drawQuestion(){
  
// }


function randomIntInc (low, high) {
    return Math.floor(Math.random() * (high - low + 1) + low);
}




//------------------------------------------



function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getAnonymousName(socketid){
  var sum = 0;
  for (var i = 0, len = socketid.length; i < len; i++) {
    sum += socketid[i].charCodeAt(0);
  }
  return capitalizeFirstLetter(anonymous[sum % anonymous.length]);
}


function findAndRemove(array, property, value) {
  array.forEach(function(result, index) {
    if(result[property] === value) {
      //Remove from array
      array.splice(index, 1);
    }    
  });
}



function nextturn(){
  clearTimeout(timer);
  console.log('***** New Turn ******');
  curPlayerIndex ++;
  curQuestionIndex ++;
  if(curPlayerIndex >= inGamePlayersSid.length){
    isGameStart = false;
    gameFinish();
    return;
  }
  curAns = questions[curQuestionIndex];
  
  console.log('curAns is ',curAns);
  var socketid = inGamePlayersSid[curPlayerIndex];
  console.log(players[socketid]);
  var player = {'id':socketid, 'name': players[socketid]['name']};
  io.emit('nextturn', player);
  io.to(inGamePlayersSid[curPlayerIndex]).emit('getquestion', curAns);

  correctIdInThisQuestion = {};
  correctNumberInThisTurn = 0;
  for(var i=0; i<inGamePlayersSid.length; i++){
    correctIdInThisQuestion[inGamePlayersSid[i]] = false;
  }

  timer = setTimeout(function () {
    io.emit('timesup', '');
    nextturn();
    }, TIME_PER_TURN*1000);
}

function findHighestPoint(){
  var maxSid;
  var maxPoint = -1;
  console.log('stat');
  for(var i=0; i<inGamePlayersSid.length; i++){
    var point = parseInt(players[inGamePlayersSid[i]]['point']);
    console.log(point);
    if(point > maxPoint){
      maxPoint = point;
      maxSid = inGamePlayersSid[i];

    }
  }
  return players[maxSid]['name'];
}

function gameFinish(){
  var winner = findHighestPoint();
  io.emit('gamefinish', winner);
}


app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});


drawQuestions();

console.log('questions');
console.log(questions);

io.on('connection', function(socket){
  
  console.log('a user connected');
  // name = "Anonymous " + getAnonymousName(socket.id);
  name = getAnonymousName(socket.id);
  player = {"pid": socket.id, "name": name, "point": "0"};

  
  io.emit('getplayers', players);

  io.emit('connected', {'id':socket.id,'player':player});
  if(!(socket.id in players)){
    players[socket.id] = player;
  }
  

  console.log('#######now list######');
  console.log(players);
  console.log('#####################');




  socket.on('disconnect', function(){
    console.log(socket.id + 'disconnected');
    delete players[socket.id];
    io.emit('disconnected', socket.id);
    // findAndRemove(players, 'socketid', socket.id);
  });


  socket.on('startgame', function(){
    if(!isGameStart){
      isGameStart = true;
      console.log('***** Here are all in-game players ******');
      for(var playerId in players){
        inGamePlayersSid.push(playerId);
        console.log(playerId);
      }
      console.log('***** End ******');
      io.emit('startgame', '');
      nextturn();
    }
    // curAns = questions[curQuestionIndex];
    // io.to(inGamePlayersSid[curPlayerIndex]).emit('getquestion', questions[curQuestionIndex]);

    // correctIdInThisQuestion = {};
    // for(var i=0; i<inGamePlayersSid.length; i++){
    //   correctIdInThisQuestion[inGamePlayersSid[i]] = false;
    // }

  });


  socket.on('nextturn', function(){
    nextturn();
  });

  socket.on('clear', function(){
    io.emit('clear', '');
  });


  socket.on('answer', function(msg){
    
    if(msg == curAns){
      
      if(correctIdInThisQuestion[socket.id] == false){
        io.to(socket.id).emit('correctanswer', answerObj);
        var oPoint = parseInt(players[socket.id]['point']);
        var dPoint = CORRECT_POINT;
        if(correctNumberInThisTurn == 0){
          dPoint = 2 * CORRECT_POINT;
        }
        players[socket.id]['point'] = oPoint + dPoint;
        io.emit('updatepoint', {'pid':socket.id, 'point': dPoint});
        correctIdInThisQuestion[socket.id] = true;
        correctNumberInThisTurn ++;
        if(correctNumberInThisTurn >= inGamePlayersSid.length-1){
          nextturn();
        }
        var answerObj = {'pid':socket.id, 'content': players[socket.id]['name']+ ' answered correctly, and get '+ dPoint +' points!'};
        io.emit('answer', answerObj);
      }

      
    }else{
      var answerObj = {'pid':socket.id, 'content':msg};
      io.emit('answer', answerObj);
    }
    
  });

  socket.on('draw', function(msg){
    io.emit('draw', msg);
  });

//   setInterval(function(){
//   console.log('test');
// }, 60 * 60 * 1000);  

  // socket.on('timesup', function(){

  // });
});


http.listen(3000, function(){
  console.log('listening on *:3000');
});
