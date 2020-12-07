interface UserSQL {
  id: number,
  username: string,
  email: string,
  accessToken: string,
}

interface ProfileSQL {
  id: number;
  fullName: string;
  likedBy: string;
  pfp: string;
  bio: string;
  birthdate: {
    date: string;
    timezone: string;
    timezone_type: number;
  }
  height: number;
  weight: number;
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
  private profile: Profile = null;
  public async getProfile(): Promise<Profile> {
    if (this.profile instanceof Profile) return this.profile;
    return (this.profile = await Profile.getProfile(this.id).then(res => res.success ? res.data : null));
  }
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
    if (this. _curUsr instanceof User) {
      return this. _curUsr;
    }
    return (this. _curUsr = (localStorage.getItem("user") != null ? User.mapToUser(JSON.parse(localStorage.getItem("user"))) : null));
  }
  public static set currentUser(user) {
    User.setCurrentUser(user);
  }

  private static _curUsr: User = null;
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
    /**
     * Name of the profile picture. All profile pictures are stored in /img/pfp/
     */
    public pfp: string,
    public bio: string,
    public birthdate: Date,
    public height: number,
    public weight: number,
  ) { }

  public get age() {
    return new Date().getFullYear() - this.birthdate.getFullYear();
  }

  public createInfoCard() {
    const div = document.createElement("div");

    div.classList.add("infocard");
    const pfp = document.createElement("img");
    pfp.src = this.pfp;

    div.appendChild(pfp);

    return div;
  }

  public static mapToProfile(data: ProfileSQL) {
    const likedBy = data.likedBy.split(",").map(i => +i);
    
    var t = data.birthdate.date.split(/[- :]/) as unknown as number[];
    const birthdate = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));

    return new Profile(
      data.id,
      data.fullName,
      likedBy,
      "/img/pfp/" + (data.pfp || "__default.png"),
      data.bio,
      birthdate,
      data.height,
      data.weight,
    );
  }

  public static async getProfile(username: string): Promise<Result<Profile>>;
  public static async getProfile(usernames: string[]): Promise<Result<Profile[]>>;
  public static async getProfile(id: number): Promise<Result<Profile>>;
  public static async getProfile(ids: number[]): Promise<Result<Profile[]>>;
  public static async getProfile(id: number | string | number[] | string[]) {
    var formdata = new FormData();
    var requestOptions = {
      method: 'POST',
      body: formdata
    };
    let key = typeof id == "string" ? "username" : "id"
    if (Array.isArray(id)) formdata.append(key, id.join(","));
    else formdata.append(key, id.toString());

    return fetch("/api/getProfile.php", requestOptions)
      .then(response => response.json())
      .then((result: Result<ProfileSQL | ProfileSQL[]>) => {
        if (result.success) {
          let res: Profile | Profile[];
          if (Array.isArray(result.data)) {
            res = result.data.map(data => Profile.mapToProfile(data))
          }
          else {
            res = Profile.mapToProfile(result.data);
          }
          let data: Result<Profile | Profile[]> = {
            success: result.success,
            data: res,
            reason: result.reason
          }
          return data;
        }
        else {
          let data: Result<Profile> = {
            success: result.success,
            data: null,
            reason: result.reason
          }
          return data;
        }
      });
  }
}

