"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
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
    static authorize(userOrToken, pass = null) {
        return __awaiter(this, void 0, void 0, function* () {
            var formdata = new FormData();
            if (pass === null) {
                formdata.append("token", userOrToken);
            }
            else {
                formdata.append("username", userOrToken);
                formdata.append("password", pass);
            }
            var requestOptions = {
                method: 'POST',
                body: formdata
            };
            return fetch("/src/authorize.php", requestOptions)
                .then(response => response.json())
                .then((result) => {
                if (result.success) {
                    let data = {
                        success: result.success,
                        data: User.mapToUser(result.data),
                        reason: result.reason
                    };
                    return data;
                }
                else {
                    let data = {
                        success: result.success,
                        data: null,
                        reason: result.reason
                    };
                    return data;
                }
            });
        });
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
class Profile {
    constructor(id, 
    /**
     * The full name of the profile.
     */
    fullName, 
    /**
     * A list of IDs of the users who liked this profile.
     */
    likedBy) {
        this.id = id;
        this.fullName = fullName;
        this.likedBy = likedBy;
    }
}
