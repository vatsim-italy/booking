<footer class="app-footer">
    <span>
        <span class="fa fa-copyright"></span>
        @php
            //An script to generate the copyright date using the server's year
            $fromYear = 2018;
            $thisYear = (int) date('Y');
        @endphp
        {{ $fromYear . (($fromYear != $thisYear) ? '-' . $thisYear : '') }} Booking System by Dave Roverts (1186831). Used
        and maintained by <a href="{{ config('app.division_url') }}" target="_blank" rel="noreferrer noopener">{{ config('app.division') }}</a>
    </span>
    <a href="{{ config('app.division_url') }}" target="_blank" rel="noreferrer noopener">
        <img src="https://cdn.vatita.net/Vatita%20logo%20COLOR_black.png" height="45" alt="{{ config('app.division') }} logo">
    </a>
</footer>
