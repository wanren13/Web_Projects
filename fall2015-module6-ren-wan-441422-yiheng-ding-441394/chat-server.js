// Require the packages we will use:
var http = require("http"),
    socketio = require("socket.io"),
    fs = require("fs");
 
// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function(req, res){
    // This callback runs when a new connection is made to our HTTP server.
 
    fs.readFile("client.html", function(err, data){
        // This callback runs when the client.html file has been read from the filesystem.
 
        if(err) return res.writeHead(500);
        res.writeHead(200);
        res.end(data);
    });
});
app.listen(3456);

 
// Do the Socket.IO magic:
var io = socketio.listen(app);
var rooms = new Rooms();
var debug = true;
var userOnline = [];
var socketRoomUser = {};
io.sockets.on("connection", function(socket){
    // This callback runs when a new Socket.IO connection is established.
 
    socket.join(socket.id);


    socket.on('create_room', function(data) {
        var roomname = data['roomname'];
        var adminname = data['adminname'];
        var password = data['password'];

        ////////////////////////////////////////////////////////////////////////////////
        //TODO
        //password = "";
        ///////////////////////////////////////////////////////////////////////////////

        if (userOnline.indexOf(socket.id) == -1) {
            userOnline.push(socket.id);
        }

        socketRoomUser[socket.id] = {roomname: roomname, username: adminname};
        var success = rooms.createNewRoom(roomname, adminname, socket, password);

        if (debug) {
            console.log("In create_room| roomname: " + roomname + " adminname: " + adminname + " password: " + password);
            console.log(userOnline);
            console.log(socketRoomUser);
            var sock_id = rooms.getSocket(roomname, adminname).id;
            console.log("Socket ID:");
            console.log(sock_id);
        }

        var message;
        if (success) {
            message = "Room is created.";
            socket.join(roomname);
            var userlist = rooms.getUserList(roomname);
+           io.sockets.in(socket.id).emit('update_user_list',{userlist: userlist});
            io.sockets.emit('new_room_created', {roomname:roomname, isprivate: !(password.localeCompare("") == 0)});
            io.sockets.in(roomname).emit('admin_status',{admin: adminname});
        }
        else {
            message = "Room already exists.";
        }

        io.sockets.in(socket.id).emit('create_room_result', {success: success, message: message, roomname: roomname, username: adminname});
        //########need to emit a global one
    });

    socket.on('get_roomlist', function() {
        if (debug) {
            console.log("In get_roomlist");
        }
        var roomlist = rooms.getRoomList();
        var privatelist = {};

        // for (var room in rooms.rooms) {
        //     var name = rooms.rooms[]
        // }

        for (var i = 0; i < roomlist.length; i++) {
            var room = roomlist[i];
            privatelist[room] = rooms.isPrivate(room);
        }

        io.sockets.emit('get_roomlist_result', {roomlist: roomlist, privatelist: privatelist});
    });

    // Need to be fixed
    // socket.on('change_room_name', function(data)) {
    //     var old_roomname = data['old_roomname'];
    //     var new_roomname = data['new_roomname'];
    //     var success = rooms.setName(old_roomname, new_roomname);
    //     socket.leave(old_roomname);
    //     socket.join(new_roomname);
    // }

    socket.on('join_room', function(data) {
        var roomname = data['roomname'];
        var username = data['username'];
        //##############not used
        var password = data['password'];

        socketRoomUser[socket.id] = {roomname: roomname, username: username};

        if (debug) {
            console.log("In join_room| roomname: " + roomname + " username: " + username);
            console.log(userOnline);
            console.log(socketRoomUser);
        }

        console.log("##############");
        console.log(roomname);
        console.log(password);
        console.log(rooms.rooms[roomname].password);
        var success = rooms.isCorrectPassword(roomname, password);
        if (!success) {
            io.sockets.in(socket.id).emit('join_room_result', {success: success, roomname:roomname, username:username, message: "Incorrect Password."});
        }
        else{
            var isbanned = rooms.isBanned(roomname, username);
            if(isbanned) {
                io.sockets.in(socket.id).emit('join_room_result', {success: false, roomname:roomname, username:username, message: "You are banned by admin."});
            }
            else{
                success = rooms.addUser(roomname, username, socket);
                if(!success) {
                    io.sockets.in(socket.id).emit('join_room_result', {success: success, roomname:roomname, username:username, message: "Username exists."});
                }
                else {
                    io.sockets.in(socket.id).emit('join_room_result', {success: success,roomname:roomname, username:username, message: "Joined"});
                    socket.join(roomname);
                    var userlist = rooms.getUserList(roomname);
                    io.sockets.in(socket.id).emit('update_user_list',{userlist: userlist});
                    io.sockets.in(roomname).emit('user_added_to_room', {username: username});
                    var adminname = rooms.getAdmin(roomname);
                    io.sockets.in(socket.id).emit('admin_status',{admin: adminname});

                    if(debug) {
                        console.log("Socket ID:");
                        var sock_id = rooms.getSocket(roomname, username).id;
                        console.log(sock_id);
                    }
                }
            }
        }
    });

    socket.on('leave_room', function(data) {
        var roomname = data['roomname'];
        var username = data['username'];

        // delete socketRoomUser[socket.id];

        if (debug) {
            console.log("In leave_room| roomname: " + roomname + " username: " + username);
            console.log("Socket ID:");
            var sock_id = rooms.getSocket(roomname, username).id;
            console.log(sock_id);
        }

        var adminChanged = rooms.isAdmin(roomname, username);
        var sock = rooms.getSocket(roomname, username);
        var leaverID = sock.id;
        var success = rooms.removeUser(roomname, username);
        io.sockets.in(socket.id).emit('leave_room_result', {success: success});

        if (success) {

            //////////////////////////////////BUG////////////////////////////////////////

            if (leaverID.localeCompare(socket.id) != 0) {
                io.sockets.in(leaverID).emit('kick_message', {message: "You are kicked by admin"});
            } 

            sock.leave(roomname);
            io.sockets.in(roomname).emit('user_left_from_room', {username: username});

            if (rooms.isEmptyRoom(roomname)){
                rooms.deleteRoom(roomname);
                io.sockets.emit('remove_room_from_list', {roomname: roomname});
            }
            else {
                if (adminChanged) {
                    var adminname = rooms.getAdmin(roomname);
                    io.sockets.in(roomname).emit('admin_status',{admin: adminname});
                }
            }
        }
    });


    socket.on('change_room_password', function(data) {
        var roomname = data['roomname'];
        var password = data['password'];

        if (debug) {
            console.log("In change_room_password| roomname: " + roomname + " password: " + password);
        }

        rooms.changePassword(roomname, password);
    });

    socket.on('change_user_name', function(data) {
        var roomname = data['roomname'];
        var old_username = data['old_username'];
        var new_username = data['new_username'];

        if (debug) {
            console.log("In change_user_name| roomname: " + old_username + " username: " + new_username);
        }

        var success = rooms.changeUserName(roomname, old_username, new_username);
        var message;
        if (success) {
            socketRoomUser[socket.id]['username'] = new_username;
            message = old_username + " change name to " + new_username;
        }
        else {
            message = "User name " + new_username + " already exists."
        }

        io.sockets.in(socket.id).emit('change_user_name_result', {success: success, message: message});
    });

    socket.on('ban_user', function(data) {
        var roomname = data['roomname'];
        var username = data['username'];

        if (debug) {
            console.log("In ban_user| roomname: " + roomname + " username: " + username);
        }

        var bannedSock = rooms.getSocket(roomname, username);
        var bannedID = bannedSock.id;
        var success = rooms.banUser(roomname, username);
        var message;
        if (success) {
            message = username + " is banned.";
            bannedSock.leave(roomname);
            io.sockets.in(roomname).emit('user_left_from_room', {username: username});
            io.sockets.in(bannedID).emit('ban_message', {message: "You are banned by admin"});
        }
        else {
            message = username + " is already in ban list.";
        }

        io.sockets.in(socket.id).emit('ban_user_result', {success: success, message: message});
    });

    socket.on('public_message_to_server', function(data) {
        var roomname = data['roomname'];
        var sender = data['sender'];
        var message = data['message'];

        if (debug) {
           console.log("In public_message_to_server| roomname: " + roomname + "sender: " + sender + " message: " + message);
       }

        io.sockets.in(roomname).emit("public_message_to_client",{sender: sender, message: message});
    });

    socket.on('private_message_to_server', function(data) {
        var message = data['message'];
        var sender = data['sender'];
        var receiver = data['receiver'];
        var roomname = data['roomname'];

        if (debug) {
            console.log("In public_message_to_server| sender: " + sender + " message: " + message);
        }

        var sid = rooms.getSocket(roomname, receiver).id;
        io.sockets.in(sid).emit("private_message_to_client",{sender: sender, message: message});
    });

    socket.on('keyup_to_client', function(data){
        var sender = data['sender'];
        var receiver = data['receiver'];
        var roomname = data['roomname'];
        var sid = rooms.getSocket(roomname, receiver).id;
        io.sockets.in(sid).emit("keyup_from_server",{sender: sender});
    });

    socket.on('keydown_from_client', function(data){
        var sender = data['sender'];
        var receiver = data['receiver'];
        var roomname = data['roomname'];
        var sid = rooms.getSocket(roomname, receiver).id;
        io.sockets.in(sid).emit("keydown_from_server",{sender: sender});
    });

    socket.on('disconnect', function(){
        if (debug) {
            var connectionlist = getConnectionList();
            var userOffline = userOnline.diff(connectionlist);
            console.log("****************Online User****************");
            console.log(connectionlist);
            console.log("****************Disconnected User****************");
            console.log(userOffline);
        }

        if (typeof socketRoomUser[socket.id] != "undefined") {
            var roomname = socketRoomUser[socket.id]['roomname'];
            var username = socketRoomUser[socket.id]['username'];
            delete socketRoomUser[socket.id];


            console.log(socket.id + " left room: " + roomname + " username: " + username);

            var adminChanged = rooms.isAdmin(roomname, username);
            var success = rooms.removeUser(roomname, username);
            io.sockets.in(socket.id).emit('leave_room_result', {success: success});

            if (success) {
                socket.leave(roomname);
                io.sockets.in(roomname).emit('user_left_from_room', {username: username});

                if (rooms.isEmptyRoom(roomname)){
                    rooms.deleteRoom(roomname);
                    io.sockets.emit('remove_room_from_list', {roomname: roomname});
                }
                else {
                    if (adminChanged) {
                        var adminname = rooms.getAdmin(roomname);
                        io.sockets.in(roomname).emit('admin_status',{admin: adminname});
                    }
                }
            }
        }
    });
});





/////////////////////////////////////////////////
// Data Structre


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


function findClientsSocket(roomId, namespace) {
    var res = []
    , ns = io.of(namespace ||"/");    // the default namespace is "/"

    if (ns) {
        for (var id in ns.connected) {
            if(roomId) {
                var index = ns.connected[id].rooms.indexOf(roomId) ;
                if(index !== -1) {
                    res.push(ns.connected[id]);
                }
            } else {
                res.push(ns.connected[id]);
            }
        }
    }
    return res;
}

function getConnectionList(roomId, namespace) {
    var connectionlist = []
    , ns = io.of(namespace ||"/");    // the default namespace is "/"

    if (ns) {
        for (var id in ns.connected) {
            connectionlist.push(id);
        }
    }
    return connectionlist;
}

Array.prototype.diff = function(a) {
    return this.filter(function(i) {
        return a.indexOf(i) < 0;
    });
};