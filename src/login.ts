function setFeedback(message: string, color: string = "white", type: "notice" | "warning" | "error" | "normal" = "notice") {
  var feedback = document.querySelector<HTMLParagraphElement>("#feedback");
  if (message == null) return;
  feedback.classList.remove("notice",  "warning",  "error", "normal");
  feedback.classList.add(type);
  feedback.style.color = color;
  feedback.innerText = message;
}
function switchTo(id: number) {
  switch (id) {
    case 0: {
      document.getElementById("loginform").hidden = false;
      document.getElementById("signupform").hidden = true;
      break;
    }
    case 1: {
      document.getElementById("loginform").hidden = true;
      document.getElementById("signupform").hidden = false;
      break;
    }
  }
}

function login() {
  setFeedback("Logging in...");
  let user = document.querySelector<HTMLInputElement>("#userInput").value;
  let pass = document.querySelector<HTMLInputElement>("#passwordInput").value;

  User.authorize(user, pass).then(result => {
    console.log(result);
    if (result.success) {
      let user = result.data;

      setFeedback(result.reason, "green", "notice");
      user.setAsCurrentUser();
      let params = new URLSearchParams(location.search);
      if (params.has("ref")) location.href = params.get("ref");
      else location.href = "/account/" + user.username;
    }
    else {
      setFeedback(result.reason, "red", "error");
    }
  })
}

function register() {
  setFeedback("Checking database...");
  let user = document.querySelector<HTMLInputElement>("#userSignUpInput").value;
  let email = document.querySelector<HTMLInputElement>("#emailSignUpInput").value;
  let pass = document.querySelector<HTMLInputElement>("#passwordSignUpInput").value;
  let confPass = document.querySelector<HTMLInputElement>("#confirmPasswordSignUpInput").value;

  var formdata = new FormData();
  formdata.append("username", user);
  formdata.append("email", email);
  formdata.append("password", pass);
  formdata.append("confirmPassword", confPass);

  var requestOptions = {
    method: 'POST',
    body: formdata,
  };

  fetch("/src/register.php", requestOptions)
  .then(response => response.json())
  .then((result: Result<UserSQL>) => {
    if (result.success) {
      setFeedback(result.reason, "green", "notice");
      document.querySelector<HTMLInputElement>("#userInput").value = user;
      document.querySelector<HTMLInputElement>("#passwordInput").value = pass;
      login();
      // Should redirect
    }
    else {
      setFeedback(result.reason, "red", "error");
    }
  })
  .catch(error => {
    console.error(error);
    setFeedback(error, "red", "error");
  });
}