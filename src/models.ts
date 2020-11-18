interface UserSQL {
  id: number,
  username: string,
  email: string,
  accessToken: string,
}

class Result<DataType = any>
{
  public success: boolean;
  public reason: string = null;
  public data: DataType = null;
  constructor(success: boolean, reason: string = null, data: DataType = null) {
    this.success = success;
    if (reason != null) this.reason = reason;

    if (data != null) {
      this.data = data;
    }
  }
}

class User {
  constructor(
    public id: number,
    public username: string,
    public email: string,
    public accessToken: string,
  ) { }

  static async authorize(token: string): Promise<Result<User>>;
  static async authorize(user: string, pass: string): Promise<Result<User>>;
  static async authorize(userOrToken: string, pass: string = null): Promise<Result<User>> {
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
      .then((result: Result<UserSQL>) => {
        if (result.success) {
          let data: Result<User> = {
            success: result.success,
            data: User.mapToUser(result.data),
            reason: result.reason
          }
          return data;
        }
        else {
          let data: Result<User> = {
            success: result.success,
            data: null,
            reason: result.reason
          }
          return data;
        }
      });
  }

  public static mapToUser(data: UserSQL) {
    return new User(
      data.id,
      data.username,
      data.email,
      data.accessToken,
    );
  }

  /**
   * Sets this user as the currently logged in user.
   */
  public setAsCurrentUser() {
    User.setCurrentUser(this);
  }

  /**
   * Sets the passed user as the currently logged in user.
   */
  public static setCurrentUser(user: User) {
    localStorage.setItem("user", JSON.stringify(user));
  }

  /**
   * Get or set the current user.
   */
  public static get currentUser(): User {
    return localStorage.getItem("user") != null ? User.mapToUser(JSON.parse(localStorage.getItem("user"))) : null;
  }
  public static set currentUser(user) {
    User.setCurrentUser(user);
  }
}

class Profile {
  constructor(
    public id: number,
    /**
     * The full name of the profile.
     */
    public fullName: string,
    /**
     * A list of IDs of the users who liked this profile.
     */
    public likedBy: number[],
  ) { }

  
}