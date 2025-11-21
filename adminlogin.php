<?php
        session_start();
       
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Animation Login Form</title>
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css"
    />
    <link rel="stylesheet" href="assets/css/base.css" />
    <!-- <link rel="stylesheet" href="assets/css/style.css" /> -->
    <title>Admin</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap");
      :root {
        --white-color: hsl(0, 0%, 100%);
        --black-color: hsl(0, 0%, 0%);
        --body-font: "Poppins", sans-serif;
        --h1-font-size: 1.75rem;
        --normal-font-size: 1rem;
        --small-font-size: 0.813rem;
        --font-medium: 500;
      }

      * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
      }

      body,
      input,
      button {
        font-size: var(--normal-font-size);
        font-family: var(--body-font);
      }

      body {
        color: var(--white-color);
      }

      input,
      button {
        border: none;
        outline: none;
      }

      a {
        text-decoration: none;
      }

      img {
        max-width: 100%;
        height: auto;
      }

      .login {
        position: relative;
        height: 100vh;
        display: grid;
        align-items: center;
      }

      .login__img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
      }

      .logo {
        text-align: center;
      }

      .logo img {
        width: 120px;
        height: 120px;
        object-fit: cover;
      }

      .login__form {
        position: relative;
        background-color: hsla(0, 0%, 10%, 0.1);
        border: 2px solid var(--white-color);
        margin-inline: 1.5rem;
        padding: 2.5rem 1.5rem;
        border-radius: 1rem;
        backdrop-filter: blur(8px);
      }

      .login__title {
        text-align: center;
        font-size: var(--h1-font-size);
        font-weight: var(--font-medium);
        margin-bottom: 2rem;
      }

      .login__content,
      .login__box {
        display: grid;
      }

      .login__content {
        row-gap: 1.75rem;
        margin-bottom: 1.5rem;
      }

      .login__box {
        grid-template-columns: max-content 1fr;
        align-items: center;
        column-gap: 0.75rem;
        border-bottom: 2px solid var(--white-color);
      }

      .login__icon,
      .login__eye {
        font-size: 1.25rem;
      }

      .login__input {
        width: 100%;
        padding-block: 0.8rem;
        background: none;
        color: var(--white-color);
        position: relative;
        z-index: 1;
      }

      .login__box-input {
        position: relative;
      }

      .login__label {
        position: absolute;
        left: 0;
        top: 13px;
        font-weight: var(--font-medium);
        transition: top 0.3s, font-size 0.3s;
      }

      .login__eye {
        position: absolute;
        right: 0;
        top: 18px;
        z-index: 10;
        cursor: pointer;
      }

      .login__box:nth-child(2) input {
        padding-right: 1.8rem;
      }

      .login__check,
      .login__check-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .login__check {
        margin-bottom: 1.5rem;
      }

      .login__check-label,
      .login__forgot,
      .login__register {
        font-size: var(--small-font-size);
      }

      .login__check-group {
        column-gap: 0.5rem;
      }

      .login__check-input {
        width: 16px;
        height: 16px;
      }

      .login__forgot {
        color: var(--white-color);
      }

      .login__forgot:hover {
        text-decoration: underline;
      }

      .login__button {
        width: 100%;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: var(--white-color);
        font-weight: var(--font-medium);
        cursor: pointer;
        margin-bottom: 2rem;
      }

      .login__register {
        text-align: center;
      }

      .login__register a {
        color: var(--white-color);
        font-weight: var(--font-medium);
      }

      .login__register a:hover {
        text-decoration: underline;
      }

      .login__input:focus + .login__label {
        top: -12px;
        font-size: var(--small-font-size);
      }

      .login__input:not(:placeholder-shown):not(:focus) + .login__label {
        top: -12px;
        font-size: var(--small-font-size);
      }

      .error-message {
        color: #ff3333;
        margin-bottom: 1rem;
        font-size: 15px;
      }

      @media screen and (min-width: 576px) {
        .login {
          justify-content: center;
        }
        .login__form {
          width: 432px;
          padding: 4rem 3rem 3.5rem;
          border-radius: 1.5rem;
        }
        .login__title {
          font-size: 2rem;
        }
      }
    </style>
  </head>
  <body>
    <div class="login">
      <img src="assets/img/banner.jpg" alt="login image" class="login__img" />

      <form action="login_process.php" method="POST" class="login__form">
        <div class="logo">
          <img src="assets/img/logo.png" />
        </div>

        <h1 class="login__title">Đăng nhập quản trị</h1>

       

        <div class="login__content">
          <div class="login__box">
            <i class="bx bx-lock-alt login__icon"></i>
            <div class="login__box-input">
              <input
                type="text"
                required
                class="login__input"
                id="login-username"
                name="username"
                value=""
              />
              <label for="login-username" class="login__label">Username</label>
            </div>
          </div>

          <div class="login__box">
            <i class="ri-lock-2-line login__icon"></i>
            <div class="login__box-input">
              <input
                type="password"
                required
                class="login__input"
                id="login-pass"
                name="password"
                value=""
              />
              <label for="login-pass" class="login__label">Password</label>
              <i class="ri-eye-off-line login__eye" id="login-eye"></i>
            </div>
          </div>
        </div>

        <div class="login__check">
          <div class="login__check-group">
            <input
              type="checkbox"
              class="login__check-input"
              id="remember-me"
              name="remember"
            />
            <label for="remember-me" class="login__check-label"
              >Remember me</label
            >
          </div>
        </div>
        <?php
         if (isset($_SESSION['error'])) {
            echo '<p class="error-message">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
         ?>
        <button type="submit" class="login__button">Login</button>
      </form>
    </div>

    <script>
      // Password visibility toggle
      const showHiddenPass = (loginPass, loginEye) => {
        const input = document.getElementById(loginPass);
        const iconEye = document.getElementById(loginEye);

        if (!input || !iconEye) {
          console.error("Input or eye icon not found");
          return;
        }

        iconEye.addEventListener("click", () => {
          if (input.type === "password") {
            input.type = "text";
            iconEye.classList.add("ri-eye-line");
            iconEye.classList.remove("ri-eye-off-line");
          } else {
            input.type = "password";
            iconEye.classList.remove("ri-eye-line");
            iconEye.classList.add("ri-eye-off-line");
          }
        });
      };

      showHiddenPass("login-pass", "login-eye");

      // Remember me functionality
      function setCookie(name, value, days) {
        let expires = "";
        if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
      }

      function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1, c.length);
          }
          if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
          }
        }
        return null;
      }

      // Auto-fill username and password from cookies
      window.onload = function() {
        const username = getCookie("username");
        const password = getCookie("password");
        if (username && password) {
          document.getElementById("login-username").value = username;
          document.getElementById("login-pass").value = password;
          document.getElementById("remember-me").checked = true;
        }
      };

      // Save credentials to cookies when form is submitted
      document.querySelector(".login__form").addEventListener("submit", function() {
        if (document.getElementById("remember-me").checked) {
          const username = document.getElementById("login-username").value;
          const password = document.getElementById("login-pass").value;
          setCookie("username", username, 30); 
          setCookie("password", password, 30);
        } else {
          // Clear cookies if "Remember me" is not checked
          setCookie("username", "", -1);
          setCookie("password", "", -1);
        }
      });
    </script>
  </body>
</html>