import LoggingWebSocket from "./LoggingWebSocket";

export default class {
    constructor(hostname, port, expose) {
        this.hostname = hostname;
        this.port     = port;
        this.expose   = expose;

        this.connection = null;
        this.connected  = false;

        this.debug = true;
    }

    connect(onOpen, onMessage) {
        //this.connection = new WebSocket(this.buildConnectionString());
        this.connection = new LoggingWebSocket(this.buildConnectionString());

        this.connection.addEventListener('open', (e) => {
            this.connected = true;

            onOpen(e);
        });

        this.connection.addEventListener('message', (e) => {
            onMessage(JSON.parse(e.data));
        });
    }

    buildConnectionString() {
        if (this.expose) {
            return 'ws://' + this.hostname + ':' + this.port + '/live-releases';
        }

        let connectionString = 'ws';

        if (window.location.protocol === 'https:') {
            connectionString += 's'
        }

        connectionString += '://' + window.location.host;

        if (window.location.port) {
            connectionString += ':' + window.location.port;
        }

        return connectionString  + '/live-releases';
    }

    subscribeToChannel(identifier) {
        if (!this.connected) {
            console.warn('Cannot subscribe to channels when the connection isn\'t open yet. Subscribe in the onOpen callback instead.');
        }

        this.connection.send(JSON.stringify({
            command: 'subscribe',
            channel: identifier
        }));
    }

    unsubscribeFromChannel(identifier) {
        if (!this.connected) {
            console.warn('Cannot subscribe to channels when the connection isn\'t open yet. Subscribe in the onOpen callback instead.');
        }

        this.connection.send(JSON.stringify({
            command: 'unsubscribe',
            channel: identifier
        }));
    }
}
