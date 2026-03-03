<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users – {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; background: #f5f5f5; }
        h1 { color: #333; }
        .error { background: #fff2f2; color: #c00; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
        table { border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.1); border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #1b1b18; color: #fff; font-weight: 600; }
        tr:last-child td { border-bottom: 0; }
        tr:hover td { background: #f9f9f9; }
        .empty { color: #666; padding: 2rem; }
        a { color: #6366f1; }
    </style>
</head>
<body>
    <h1>Users table</h1>

    @if(isset($error))
        <div class="error">{{ $error }}</div>
        <p><a href="/db-test">View raw JSON (db-test)</a></p>
    @elseif($users->isEmpty())
        <p class="empty">No users in the database.</p>
        <p><a href="/db-test">View raw JSON (db-test)</a></p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach(array_keys((array) $users->first()) as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($users as $row)
                    <tr>
                        @foreach((array) $row as $value)
                            <td>{{ $value === null || $value === '' ? '—' : e($value) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="margin-top:1rem;"><a href="/db-test">View as JSON (db-test)</a></p>
    @endif
</body>
</html>
