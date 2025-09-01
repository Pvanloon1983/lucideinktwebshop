<x-dashboard-layout>
  <main class="container page dashboard">
    <h2>Kortingscodes</h2>
    @if(session('success'))
      <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
      </div>
    @endif
    <a href="{{ route('discountCreate') }}"><button class="btn">Nieuwe toevoegen</button></a>

    <div class="table-wrapper">
      <table class="table">
        <thead>
        <tr>
          <th>ID</th>
          <th>Code</th>
          <th>Type</th>
          <th>Korting</th>
          <th>Gebruikslimit</th>
          <th>Gebruikslimit per klant</th>
          <th>Gepubliceerd</th>
          <th>Aangemaakt</th>
          <th>Vervaldatum</th>
          <th>Actie</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($discountCodes as $discountCode)
          <tr>
            <td>{{ $discountCode->id }}</td>
            <td style="min-width:140px;">{{ $discountCode->code }}</td>
            <td style="min-width:100px;">{{ $discountCode->discount_type == 'percent' ? 'Procent' : 'Bedrag' }}</td>

            <td style="min-width:100px;">
              @if($discountCode->discount_type == 'percent')
                {{ (int)$discountCode->discount }}%
              @else
                € {{ number_format($discountCode->discount, 2, ',', '.') }}
              @endif
            </td>

            <td style="min-width:100px;">{{ $discountCode->usage_limit }}</td>
            <td style="min-width:100px;">{{ $discountCode->usage_limit_per_customer }}</td>

            <td style="min-width:90px;">
              @if ($discountCode->is_published == 1)
                ja
              @else
                nee
              @endif
            </td>
            <td style="min-width:110px;">{{ $discountCode->created_at ? $discountCode->created_at->format('d-m-Y H:i') : '-' }}</td>
            <td style="min-width:110px;">{{ $discountCode->expiration_date ? \Carbon\Carbon::parse($discountCode->expiration_date)->format('d-m-Y') : '-' }}</td>
            <td class="table-action" style="min-width:80px;">
              <a href="{{ route('discountEdit', $discountCode->id) }}"><i
                  class="fa-regular fa-pen-to-square edit action-btn"></i></a>
              <form action="{{ route('discountDelete', $discountCode->id) }}" method="POST" class="needs-confirm" data-confirm="Weet je zeker dat je deze kortingscode wilt verwijderen?" data-confirm-title="Kortingscode verwijderen">
                @csrf
                @method('DELETE')
                <button style="background-color: transparent; border: none;padding: 0;" type="submit"><i
                    class="fa-regular fa-trash-can delete action-btn"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="table-empty-state">Geen kortingscodes gevonden.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
      {{ $discountCodes->links('vendor.pagination.custom') }}
    </div>

  </main>
</x-dashboard-layout>