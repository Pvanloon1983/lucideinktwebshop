<x-dashboard-layout>
  <main class="container page dashboard">
    <h2>Gebruikers</h2>
    @if(session('success'))
      <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
      </div>
    @endif
    <a href="{{ route('userCreate') }}"><button class="btn">Gebruiker toevoegen</button></a>
    <div class="table-wrapper">
      <table class="table">
        <thead>
        <tr>
          <th>ID</th>
          <th>Naam</th>
          <th>Datum geregistreerd</th>
          <th>E-mail</th>
          <th>Rol</th>
          <th>Actie</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($users as $user)
          <tr style="cursor: pointer;" onclick="window.location='{{ route('userShow', $user->id) }}'">
            <td>{{ $user->id }}</td>
            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>{{ $user->created_at->format('d-m-Y H:i') }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td class="table-action">
              <a href="{{ route('userShow', $user->id)  }}">
                <i class="fas fa-eye show"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 20px;">
              Geen gebruikers gevonden.
            </td>
          </tr>
        @endforelse

        </tbody>
      </table>
      {{ $users->links('vendor.pagination.custom') }}
    </div>

  </main>
</x-dashboard-layout>