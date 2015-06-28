@extends('layout')

@section('content-body')
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Token info</div>

        @if ($picasa->hasValidToken())
            <table class="table">
                <tr>
                    <th>Token</th>
                    <td><code>{{ $picasa->getTokenAccessKey() }}</code></td>
                </tr>
                <tr>
                    <th>Expires</th>
                    <td><code>{{ \Carbon\Carbon::createFromTimestamp($picasa->getTokenExpires())->format('H:i:s d/m/Y') }}</code></td>
                </tr>
                <tr>
                    <th>Refresh token</th>
                    <td><code>{{ $picasa->getRefreshToken() }}</code></td>
                </tr>
            </table>
        @else
            <div class="panel-body">
                No token found.
            </div>
        @endif
    </div>

    @if (!$picasa->hasValidToken())
    <a href="{{ $login_url }}" class="btn btn-block btn-info">Login to get new access token</a>
    @endif
@stop