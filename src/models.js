"use strict";
class Result {
    constructor(success, reason = null, data = null) {
        this.reason = null;
        this.data = null;
        this.success = success;
        if (reason != null)
            this.reason = reason;
        if (data != null) {
            this.data = data;
        }
    }
}
class User {
    constructor(id, username, email, accessToken) {
        this.id = id;
        this.username = username;
        this.email = email;
        this.accessToken = accessToken;
    }
    static mapToUser(data) {
        return new User(data.id, data.username, data.email, data.accessToken);
    }
    /**
     * Sets this user as the currently logged in user.
     */
    setAsCurrentUser() {
        User.setCurrentUser(this);
    }
    /**
     * Sets the passed user as the currently logged in user.
     */
    static setCurrentUser(user) {
        localStorage.setItem("user", JSON.stringify(user));
    }
    /**
     * Get or set the current user.
     */
    static get currentUser() {
        return localStorage.getItem("user") != null ? User.mapToUser(JSON.parse(localStorage.getItem("user"))) : null;
    }
    static set currentUser(user) {
        User.setCurrentUser(user);
    }
}
