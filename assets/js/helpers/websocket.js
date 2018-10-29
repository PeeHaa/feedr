import WebSocket from "../connection/WebSocket";

export default function() {
    const connectionDetails = JSON.parse(document.getElementsByTagName('body')[0].dataset.websocket);

    return new WebSocket(
        connectionDetails.internalHostname,
        connectionDetails.internalPort,
        connectionDetails.exposeInternalAddress
    );
}
