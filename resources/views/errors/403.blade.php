<!DOCTYPE html>
<meta charset="UTF-8">

<head>
    <title>Ah Ah Ah, you didn't say the magic word!</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<html>

<body>
    <div class="container" onclick="playSound()">
        <div class="row justify-content-center">
            <div class="col-md-5 px-7">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h3>Ah Ah Ah, you didn't say the magic word!</h3>
                    </div>
                    <div class="card-body">
                        <div class="nedry">
                            <img class="nedry-body" src="/nedry/body.png">
                            <img class="nedry-head" src="/nedry/head.png">
                            <img class="nedry-hand" src="/nedry/hand.png">
                        </div>
                        <audio id="player" autoplay loop>
                            <source src="/nedry/ahahah.mp3" type="audio/mp3">
                        </audio>
                    </div>
                    {{-- <input id="unmuteButton" type="button" onclick="playSound()" value="🔈 Unmute" /> --}}
                </div>
            </div>
        </div>
    </div>
</body>
<link rel="stylesheet" href="{{ asset('nedry/style.css') }}">


<script>
    const sound = document.getElementById('player');

    sound.addEventListener('play', (event) => {
        document.getElementById("unmuteButton").hidden = true;
    });
    sound.addEventListener('pause', (event) => {
        document.getElementById("unmuteButton").hidden = false;
    });

    function playSound() {
        document.getElementById("player").play();
    }
</script>

</html>
