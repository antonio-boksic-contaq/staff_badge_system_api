<style>
  @font-face {
    font-family: 'Poppins';
    src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
    font-style: normal; // use the matching font-style here
  }
  
  @font-face {
    font-family: 'Poppins-SemiBold';
    src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
    font-style: normal; // use the matching font-style here
  }
  
  html { font-size: 10px; }
  .text-center{ text-align: center; }
  .text-left{ text-align: left; }
  .list-style-none { list-style: none; }
  .mw-500 { max-width: 50rem; }
  .card { border-radius: .3rem; padding: 2rem; }
  .shadow { box-shadow: #d5d5d5 0 0 .5rem .2rem }
  .mx-auto { margin-right: auto; margin-left: auto; }
  .mt-5 { margin-top: 5rem }
  .p { font-size: 1.6rem }
  .header {
    background-image: url("http://localhost8081:img/contract_header.png");
    background-position: center;
    background-size: contain;
    background-repeat: no-repeat;
    height: 5.2rem;
    width: 100%;
  }
  blockquote{
    padding: .8rem;
    background: #f1f1f1;
    border-radius: .2rem;
    border-left: .3rem solid #e42616;
    margin-top: 2rem;
    margin-bottom: 2rem;
  }
</style>
<div class="mw-500 card shadow mx-auto mt-5">
  <div class="header"></div>
  <p>
    Ciao {{ $user->name }}, la procedura per il recupero delle credenziali è andata a buon fine.
    <br />
    Utilizza le seguenti credenziali per accedere al di timbratura digitale.
  </p>
  <blockquote>
    <ul class="text-center list-style-none">
      <li><strong>Email</strong> {{ $user->email }}</li>
      <li><strong>Password</strong> {{ $password }}</li>
    </ul>
  </blockquote>
  <p class="text-left">
    Grazie, <br />
    assistenza ContaQ
  </p>
</div>