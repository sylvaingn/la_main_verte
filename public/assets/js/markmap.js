$(document).ready(() => {
    // // Pour la geolocalisation
    // if (navigator.geolocation) {
    //     const position = navigator.geolocation.getCurrentPosition((position) => {
    //         const latitude = position.coords.latitude;
    //         const longitude = position.coords.longitude;
    //         console.log(latitude);
    //         console.log(longitude);
    //     });
    // } else {
    //     $("#geolocation").hide();
    // }

    // Pour la carte

    alert("affichage carte");
    
    var mapboxTiles = L.tileLayer(
        "http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png", {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>',
        }
    );

    const producteursLayer = L.layerGroup();

    const map = L.map("mapid")
        .addLayer(mapboxTiles)
        .addLayer(producteursLayer)
        .setView([45.7673014, 4.8315513], 10);

    $("#searchProd").on("submit", (e) => {
        e.preventDefault();
        alert("chercher");
    //     const postcode = $("#form_postcode").val();
    //     if (!isNaN(postcode)) { // vérification validité CP
            $.ajax({
                url: $(e.currentTarget).data("url"),
                type: "POST",
                dataType: "json",
                async: true,
                data: {
                    postcode: postcode,
                },
                success: (datas) => {
                    console.log(datas);

                    producteursLayer.clearLayers();

                    for (data of datas) { //affichage des markers sur la carte
                        const marker = L.marker([data.latitude, data.longitude]);
                        producteursLayer.addLayer(marker);
                        marker.on("mouseover", function () {
                            this.bindPopup(data.name).openPopup(); //actions à réaliser sur passage sur le marker
                        });
                    }
                },
                error: (xhr, textStatus, errorThrown) => {
                    alert(errorThrown);
                },
            });
    //     } else {
    //         return false;
    //     }
    });

});
