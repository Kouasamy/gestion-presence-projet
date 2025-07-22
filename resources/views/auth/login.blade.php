<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>IFRAN Présence</title>
  <style>
    * { box-sizing: border-box; }
    body, html { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; }
    .container { display: flex; height: 100vh; width: 100vw; }
    .left-side {
      flex: 1;
      background-color: white;
      display: flex;
      flex-direction: column;
      padding: 40px 60px;
      position: relative;
      text-align: center;
    }
    .logo { width: 180px; margin-bottom: 60px; align-self: flex-start; }
    .content-wrapper {
      max-width: 350px; width: 100%; margin: auto auto;
      display: flex; flex-direction: column; align-items: center;
    }
    .title { font-size: 34px; font-weight: bold; margin-bottom: 10px; }
    .subtitle { font-size: 16px; color: #999; font-weight: lighter; margin-bottom: 40px; }
    form {
      display: flex; flex-direction: column; gap: 25px;
      width: 100%; align-items: center;
    }
    label {
      font-weight: 600; font-size: 16px; margin-bottom: 6px;
      color: #000; text-align: left; display: block; width: 100%;
    }
    input[type="email"],
    input[type="password"] {
      padding: 12px 15px; border-radius: 16px;
      height: 50px; border: 1px solid #000000;
      font-size: 14px; background-color: #D9D9D9;
      outline: none; transition: border-color 0.3s ease;
      width: 100%;
    }
    input[type="email"]:focus, input[type="password"]:focus {
      border-color: #e91e40;
    }
    .checkbox-container {
      display: flex; align-items: center;
      gap: 5px; font-size: 12px; align-self: flex-start;
    }
    .checkbox-container input[type="checkbox"] {
      width: 14px; height: 14px; cursor: pointer;
    }
    .checkbox-container label {
      cursor: pointer; font-weight: lighter; color: #000000;
    }
    .footer {
      position: absolute; bottom: 20px;
      font-size: 12px; color: #999; width: 100%;
      text-align: center; display: flex; justify-content: center;
    }
    .right-side { flex: 1; background-color: #202149; }
    .btn-submit {
      background-color: #e91e40;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      width: 80%;
    }
    .btn-submit:hover {
      background-color: #c2183b;
    }
    .error {
      color: red;
      font-size: 12px;
      margin-top: -15px;
      margin-bottom: 10px;
      align-self: flex-start;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-side">
      <img class="logo" src="https://www.ifran-ci.com/parent/img/logo-ifran-actualise.jpg" alt="IFRAN Logo" />
      <div class="content-wrapper">
        <div class="title">Bienvenue sur IFRAN Présence</div>
        <div class="subtitle">Entrez votre e-mail & votre mot de passe</div>

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div style="width: 100%;">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email')
              <div class="error">{{ $message }}</div>
            @enderror
          </div>

          <div style="width: 100%;">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required />
            @error('password')
              <div class="error">{{ $message }}</div>
            @enderror
          </div>

          <div class="checkbox-container">
            <input type="checkbox" id="remember" name="remember" />
            <label for="remember">Se souvenir de moi</label>
          </div>

          <button type="submit" class="btn-submit">Se connecter</button>
        </form>
      </div>

      <div class="footer">© Copyright 2024 | Tous droits réservés | IFRAN</div>
    </div>
    <div class="right-side"></div>
  </div>
</body>
</html>
