document.getElementById("search-btn").addEventListener("click", function() {
    const query = document.getElementById("search-bar").value.trim();

    if (query) {
        // Realizamos la solicitud AJAX
        fetch(`../PHP/buscar_cancion.php?query=${query}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let songList = "";
                    data.forEach(song => {
                        songList += `
                            <div class="song">
                                <strong>${song.titulo}</strong> - ${song.artista} <br>
                                <audio controls>
                                    <source src="${song.url}" type="audio/mp3">
                                    Tu navegador no soporta el reproductor de audio.
                                </audio>
                            </div>
                        `;
                    });
                    document.getElementById("song-demo").innerHTML = songList;
                } else {
                    document.getElementById("song-demo").innerHTML = "No se encontraron canciones.";
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
