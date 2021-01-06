let username = location.pathname.split("/").pop();
let promiseProfile = Profile.getProfile(username)
.then(d => d.data);

addEventListener("load", async () => {
  const profile = await promiseProfile;
  if (profile !== null) {
    document.getElementById("bio").innerText = profile.bio;
  }
})