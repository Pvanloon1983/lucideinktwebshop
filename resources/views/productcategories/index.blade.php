<x-dashboard-layout>
<main class="container page dashboard">
		<h2>Productcategorieën</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif
		<a href="{{ route('productCategoryCreatePage') }}"><button class="btn">Nieuwe toevoegen</button></a>
    <div class="table-wrapper">
        <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
								<th>Slug</th>
                <th>Gepubliceerd</th>
                <th>Datum</th>
								<th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productCategories as $productCategory)
            <tr>
                <td>{{ $productCategory->id }}</td>
                <td style="min-width:180px;">{{ $productCategory->name }}</td>
                <td style="min-width:180px;">{{ $productCategory->slug }}</td>
                <td style="min-width:90px;">
                    @if ($productCategory->is_published == 1)
                        ja
                    @else
                        nee
                    @endif
                </td>
                <td style="min-width:110px;">{{ $productCategory->created_at->format('d-m-Y') }}</td>
								<td class="table-action" style="min-width:80px;">
									<a href="{{ route('productCategoryEditPage', $productCategory->id) }}"><i class="fa-regular fa-pen-to-square edit action-btn"></i></a>
									<form action="{{ route('productCategoryDelete', $productCategory->id) }}" method="POST">
										@csrf
										@method('DELETE')
										<button onclick="return confirm('Weet je zeker dat je dit wilt verwijderen?');" style="background-color: transparent; border: none;padding: 0;" type="submit"><i class="fa-regular fa-trash-can delete action-btn"></i></button>
									</form>
								</td>
            </tr>
            @empty
                
            @endforelse

        </tbody>
        </table>
        {{ $productCategories->links('vendor.pagination.custom') }}
    </div>

</main>
</x-dashboard-layout>