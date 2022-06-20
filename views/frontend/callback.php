<?php
$manager                    = new \Tollbridge\Paywall\Manager();
$name                       = _e( 'Authenticating', 'tollbridge' ) . '...';
$branding_button_background = '#2D7ED5';

if ( $manager->allAccountSettingsAreEntered() ) {
    $name                       = $manager->getConfig( 'name' );
    $branding_button_background = $manager->getConfig( 'branding_button_background', '#2D7ED5' );
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>{{ $name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body {margin: 0 }a {background-color: transparent }b {font-weight: bolder }img {border-style: none }dl {margin: 0 }button {background-color: transparent;background-image: none }button:focus {outline: 5px auto -webkit-focus-ring-color }ul {list-style: none;margin: 0;padding: 0 }*, :after, :before {box-sizing: border-box;border: 0 solid #e2e8f0 }img {border-style: solid }img, svg {display: block;vertical-align: middle }img {max-width: 100%;height: auto }.block {display: block }.flex {display: flex }.items-center {align-items: center }.justify-center {justify-content: center }.font-semibold {font-weight: 600 }.h-screen {height: 100vh }.text-2xl {font-size: 1.5rem }.my-12 {margin-top: 3rem;margin-bottom: 3rem }.p-8 {padding: 2rem }.py-8 {padding-top: 2rem;padding-bottom: 2rem }.text-center {text-align: center }.text-gray-800 {color: #19202b }.w-screen {width: 100vw }body {font-family: Source Sans Pro, sans-serif;font-size: 14px;line-height: 18px }.dot-flashing {position: relative;width: 10px;height: 10px;border-radius: 5px;background-color: {{$branding_button_background}};color: {{$branding_button_background}};animation: dotFlashing 1s infinite linear alternate;animation-delay: .5s;}.dot-flashing::before, .dot-flashing::after {content: '';display: inline-block;position: absolute;top: 0;}.dot-flashing::before {left: -15px;width: 10px;height: 10px;border-radius: 5px;background-color: {{$branding_button_background}};color: {{$branding_button_background}};animation: dotFlashing 1s infinite alternate;animation-delay: 0s;}.dot-flashing::after {left: 15px;width: 10px;height: 10px;border-radius: 5px;background-color: {{$branding_button_background}};color: {{$branding_button_background}};animation: dotFlashing 1s infinite alternate;animation-delay: 1s;}@keyframes dotFlashing {0% {background-color: {{$branding_button_background}};}50%, 100% {background-color: #ebe6ff;}}</style></head>
<body>
    <?php
    require_once plugin_dir_path( __DIR__ ) . 'frontend/js-payload.php';
    ?>
    <div class="flex w-screen h-screen justify-center items-center p-8">
        <div class="block my-12 py-8 text-center text-gray-800 text-2xl font-semibold">
            <div class="dot-flashing"></div>
        </div>
    </div>
</body>
</html>
