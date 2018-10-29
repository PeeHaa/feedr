import 'bootstrap';
import './../scss/app.scss';

import HandleSearchUsers from "./components/administrator/HandleSearchUsers";
import HandleSearchRepositories from "./components/repository/HandleSearchRepositories";
import LivePreview from "./components/feed/LivePreview";

const livePreview = new LivePreview();

document.querySelectorAll('form.searchUsers').forEach((form) => {
    new HandleSearchUsers(form);
});

document.querySelectorAll('form.searchRepositories').forEach((form) => {
    new HandleSearchRepositories(form, livePreview);
});
