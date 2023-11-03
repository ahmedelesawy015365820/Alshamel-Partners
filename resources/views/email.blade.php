<div>
    @component('mail::message')
    <h1>{{ $data['body'] }}</h1>
    @endcomponent
    {{ config('app.name') }}
</div>
