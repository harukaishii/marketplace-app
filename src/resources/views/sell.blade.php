@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
    <main class="main">
        <div class="container">
            <h2 class="page-title">商品の出品</h2>
            <form class="sell-form" action="{{route('sell.store')}}" method="POST" enctype="multipart/form-data">
                @csrf

                <section class="form-section">
                    <h3 class="form-section__title">商品画像</h3>
                    <div class="image-upload-area">
                        <input type="file" id="productImage" class="image-upload-input" name="product_image" accept="image/*">
                        <label for="productImage" class="image-upload-label">
                            画像を選択する
                        </label>
                        <div class="image-preview" id="imagePreview"></div>
                        @error('product_image')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </section>

                <section class="form-section">
                    <h3 class="form-section__title">商品の詳細</h3>
                    <div class="form-group">
                        <label class="form-label">カテゴリー</label>
                        <div class="category-tags">
                            @foreach($categories as $category)
                                <label class="category-checkbox-label">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="category-checkbox-input" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                    <span class="category-tag category-tag-checkbox">{{ $category->name }}</span>
                                </label>
                            @endforeach
                            @error('category_ids')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="itemCondition" class="form-label">商品の状態</label>
                        <select id="itemCondition" class="form-select" name="condition">
                            <option value="">選択してください</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->value }}" {{ old('condition') == $condition->value ? 'selected' : '' }}>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                        @error('condition')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </section>

                <section class="form-section">
                    <h3 class="form-section__title">商品名と説明</h3>
                    <div class="form-group">
                        <label for="productName" class="form-label">商品名</label>
                        <input type="text" id="productName" class="form-input" name="name" value="{{old('name')}}">
                        @error('name')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="brandName" class="form-label">ブランド名</label>
                        <input type="text" id="brandName" class="form-input" name="brand_name" value="{{old('brand_name')}}">
                        @error('brand_name')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="productDescription" class="form-label">商品の説明</label>
                        <textarea id="productDescription" class="form-textarea" name="detail">
                            {{ old('detail') }}
                        </textarea>
                        @error('detail')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </section>

                <section class="form-section">
                    <h3 class="form-section__title">販売価格</h3>
                    <div class="form-group price-input-group">
                        <span class="price-currency">¥</span>
                        <input type="number" id="sellingPrice" class="form-input price-input" name="price" placeholder="0" value="{{old('price')}}">
                        @error('price')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </section>

                <button type="submit" class="submit-button">出品する</button>
            </form>
        </div>
    </main>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productImageInput = document.getElementById('productImage');
            const imagePreview = document.getElementById('imagePreview');

            productImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `<img src="${e.target.result}" alt="Product Image Preview" style="max-width: 100%; height: auto;">`;
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreview.innerHTML = '';
                }
            });
        });
    </script>
@endsection
@endsection
