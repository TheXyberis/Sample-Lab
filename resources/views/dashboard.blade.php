<h1>Dashboard</h1>
<p>Witaj, {{ Auth::user()->name }}!</p>
<p>Twoja rola: {{ Auth::user()->getRoleNames()->first() }}</p>