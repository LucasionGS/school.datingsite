"use strict";
addEventListener("load", () => {
    let username = location.pathname.split("/").pop();
    if (username) {
        Profile.getProfile(username);
    }
});
