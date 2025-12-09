@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
@endsection

@section('content')
<main class="transaction-page">
    <div class="transaction-container">
        {{-- サイドバー: その他の取引 --}}
        <aside class="transaction-sidebar">
            <h2 class="transaction-sidebar__title">その他の取引</h2>
            <div class="transaction-sidebar__list">
                @forelse ($transactionItems as $transactionItem)
                    <a href="{{ route('transactions.show', $transactionItem->id) }}"
                       class="transaction-sidebar__item {{ $transactionItem->id === $item->id ? 'transaction-sidebar__item--active' : '' }}">
                        <span class="transaction-sidebar__item-name">{{ $transactionItem->name }}</span>
                    </a>
                @empty
                    <p class="transaction-sidebar__empty">取引中の商品はありません</p>
                @endforelse
            </div>
        </aside>

        {{-- メインコンテンツ --}}
        <div class="transaction-content">
            {{-- 成功メッセージ --}}
            @if (session('success'))
                <div class="alert alert-success" id="successMessage">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ヘッダー: ユーザー名と取引完了ボタン --}}
            <div class="transaction-header">
                <h1 class="transaction-header__title">
                    「{{ $otherUser ? $otherUser->name : 'ユーザー' }}」さんとの取引画面
                </h1>
                @if ($isBuyer)
                    <button type="button" class="transaction-header__complete-btn" onclick="openRatingModal()">
                        取引を完了する
                    </button>
                @endif
            </div>

            {{-- 商品情報バー --}}
            <div class="product-info-bar">
                <div class="product-info-bar__image">
                    @if ($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                    @else
                        <div class="product-info-bar__no-image">画像なし</div>
                    @endif
                </div>
                <div class="product-info-bar__details">
                    <h2 class="product-info-bar__name">{{ $item->name }}</h2>
                    <p class="product-info-bar__price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- チャットエリア --}}
            <div class="chat-area">
                <div class="chat-messages" id="chatMessages">
                    @forelse ($messages as $message)
                        @if ($message->user_id === $user->id)
                            {{-- 自分のメッセージ（右側） --}}
                            <div class="chat-message chat-message--own">
                                <div class="chat-message__wrapper">
                                    <div class="chat-message__header">
                                        <p class="chat-message__sender">{{ $user->name }}</p>
                                        <div class="chat-message__avatar">
                                            @if ($user->image)
                                                <img src="{{ Storage::url($user->image) }}" alt="{{ $user->name }}">
                                            @else
                                                <div class="chat-message__avatar-placeholder"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-message__content">
                                        @if ($message->image)
                                            <div class="chat-message__image">
                                                <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像">
                                            </div>
                                        @endif
                                        <div class="chat-message__bubble chat-message__bubble--own">
                                            {{ $message->content }}
                                        </div>
                                    </div>
                                    <div class="chat-message__actions">
                                        <a href="#" class="chat-message__action-link" data-action="edit" data-message-id="{{ $message->id }}" data-message-content="{{ $message->content }}" data-message-image="{{ $message->image }}">編集</a>
                                        <a href="#" class="chat-message__action-link" data-action="delete" data-message-id="{{ $message->id }}">削除</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- 相手のメッセージ（左側） --}}
                            <div class="chat-message chat-message--other">
                                <div class="chat-message__wrapper">
                                    <div class="chat-message__header">
                                        <div class="chat-message__avatar">
                                            @if ($otherUser && $otherUser->image)
                                                <img src="{{ Storage::url($otherUser->image) }}" alt="{{ $otherUser->name }}">
                                            @else
                                                <div class="chat-message__avatar-placeholder"></div>
                                            @endif
                                        </div>
                                        <p class="chat-message__sender">{{ $otherUser ? $otherUser->name : 'ユーザー' }}</p>
                                    </div>
                                    <div class="chat-message__content">
                                        @if ($message->image)
                                            <div class="chat-message__image">
                                                <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像">
                                            </div>
                                        @endif
                                        <div class="chat-message__bubble chat-message__bubble--other">
                                            {{ $message->content }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="chat-messages__empty">
                            <p>まだメッセージがありません</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- メッセージ入力エリア --}}
            <div class="message-input-area">
                <form action="{{ route('transactions.messages.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
                    @csrf

                    {{-- エラーメッセージ --}}
                    @if ($errors->any())
                        <div class="message-form__errors">
                            @foreach ($errors->all() as $error)
                                <p class="error-message">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="message-form__input-group">
                        <input
                            type="text"
                            name="content"
                            class="message-form__input"
                            placeholder="取引メッセージを入力してください"
                            value="{{ old('content', session('transaction_message_draft_' . $item->id)) }}">

                        <label class="message-form__image-btn" title="画像を追加">
                            <input type="file" name="image" accept=".jpg,.jpeg,.png" style="display: none;" id="imageInput">
                            画像を追加
                        </label>

                        <button type="submit" class="message-form__submit-btn">
                            <img src="{{ asset('storage/images/send-icon.jpg') }}" alt="送信" class="message-form__send-icon">
                        </button>
                    </div>

                    <span class="message-form__filename" id="imageName"></span>
                </form>
            </div>
        </div>
    </div>
</main>

{{-- 編集モーダル --}}
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">メッセージを編集</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit-content">メッセージ内容</label>
                    <textarea id="edit-content" name="content" class="form-control" rows="4" maxlength="400" required></textarea>
                    <span class="char-count"><span id="edit-char-count">0</span>/400</span>
                </div>

                <div class="form-group">
                    <label>画像</label>
                    <div id="current-image-preview" class="image-preview" style="display: none;">
                        <img id="current-image" src="" alt="現在の画像">
                        <button type="button" class="btn-remove-image" onclick="removeCurrentImage()">画像を削除</button>
                        <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                    </div>
                    <input type="file" name="image" id="edit-image" accept=".jpg,.jpeg,.png" class="form-control">
                    <span class="image-name" id="edit-image-name"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">キャンセル</button>
                <button type="submit" class="btn btn-primary">更新</button>
            </div>
        </form>
    </div>
</div>

{{-- 削除確認モーダル --}}
<div id="deleteModal" class="modal">
    <div class="modal-content modal-content--small">
        <div class="modal-header">
            <h2 class="modal-title">メッセージを削除</h2>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p>このメッセージを削除してもよろしいですか？</p>
                <p class="text-danger">※この操作は取り消せません。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </div>
        </form>
    </div>
</div>

{{-- 評価モーダル --}}
<div id="ratingModal" class="modal">
    <div class="modal-content modal-content--rating">
        <div class="modal-header">
            <h2 class="modal-title">取引を完了する</h2>
            <button type="button" class="modal-close" onclick="closeRatingModal()">&times;</button>
        </div>
        <form action="{{ route('transactions.complete', $item) }}" method="POST">
            @csrf
            <div class="modal-body modal-body--rating">
                <p class="rating-title">取引が完了しました。</p>
                <p class="rating-description">今回の取引相手はどうでしたか？</p>

                <div class="star-rating">
                    <input type="radio" name="rating" value="5" id="star5" required>
                    <label for="star5" class="star">★</label>
                    <input type="radio" name="rating" value="4" id="star4">
                    <label for="star4" class="star">★</label>
                    <input type="radio" name="rating" value="3" id="star3">
                    <label for="star3" class="star">★</label>
                    <input type="radio" name="rating" value="2" id="star2">
                    <label for="star2" class="star">★</label>
                    <input type="radio" name="rating" value="1" id="star1">
                    <label for="star1" class="star">★</label>
                </div>
            </div>
            <div class="modal-footer modal-footer--rating">
                <button type="submit" class="btn btn-submit-rating">送信する</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // メッセージエリアを最下部にスクロール
    const messagesContainer = document.getElementById('chatMessages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // 成功メッセージの自動非表示（3秒後）
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s';
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 3000);
    }

    // 画像選択時にファイル名を表示
    const imageInput = document.getElementById('imageInput');
    const imageName = document.getElementById('imageName');

    if (imageInput && imageName) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                imageName.textContent = this.files[0].name;
            } else {
                imageName.textContent = '';
            }
        });
    }

    // 編集・削除リンクのイベントリスナー
    document.querySelectorAll('[data-action="edit"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const messageId = this.dataset.messageId;
            const content = this.dataset.messageContent;
            const image = this.dataset.messageImage;
            openEditModal(messageId, content, image);
        });
    });

    document.querySelectorAll('[data-action="delete"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const messageId = this.dataset.messageId;
            openDeleteModal(messageId);
        });
    });

    // 編集モーダルの文字数カウント
    const editContent = document.getElementById('edit-content');
    const editCharCount = document.getElementById('edit-char-count');
    if (editContent && editCharCount) {
        editContent.addEventListener('input', function() {
            editCharCount.textContent = this.value.length;
        });
    }

    // 編集モーダルの画像選択
    const editImage = document.getElementById('edit-image');
    const editImageName = document.getElementById('edit-image-name');
    if (editImage && editImageName) {
        editImage.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                editImageName.textContent = this.files[0].name;
            } else {
                editImageName.textContent = '';
            }
        });
    }
});

// 編集モーダルを開く
function openEditModal(messageId, content, image) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    const contentInput = document.getElementById('edit-content');
    const charCount = document.getElementById('edit-char-count');
    const currentImagePreview = document.getElementById('current-image-preview');
    const currentImage = document.getElementById('current-image');
    const removeImageFlag = document.getElementById('remove-image-flag');

    // フォームのアクションを設定
    form.action = `/transactions/messages/${messageId}`;

    // 内容を設定
    contentInput.value = content;
    charCount.textContent = content.length;

    // 画像プレビュー
    if (image) {
        currentImage.src = `/storage/${image}`;
        currentImagePreview.style.display = 'block';
        removeImageFlag.value = '0';
    } else {
        currentImagePreview.style.display = 'none';
    }

    modal.style.display = 'block';
}

// 編集モーダルを閉じる
function closeEditModal() {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    modal.style.display = 'none';
    form.reset();
    document.getElementById('edit-image-name').textContent = '';
}

// 現在の画像を削除
function removeCurrentImage() {
    document.getElementById('current-image-preview').style.display = 'none';
    document.getElementById('remove-image-flag').value = '1';
}

// 削除モーダルを開く
function openDeleteModal(messageId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');

    // フォームのアクションを設定
    form.action = `/transactions/messages/${messageId}`;

    modal.style.display = 'block';
}

// 削除モーダルを閉じる
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
}

// モーダル外クリックで閉じる
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    const ratingModal = document.getElementById('ratingModal');

    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === ratingModal) {
        closeRatingModal();
    }
}

// 評価モーダルを開く
function openRatingModal() {
    const modal = document.getElementById('ratingModal');
    modal.style.display = 'block';
}

// 評価モーダルを閉じる
function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    const form = modal.querySelector('form');
    modal.style.display = 'none';
    form.reset();
}
</script>
@endsection
