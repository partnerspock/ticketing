<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Now Playing - Cinema XXI Style (List View)</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    body {
        margin: 0;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-image: url('img/bgcn.png');
        color: white;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        min-height: 100vh;
    }

    /* HEADER */
    .header {
        width: 100%;
        padding-top:2%;
        display: flex;
        gap: 16%;
        justify-content: center;
        align-items: center;
    }

    .header img {
        width: 140px;
        height: auto;
    }

    .header-title {
        font-size: 68px;
        font-weight: 600;
        letter-spacing: 2px;
        text-align: center;
        color: #ffca38;
        text-shadow: 0 3px 4px rgba(0,0,0,0.5);

    }

    /* LIST LAYOUT */
    .movie-list {
        width: 75%;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .movie-item {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 15px 20px;
        display: grid;
        grid-template-columns: 120px 1fr 150px;
        align-items: center;
        gap: 20px;
        border-left: 5px solid #ffc107;
        transition: background 0.2s ease;
    }

    .movie-now {
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        padding: 15px 20px;
        display: grid;
        grid-template-columns: 120px 1fr 150px;
        align-items: center;
        gap: 20px;
        border-left: 5px solid #ffc107;
        transition: background 0.2s ease;
    }

    .movie-now:hover {
        background-color: #004524;
    }

    .movie-info-now {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    @keyframes blink {
     /* Visible Start: 0s (0%) */
        0% {
            opacity: 0;
        }

        /* Fade Out End: at 2s mark (~22.22%) */
        22.22% {
            opacity: 1;
        }

        /* Stay Hidden Start: at 2s mark (~22.22%) */
        /* Stay Hidden End: at 7s mark (~77.78%) */
        77.78% {
            opacity: 1; /* Remains invisible during the 5s dwell time */
        }

        /* Fade In End / Visible End: at 9s mark (100%) */
        100% {
            opacity: 0;
        }
    }

    /* Apply the animation to the element with the 'blinking' class */
    .movie-info-now {
        /* Name of animation | duration | timing function | iteration count */
        animation: blink 2.5s ease-in-out infinite;
    }

    .poster {
        width: 160px;
        height: 160px;
        border-radius: 20px;
        object-fit: cover;
    }

    .poster-right {
        display: flex;
        padding-left: 40px;
        padding-bottom: 30px;
        width: 120px;
        height: 120px;
    }

    .movie-title {
        font-size: 38px;
        font-weight: 700;
        text-align: center;
        color: white;
        margin-bottom: 5px;
    }

    .movie-date {
        text-align: right;
        font-size: 26px;
        color: white;
        font-weight: 600;
    }
    .movie-info {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 5px;
        opacity: 65%;
    }
    .movie-sub {
        font-size: 24px;
        color: #white;
    }

</style>

</head>
<body>

<!-- HEADER -->
<div class="header">
    <img src="img/gunsa.png">

    <div class="header-title">NOW SHOWING</div>

    <img src="img/kompa.jpg" style="border-radius: 99px;">
</div>

<!-- MOVIE LIST -->
<div class="movie-list">
    <div class="movie-item">
    <img class="poster" src="img/ad1.png">

    <div class="movie-info">
        <div class="movie-title">00.00.00 - Stop In Silence</div>
        <div class="movie-sub">30 November 2025 | Pdt. Ima F. Simamora - GKI Melur</div>
        <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
    </div>

    <img class="poster-right" src="img/21.png">
    </div>

    <div class="movie-item">
    <img class="poster" src="img/ad2.png">

    <div class="movie-info">
        <div class="movie-title">06.00.00 - Watch the Promise Unfold</div>
        <div class="movie-sub">07 December 2025 | Pnt. Gilbert C. Kristamulyana</div>
        <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
    </div>

    <img class="poster-right" src="img/21.png">
    </div>
    </a>

    <div class="movie-item">
    <img class="poster" src="img/ad3.png">

    <div class="movie-info">
        <div class="movie-title">23.59.59 - Watch & Hold Your Breath</div>
        <div class="movie-sub">14 December 2025 | Pdt. Febe O. Hermanto</div>
        <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
    </div>

    <img class="poster-right" src="img/21.png">
    </div>  

    <a href=" index.php" style="text-decoration:none; color: white;">
    <div class="movie-now">
    <img class="poster" src="img/ad4.png">

    <div class="movie-info-now">
        <div class="movie-title">00.00.00 - See the Light</div>
        <div class="movie-sub">21 December 2025 | Pdt. Andi Christianto - GKI Cawang</div>
        <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
    </div>

    <img class="poster-right" src="img/21.png">
    </div>
    </a>
</div>

</body>
</html>
