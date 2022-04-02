<x-tests.test>
    <x-slot name="header">
        ヘッダーかよ
    </x-slot>
    テスト1
    <x-tests.card title="タイトル" content="本文です" :message="$message" />
    <x-tests.card title="タイトル2"  />
    <x-tests.card title="タイトル3"  class="bg-red-300"/>
</x-tests.test>
