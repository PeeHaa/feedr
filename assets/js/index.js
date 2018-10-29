import 'bootstrap';
import './../scss/app.scss';

import HandleSearchUsers from "./components/administrator/HandleSearchUsers";
import HandleSearchRepositories from "./components/repository/HandleSearchRepositories";
import LivePreview from "./components/feed/LivePreview";
import ConfirmFeedDeleteModal from "./components/feed/ConfirmFeedDeleteModal";
import ConfirmAdministratorDeleteModal from "./components/administrator/ConfirmAdministratorDeleteModal";
import ConfirmRepositoryDeleteModal from "./components/repository/ConfirmRepositoryDeleteModal";

const livePreview = new LivePreview();

document.querySelectorAll('form.searchUsers').forEach((form) => {
    new HandleSearchUsers(form);
});

document.querySelectorAll('form.searchRepositories').forEach((form) => {
    new HandleSearchRepositories(form, livePreview);
});

new ConfirmFeedDeleteModal();
new ConfirmAdministratorDeleteModal();
new ConfirmRepositoryDeleteModal(livePreview);
