<!doctype html>
<html>

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.6.1/socket.io.js"
        integrity="sha512-xbQU0+iHqhVt7VIXi6vBJKPh3IQBF5B84sSHdjKiSccyX/1ZI7Vnkt2/8y8uruj63/DVmCxfUNohPNruthTEQA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type='text/javascript'>
        var socket = io.connect('http://yeejai.in:10056');
        // optionally use io('http://localhost:3000');
        // but make SURE it matches the jScript src
        socket.on('test',
            function (messageFromServer) {
                console.log('server said: ' + messageFromServer);
            });

    </script>
</head>

<body>
    <h5>Worlds smallest Socket.io example to uppercase strings</h5>
    <p>
        <a href='#'
            onClick="javascript:socket.emit('Client 2 Server Message', 'return UPPERCASED in the console');">return
            UPPERCASED in the console</a>
        <br />
        socket.emit('Client 2 Server Message', 'try cut/paste this command in your console!');
    </p>
</body>

</html>
