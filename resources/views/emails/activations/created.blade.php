@component('mail::message')
# Introduction

# 新規登録していただきありがとうございます！
下記のリンクをクリックしてアカウントの有効化を行ってください。

@component('mail::button', ['url' => $link])
アカウントの有効化
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
