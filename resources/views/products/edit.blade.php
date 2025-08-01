<x-dashboard-layout>
    <main class="container page dashboard product-form">
        <h2>Product bewerken</h2>
        @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close"
                onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        @endif

        <form action="{{ route('productUpdate', $product->id) }}" method="POST" class="form" enctype="multipart/form-data">
            @method('PUT')  
            @csrf						
            <div class="grid-box">
                {{-- Beschrijving --}}
                <div class="section">
                    <div class="form-input">
                        <label for="is_published">Publiceren</label>
                        <select name="is_published" id="is_published">
                            <option value="0" {{ old('is_published', $product->is_published) == '0' ? 'selected' : '' }}>Nee</option>
                            <option value="1" {{ old('is_published', $product->is_published) == '1' ? 'selected' : '' }}>Ja</option>
                        </select>
                        @error('is_published')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="title">Titel</label>
                        <input type="text" name="title" value="{{ old('title', $product->title) }}">
                        @error('title')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="short_description">Korte omschrijving</label>
                        <textarea class="short_description"
                            name="short_description">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="long_description">Lange omschrijving</label>
                        <textarea class="long_description" name="long_description">{{ old('long_description', $product->long_description) }}</textarea>
                        @error('long_description')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section">
                    <div class="form-input">
                        <label for="price">Prijs</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01">
                        @error('price')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="stock">Voorraad</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}">
                        @error('stock')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="category">Categorie</label>
                        <select name="category_id" id="category_id">
                            <option value="">-- Kies categorie --</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                            @foreach ($products as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $product->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @error('parent_id')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Dimensions and weight --}}
                <div class="section">
                    <div class="form-input">
                        <label for="weight">Gewicht (gr.)</label>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight) }}">
                        @error('weight')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="height">Hoogte (cm)</label>
                        <input type="number" name="height" value="{{ old('height', $product->height) }}">
                        @error('height')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="width">Breedte (cm)</label>
                        <input type="number" name="width" value="{{ old('width', $product->width) }}">
                        @error('width')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-input">
                        <label for="depth">Diepte (cm)</label>
                        <input type="number" name="depth" value="{{ old('depth', $product->depth) }}">
                        @error('depth')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Images --}}
                <div class="section images">

                @for ($i = 1; $i <= 4; $i++)
                <div class="form-input">
                    <label for="image_{{ $i }}">
                        @if($i == 1)
                            Hoofdafbeelding
                        @else
                            Afbeelding {{ $i }}
                        @endif
                    </label>
                    <div class="custom-file-input-wrapper">
                        <input type="file" name="image_{{ $i }}" id="image_{{ $i }}" accept="image/*" class="custom-file-input">
                        <label for="image_{{ $i }}" class="custom-file-label">
                            <span id="image_{{ $i }}_label_text">Kies afbeelding...</span>
                        </label>

                        {{-- Preview afbeelding --}}
                        <div id="image_{{ $i }}_preview" style="display: flex; align-items:center;margin-top:5px;">
                            @if($product->{'image_'.$i})
                                <img 
                                    src="{{ Str::startsWith($product->{'image_'.$i}, 'https://') 
                                        ? $product->{'image_'.$i} 
                                        : asset('storage/' . $product->{'image_'.$i}) }}" 
                                    alt="" 
                                    style="max-width:60px;max-height:60px;">
                            @endif
                        </div>

                        <button type="button" class="remove-image-btn"
                            data-input="image_{{ $i }}"
                            data-label="image_{{ $i }}_label_text"
                            data-preview="image_{{ $i }}_preview"
                            style="{{ $product->{'image_'.$i} ? '' : 'display:none;' }}">
                            Verwijder
                        </button>
                        <input type="checkbox" name="delete_image_{{ $i }}" id="delete_image_{{ $i }}" value="1" style="display:none;">
                    </div>
                    @error('image_' . $i)
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                @endfor

                </div>

            </div>

            <div class="form-input">
                <button type="submit" class="btn"><span class="loader"></span>Opslaan</button>
            </div>
        </form>
    </main>
</x-dashboard-layout>