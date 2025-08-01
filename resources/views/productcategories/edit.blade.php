<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Categorie bijwerken</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

			<form action="{{ route('productCategoryUpdate', $category->id) }}" method="POST" class="form profile">
				@method('PUT')
        @csrf
        <div class="form-input">
          <label for="name">Naam</label>
						<input type="text" name="name" value="{{ old('name', $category->name) }}">
          @error('name')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-input">
          <label for="is_published">Publiceren</label>
						<select name="is_published" id="is_published">
							<option value="0" {{ old('is_published', $category->is_published) == 0 ? 'selected' : '' }}>Nee</option>
							<option value="1" {{ old('is_published', $category->is_published) == 1 ? 'selected' : '' }}>Ja</option>
						</select>
          @error('is_published')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-input">
          <button type="submit" class="btn"><span class="loader"></span>Opslaan</button>
        </div>
      </form>

</main>
</x-dashboard-layout>