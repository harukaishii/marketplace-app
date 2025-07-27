@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="edit-form__heading">
        <h2>プロフィール設定</h2>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- formタグに enctype="multipart/form-data" を追加 --}}
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="profile-image__container">
    <div class="profile-image__wrapper">
        @if (empty($user->image))
            <div class="profile-image__placeholder"></div>
        @else
            <img src="{{ Storage::url($user->image) }}" alt="Current Profile Image" class="profile-image__actual">
        @endif
    </div>

    <div class="profile-image__input-group">
        <label for="image_file" class="custom-file-upload">
            画像を選択する
        </label>
        <input type="file" id="image_file" name="image_file" style="display: none">
    </div>
</div>

        <div class="form__group">
            <div class="form___group-title">
                <label for="name">ユーザー名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form__error">
                    @error('name')
                    {{message}}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form___group-title">
                <label for="post">郵便番号</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" class="form-control" id="post" name="post" value="{{ old('post', $user->address->post ?? '') }}">
                </div>
                <div class="form__error">
                    @error('post')
                    {{message}}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form___group-title">
                <label for="address">住所</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address->address ?? '') }}">
                </div>
                <div class="form__error">
                    @error('address')
                    {{message}}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form___group-title">
                <label for="building">建物名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" class="form-control" id="building" name="building" value="{{ old('building', $user->address->building ?? '') }}">
                </div>
                <div class="form__error">
                    @error('building')
                    {{message}}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit" >更新する</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('image_file');
        const imagePreview = document.querySelector('.profile-image__actual');
        const placeholder = document.querySelector('.profile-image__placeholder');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    // プレビュー画像を表示
                    if (imagePreview) {
                        imagePreview.src = event.target.result;
                        imagePreview.style.display = 'block';
                    } else {
                        const newImage = document.createElement('img');
                        newImage.src = event.target.result;
                        newImage.alt = "Selected Image Preview";
                        newImage.classList.add('profile-image__actual');
                        document.querySelector('.profile-image__wrapper').appendChild(newImage);
                    }
                    // プレースホルダーを非表示に
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection
