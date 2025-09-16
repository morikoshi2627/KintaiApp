<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KintaiApp</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header-inner">
      <img src="{{ asset('storage/CoachTech_White 1.png') }}" alt="coachtechロゴ">
    </div>
  </header>


  <main class="main-content">
    @yield('content')
  </main>
</body>

</html>