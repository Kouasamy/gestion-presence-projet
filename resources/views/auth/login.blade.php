@extends('layouts.auth')
@section('content')
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
  @endsection

