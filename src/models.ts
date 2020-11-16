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