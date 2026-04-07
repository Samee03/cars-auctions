<x-mail::message>
    # Account Verification

    Your Account is successfully Verified. <br>
    You can view <b>Verification Badge</b> on right corner of the site.

    <x-mail::button :url="$url">
        Visit Our Site
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
