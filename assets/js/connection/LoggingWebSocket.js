export default class {
    constructor(url) {
        this.webSocket = new WebSocket(url);

        this.webSocket.addEventListener('open', (e) => {
            this.info('connection opened');
        });

        this.webSocket.addEventListener('message', (e) => {
            this.incoming(e.data);
        });
    }

    addEventListener(event, callback) {
        this.webSocket.addEventListener(event, callback);
    }

    send(message) {
        this.webSocket.send(message);

        this.outgoing(message);
    }

    info(message) {
        console.log('[ws] ' + message);
    }

    incoming(message) {
        console.log('[ws] %c← %c ' + message, 'color: green', 'color: auto');
    }

    outgoing(message) {
        console.log('[ws] %c→ %c ' + message, 'color: red', 'color: auto');
    }
}
