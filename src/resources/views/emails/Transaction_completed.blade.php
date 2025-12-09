<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引完了のお知らせ</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }
        .item-info {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }
        .item-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .buyer-name {
            color: #666;
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            background-color: #FF5555;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #ff3333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COACHTECHフリマ</h1>
    </div>

    <div class="content">
        <p>{{ $item->seller->name }} 様</p>

        <p>いつもCOACHTECHフリマをご利用いただき、ありがとうございます。</p>

        <p>以下の商品の取引が購入者により完了されました。<br>
        お取引内容をご確認いただき、購入者の評価をお願いいたします。</p>

        <div class="item-info">
            <div class="item-name">商品名: {{ $item->name }}</div>
            <div class="buyer-name">購入者: {{ $buyer->name }} 様</div>
        </div>

        <p>購入者からの評価をお待ちしております。</p>

        <center>
            <a href="{{ route('transactions.show', $item->id) }}" class="button">
                取引画面を開く
            </a>
        </center>

        <p>※このメールは自動送信されています。<br>
        返信されても対応できませんのでご了承ください。</p>
    </div>

</body>
</html>
