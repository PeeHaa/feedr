import 'bootstrap';
import './../scss/app.scss';

import HandleSearchUsers from "./components/administrator/HandleSearchUsers";

document.querySelectorAll('form.searchUsers').forEach((form) => {
    new HandleSearchUsers(form);
});
