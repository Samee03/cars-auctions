<x-mail::message>
# Account Verification

Your Account is successfully Verified.
You can view **Verification Badge** on right corner of the site.

<x-mail::button :url="$url">
    Visit JP-Prime
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
