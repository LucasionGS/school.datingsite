{
  let user = User.currentUser;
  if (user) {
    let button = document.querySelector("#loginButton");
    button.innerText = user.username;
    button.parentElement.href = "/account/" + user.username;
    document.querySelector("#loginButton").innerText = user.username;
  }
}