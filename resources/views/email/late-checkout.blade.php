<h1>Timbratura da convalidare</h1>

<p>
  L'utente <strong>{{ $punch->user->name }} {{$punch->user->surname}}</strong> ha effettuato il check-out in un giorno successivo all'ultimo check-in :
</p>

<ul>
  <li><strong>Check-in:</strong> {{ $punch->check_in }}</li>
  <li><strong>Check-out:</strong> {{ $punch->check_out }}</li>
</ul>

<p>Accedi all'app per gestire la richiesta.</p>
