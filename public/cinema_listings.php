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
        height: 100vh;
        max-height: 100vh;
        overflow: hidden; /* Mengunci agar tidak ada scroll vertikal/horizontal */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1vh 2vw;
        box-sizing: border-box;
    }

    /* HEADER */
    .header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5vh 2vw;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    .header img {
        width: 75px;
        height: auto;
        object-fit: contain;
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 600;
        letter-spacing: 2px;
        text-align: center;
        color: #ffca38;
        text-shadow: 0 3px 4px rgba(0,0,0,0.5);
        margin: 0;
    }

    /* LIST LAYOUT */
    .movie-list {
        width: 85%;
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.2vh;
        flex-grow: 1;
        justify-content: center;
    }

    .movie-item, .movie-now {
        background-color: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 1.2vh 1.5vw;
        display: grid;
        grid-template-columns: 90px 1fr 90px;
        align-items: center;
        gap: 20px;
        border-left: 5px solid #ffc107;
        transition: background 0.2s ease;
    }

    .movie-now:hover {
        background-color: rgba(0, 69, 36, 0.6);
    }

    .poster {
        width: 85px;
        height: 85px;
        border-radius: 12px;
        object-fit: cover;
    }

    .poster-right {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 70px;
        height: 70px;
        object-fit: contain;
        margin-left: auto;
    }

    .movie-info, .movie-info-now {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    @keyframes blink {
        0% { opacity: 0; }
        22.22% { opacity: 1; }
        77.78% { opacity: 1; }
        100% { opacity: 0; }
    }

    .movie-info-now {
        animation: blink 2.5s ease-in-out infinite;
    }

    .movie-title {
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
        color: white;
        margin-bottom: 2px;
    }

    .movie-sub {
        font-size: 0.95rem;
        color: #f3f4f6;
    }
</style>

</head>
<body>

<!-- HEADER -->
<div class="header">
    <img src="img/gunsa.png" alt="Logo 1">
    <div class="header-title">NOW SHOWING</div>
    <img src="img/kompa.jpg" alt="Logo 2" style="border-radius: 99px;">
</div>

<!-- MOVIE LIST -->
<div class="movie-list">
    <div class="movie-item">
        <img class="poster" src="img/ad1.png" alt="Poster 1">
        <div class="movie-info">
            <div class="movie-title">00.00.00 - Stop In Silence</div>
            <div class="movie-sub">30 November 2025 | Pdt. Ima F. Simamora - GKI Melur</div>
            <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
        </div>
        <img class="poster-right" src="img/21.png" alt="21">
    </div>

    <div class="movie-item">
        <img class="poster" src="img/ad2.png" alt="Poster 2">
        <div class="movie-info">
            <div class="movie-title">06.00.00 - Watch the Promise Unfold</div>
            <div class="movie-sub">07 December 2025 | Pnt. Gilbert C. Kristamulyana</div>
            <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
        </div>
        <img class="poster-right" src="img/21.png" alt="21">
    </div>

    <div class="movie-item">
        <img class="poster" src="img/ad3.png" alt="Poster 3">
        <div class="movie-info">
            <div class="movie-title">23.59.59 - Watch & Hold Your Breath</div>
            <div class="movie-sub">14 December 2025 | Pdt. Febe O. Hermanto</div>
            <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
        </div>
        <img class="poster-right" src="img/21.png" alt="21">
    </div>  

    <a href="index.php" style="text-decoration:none; color: white;">
        <div class="movie-now">
            <img class="poster" src="img/ad4.png" alt="Poster 4">
            <div class="movie-info-now">
                <div class="movie-title">00.00.00 - See the Light</div>
                <div class="movie-sub">21 December 2025 | Pdt. Andi Christianto - GKI Cawang</div>
                <div class="movie-sub">GSP 3 Lt. 4 | 10.30 WIB</div>
            </div>
            <img class="poster-right" src="img/21.png" alt="21">
        </div>
    </a>
</div>

</body>
</html>