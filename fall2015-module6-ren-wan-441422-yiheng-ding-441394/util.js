// ID gen
function genid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}


function Room (roomname, adminname, adminSocket, password) {
    this.name = roomname;
    this.admin = adminname;
    this.password = password;
    this.userlist = [];
    this.banlist = [];
    this.userSocket = {};
    // add admin to user list
    this.userlist.push(adminname);
    this.userSocket[adminname] = adminSocket;

    this.getName = function() {
        return this.name;
    };
    this.setName = function(roomname) {
        this.name = roomname;
    };
    this.getAdmin = function() {
        return this.admin;
    };
    this.setAdmin = function(username) {
        if (this.userlist[username]) {
            this.admin = username;
            return true;
        }
        return false;
    };
    this.isAdmin = function(username) {
        return this.admin.localeCompare(username) == 0;
    };
    this.changePassword = function(password) {
        this.password = password;
    };
    // this.getPassword = function() {
    //     return this.password;
    // };
    this.isCorrectPassword = function(password) {
        // return this.password.localeCompare(password) == 0;
        return this.password.localeCompare(password) == 0;
    };
    // return false if the user cannot join this room
    this.addUser = function(username, socket) {
        if (this.userlist.indexOf(username) > -1 ||
            this.banlist.indexOf(username) > -1) {
            return false;
        }
        this.userlist.push(username);
        this.userSocket[username] = socket;
        return true;
    };
    this.removeUser = function(username) {
        var index;

        if ((index = this.userlist.indexOf(username)) > -1) {
            this.userlist.splice(index, 1);
            if (this.userlist.length > 0 && username == this.admin) {
                this.admin = this.userlist[0];
            }
            if (this.userSocket[username]) {
                delete this.userSocket[username];
            }
            return true;
        }
        return false;
    };
    this.changeUserName = function(old_username, new_username) {
        var index;
        if ((index = this.userlist.indexOf(old_username)) > -1 &&
            !this.isInRoom(new_username)) {
            var socket = this.userSocket[old_username];
            this.userSocket[new_username] = socket;
            this.userlist[index] = new_username;
            return true;
        }
        return false;
    };
    this.getSocket = function(username) {
        return this.userSocket[username];
    };
    this.getUserList = function() {
        return this.userlist;
    };
    this.getBanList = function() {
        return this.banlist;
    };
    this.isEmptyRoom = function() {
        return this.userlist.length == 0;
    };
    this.isInRoom = function(username) {
        return this.userlist.indexOf(username) > -1;
    };
    this.isBanned = function(username) {
        return this.banlist.indexOf(username) > -1;
    };
    this.isPrivate = function() {
        return this.password.length != 0;
    };
    this.banUser = function(username) {
        var index;

        if ((index = this.banlist.indexOf(username)) > -1) {
                return false;
        }
        else {
            this.banlist.push(username);
            if ((index = this.userlist.indexOf(username)) > -1) {
                this.userlist.splice(index, 1);
                if (this.userSocket[username]) {
                    delete this.userSocket[username];
                }
            }
            return true;
        }
    };
}



function Rooms () {
    this.rooms = {};
    this.createNewRoom = function(roomname, adminname, adminSocket, password) {
        if (typeof this.rooms[roomname] == "undefined") {
            this.rooms[roomname] = new Room(roomname, adminname, adminSocket, password);
            return true;
        }
        return false;
    };
    this.deleteRoom = function(roomname) {
        delete this.rooms[roomname];
    };
    this.isExisting = function (roomname) {
        return (typeof this.rooms[roomname] != "undefined")
    };
    this.getRoomList = function () {
        return Object.keys(this.rooms);
    }
    // this.getName = function() {

    // };
    this.setName = function(old_roomname, new_roomname) {
        if (typeof this.rooms[old_roomname] == "undefined" ||
            typeof this.rooms[new_roomname] != "undefined") {
            return false;
        }
        var room = this.rooms[old_roomname];
        room.setName(new_roomname);
        delete this.rooms[old_roomname];
        rooms[new_roomname] = room;
        return true;
    };
    this.getAdmin = function(roomname) {
        return this.rooms[roomname].getAdmin();
    };
    this.setAdmin = function(roomname, username) {
        return this.rooms[roomname].setAdmin(username);
    };
    this.isAdmin = function(roomname, username) {
        return this.rooms[roomname].isAdmin(username);
    };
    this.changePassword = function(roomname, password) {
        this.rooms[roomname].changePassword(roomname, password);
    };
    this.isCorrectPassword = function(roomname, password) {
        return this.rooms[roomname].isCorrectPassword(password);
    };
    this.addUser = function(roomname, username, socket) {
        return this.rooms[roomname].addUser(username, socket);
    };
    this.removeUser = function(roomname, username) {
        var success = this.rooms[roomname].removeUser(username);
        return success;
    };
    this.changeUserName = function(roomname, old_username, new_username) {
        return this.rooms[roomname].changeUserName(old_username, new_username);
    };
    this.getSocket = function(roomname, username) {
        return this.rooms[roomname].getSocket(username);
    };
    this.getUserList = function(roomname) {
        return this.rooms[roomname].getUserList();
    };
    this.getBanList = function(roomname) {
        return this.rooms[roomname].getBanList();
    };
    this.isEmptyRoom = function(roomname) {
        return this.rooms[roomname].isEmptyRoom();
    };
    this.isInRoom = function(roomname, username) {
        return this.rooms[roomname].isInRoom(username);
    };
    this.isBanned = function(roomname, username) {
        return this.rooms[roomname].isBanned(username);
    };
    this.isPrivate = function(roomname) {
        return this.rooms[roomname].isPrivate();
    };
    this.banUser = function(roomname, username) {
        return this.rooms[roomname].banUser(username);
    };
}




























// // Test Case:

// var rooms = {};

// rooms["Droom"] = new Room("Droom", "Dingding", "");
// rooms["Wroom"] = new Room("Wroom", "wa", "123");

// rooms["Droom"].addUser("user1");
// rooms["Droom"].addUser("user2");
// rooms["Droom"].addUser("user3");
// rooms["Droom"].addUser("user4");
// rooms["Droom"].addUser("user5");

// rooms["Wroom"].addUser("user6");
// rooms["Wroom"].addUser("user7");
// rooms["Wroom"].addUser("user8");
// rooms["Wroom"].addUser("user9");
// rooms["Wroom"].addUser("user10");


// function printList(){
//     var userlist_d = rooms["Droom"].getUserList();
//     var banlist_d  = rooms["Droom"].getBanList();
//     var userlist_w = rooms["Wroom"].getUserList();
//     var banlist_w  = rooms["Wroom"].getBanList();

//     console.log("----------Droom User List---------");
//     for (i = 0; i < userlist_d.length; i++) {
//         console.log(userlist_d[i]);
//     }

//     console.log("----------Droom Ban List---------");
//     for (i = 0; i < banlist_d.length; i++) {
//         console.log(banlist_d[i]);
//     }

//     console.log("----------Wroom User List---------");
//     for (i = 0; i < userlist_w.length; i++) {
//         console.log(userlist_w[i]);
//     }

//     console.log("----------Wroom Ban List---------");
//     for (i = 0; i < banlist_w.length; i++) {
//         console.log(banlist_w[i]);
//     }
// }

// console.log("***********Existing user***********");
// printList();


// console.log("***********Ban user***********");
// rooms["Droom"].banUser("user4"); // existing user
// rooms["Droom"].banUser("user6"); // non-existing user
// rooms["Wroom"].banUser("user8");
// rooms["Wroom"].banUser("user8"); // duplicate ban
// printList();


// console.log("***********Remove user***********");
// rooms["Droom"].removeUser("user3"); // remove user
// rooms["Droom"].removeUser("user8"); // remove non-existing user
// rooms["Wroom"].removeUser("user8"); // remove banned user
// printList();


// console.log("***********Add user***********");
// rooms["Droom"].addUser("user4"); // add banned user
// rooms["Wroom"].addUser("user11"); // add user
// printList();

// // console.log(rooms["Droom"].isBanned("user4"));
// // console.log(rooms["Wroom"].isBanned("user4"));

// console.log("***********Change username***********");

// rooms["Droom"].changeUserName("user3", "user12"); // change non-existing username
// rooms["Wroom"].changeUserName("user9", "user13"); // change existing username
// rooms["Wroom"].changeUserName("user6", "user7");  // change to existing username
// printList();


// console.log("***********ID***********");

// console.log(genid());





var rooms = new Rooms();
rooms.createNewRoom("Droom", "Dingding", "123fdfafda", "");
rooms.createNewRoom("Wroom", "wa", "1fdafadaf", "123");

console.log(rooms.isPrivate("Droom"));
console.log(rooms.isPrivate("Wroom"));


var roomlist = rooms.getRoomList();
var privatelist = {};

console.log(roomlist);
for (var i in roomlist) {
    var room = roomlist[i];
    privatelist[room] = rooms.isPrivate(room);
}

console.log(privatelist);


rooms.addUser("Droom", "user1", "user1socket");
rooms.addUser("Droom", "user2", "user2socket");
rooms.addUser("Droom", "user3", "user3socket");
rooms.addUser("Droom", "user4", "user4socket");
rooms.addUser("Droom", "user5", "user5socket");

rooms.addUser("Wroom", "user6", "user6socket");
rooms.addUser("Wroom", "user7", "user7socket");
rooms.addUser("Wroom", "user8", "user8socket");
rooms.addUser("Wroom", "user9", "user9socket");
rooms.addUser("Wroom", "user10", "user10socket");


function printList_new(){
    var userlist_d = rooms.getUserList("Droom");
    var banlist_d  = rooms.getBanList("Droom");
    var userlist_w = rooms.getUserList("Wroom");
    var banlist_w  = rooms.getBanList("Wroom");

    console.log("----------Droom User List---------");
    for (i = 0; i < userlist_d.length; i++) {
        console.log(userlist_d[i] + " " + rooms.getSocket("Droom", userlist_d[i]));
    }

    console.log("----------Droom Ban List---------");
    for (i = 0; i < banlist_d.length; i++) {
        console.log(banlist_d[i]);
    }

    console.log("----------Wroom User List---------");
    for (i = 0; i < userlist_w.length; i++) {
        console.log(userlist_w[i] + " " + rooms.getSocket("Wroom", userlist_w[i]));
    }

    console.log("----------Wroom Ban List---------");
    for (i = 0; i < banlist_w.length; i++) {
        console.log(banlist_w[i]);
    }
}



console.log("***********Existing user***********");
printList_new();


console.log("***********Ban user***********");
rooms.banUser("Droom", "user4"); // existing user
rooms.banUser("Droom", "user6"); // non-existing user
rooms.banUser("Wroom", "user8");
rooms.banUser("Wroom", "user8"); // duplicate ban
printList_new();


console.log("***********Test AdminUser***********");
console.log(rooms.isAdmin("Droom", "Dingding"));
console.log(rooms.isAdmin("Droom", "dingding"));
console.log(rooms.isAdmin("Wroom", "wa"));

console.log("***********Remove user***********");
rooms.removeUser("Droom", "user3"); // remove user
rooms.removeUser("Droom", "user8"); // remove non-existing user
rooms.removeUser("Wroom", "user8"); // remove banned user
rooms.removeUser("Wroom", "wa");
printList_new();


console.log("***********AdminUser***********");
console.log(rooms.getAdmin("Droom"));
console.log(rooms.getAdmin("Wroom"));
console.log(rooms.isAdmin("Droom", "Dingding"));
console.log(rooms.isAdmin("Droom", "dingding"));
console.log(rooms.isAdmin("Wroom", "wa"));


console.log("***********Add user***********");
rooms.addUser("Droom", "user4", "fdafafada"); // add banned user
rooms.addUser("Wroom", "user11", "dfdfdfe"); // add user
printList_new();

// console.log(rooms["Droom"].isBanned("user4"));
// console.log(rooms["Wroom"].isBanned("user4"));

console.log("***********Change username***********");

rooms.changeUserName("Droom", "user3", "user12"); // change non-existing username
rooms.changeUserName("Wroom", "user9", "user13"); // change existing username
rooms.changeUserName("Wroom", "user6", "user7");  // change to existing username
printList_new();


console.log("***********Test EmptyRoom***********");
rooms.removeUser("Droom", "Dingding");
rooms.removeUser("Droom", "user1");
rooms.removeUser("Droom", "user2");

console.log(rooms.isEmptyRoom("Droom"));
rooms.removeUser("Droom", "user5");
// console.log(rooms.isEmptyRoom("Droom"));





































////////////////////////////////////////////////////////////////////////////////////////
// function UserList () {
//  this.list = [];
//  this.addUser = function(user) {
//      this.list.push(user);
//  };
//  this.removeUser = function(user) {
//      var index = this.list.indexOf(user);
//      if (index > -1) {
//          this.list.splice(index, 1);
//      }
//  }
//     this.length = function() {
//         return this.list.length;
//     }
// }



// function User (name) {
//  this.name = name;
//  this.isBanned = false;
//     this.getName = function() {
//         return this.name;
//     };
//     this.setName = function(name) {
//         this.name = name;
//     };
//  this.addBanFlag = function() {
//      this.isBanned = true;
//  }
//  this.removeBanFlag = function() {
//      this.isBanned = false;
//  }
// }