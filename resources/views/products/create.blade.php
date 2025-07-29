<x-dashboard-layout>
<main class="container page dashboard product-form">
    <h2>Product aanmaken</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

    <form action="{{ route('productStore') }}" method="POST" class="form" enctype="multipart/form-data">
        @csrf
        <div class="grid-box">
            {{-- Beschrijving --}}
            <div class="section">
								<div class="form-input">
                    <label for="is_published">Publiceren</label>
                    <select name="is_published" id="is_published">
                        <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Nee</option>
                        <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>Ja</option>
                    </select>
                    @error('is_published')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
							  <div class="form-input">
                    <label for="title">Titel</label>
                    <input type="text" name="title" value="{{ old('title') }}">
                    @error('title')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="short_description">Korte omschrijving</label>
                    <textarea style="height: 75px" name="short_description">{{ old('short_description') }}</textarea>
                    @error('short_description')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="long_description">Lange omschrijving</label>
                    <textarea style="height: 150px" name="long_description">{{ old('long_description') }}</textarea>
                    @error('long_description')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

						<div class="section">
								<div class="form-input">
                    <label for="price">Prijs</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01">
                    @error('price')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
								<div class="form-input">
                    <label for="category">Categorie</label>

								<select name="category_id" id="category_id">
										<option value="">-- Kies categorie --</option>
										@foreach ($categories as $category)
												<option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
														{{ $category->name }}
												</option>
										@endforeach
								</select>
										
                    @error('category_id')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
								<div class="form-input">
                    <label for="parent_id">Hoofdproduct (optioneel)</label>
                    <select name="parent_id" id="parent_id">
												<option value="">-- Kies hoofdproduct --</option>
												@if (count($products) > 0)
													@foreach ($products as $product)
														<option value="{{ $product->id }}" {{ old('parent_id') == $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
													@endforeach
												@endif
                    </select>
                    @error('parent_id')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
						</div>

            {{-- Afmetingen --}}
            <div class="section">
                <div class="form-input">
                    <label for="weight">Gewicht (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight') }}" step="0.01">
                    @error('weight')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="height">Hoogte (cm)</label>
                    <input type="number" name="height" value="{{ old('height') }}" step="0.01">
                    @error('height')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="width">Breedte (cm)</label>
                    <input type="number" name="width" value="{{ old('width') }}" step="0.01">
                    @error('width')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="depth">Diepte (cm)</label>
                    <input type="number" name="depth" value="{{ old('depth') }}" step="0.01">
                    @error('depth')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Afbeeldingen --}}
            <div class="section">
                <div class="form-input">
                    <label for="image_1">Hoofdafbeelding</label>
                    <input type="file" name="image_1" id="image_1" accept="image/*">
                    @error('image_1')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="image_2">Afbeelding 2</label>
                    <input type="file" name="image_2" id="image_2" accept="image/*">
                    @error('image_2')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="image_3">Afbeelding 3</label>
                    <input type="file" name="image_3" id="image_3" accept="image/*">
                    @error('image_3')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-input">
                    <label for="image_4">Afbeelding 4</label>
                    <input type="file" name="image_4" id="image_4" accept="image/*">
                    @error('image_4')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>

        <div class="form-input">
            <button type="submit" class="btn">Verzenden</button>
        </div>
    </form>
</main>
</x-dashboard-layout>