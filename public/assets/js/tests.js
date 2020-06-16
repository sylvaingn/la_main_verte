// Chargement jQuery
if (!(typeof jQuery == 'undefined')) {
    console.log('jQuery est chargé')
};
console.log(typeof jQuery);

// création $.noConflict pour écriture avec $ pour librairie jQuery
$.noConflict();
jQuery(document).ready(function ($) { // code jQuery du script

    $("#smallCart").hide();
    // fonction "ajouter un produit dans le panier" :
    // placer l'id "addStockOnCart" sur le bouton "+" de la page stock pour commande
    $("#addStockOnCart").on("click", (e) => {
        // ajout d'un élément au panier
        e.preventDefault();
        alert("clic sur ajouter");
        // $("#smallCart").show();
        // si stock suffisant, sinon message avec qté = stock
        // 1. on incrémente le panier
        // 2. on diminue la valeur en stock
    });

    //     // fonction "supprimer un produit du panier" :
    //     // placer l'id "suppStockOffCart" sur le bouton "-" de la page stock pour commande
    //     $("#suppStockOffCart").on("click", (e) => {
    //         e.preventDefault();
    //         alert("clic sur supprimer");

    //     // if (!isNaN(postcode)) { // vérification validité CP
    //         $.ajax({
    //             url: "{{ path('/panier/testAjax')}}",
    //             // type: "POST",
    //             dataType: "json",
    //             async: true,
    //             data: {
    //                 panier: panier,
    //             },
    //             success: (datas) => {
    //                 console.log(datas);

    //                 // producteursLayer.clearLayers();

    //                 for (data of datas) { //affichage des markers sur la carte
    //                     // const marker = L.marker([data.latitude, data.longitude]);
    //                     // producteursLayer.addLayer(marker);
    //                     // marker.on("mouseover", function () {
    //                     //     this.bindPopup(data.name).openPopup(); //actions à réaliser sur passage sur le marker
    //                     // });
    //                 }
    //             },
    //             error: (xhr, textStatus, errorThrown) => {
    //                 alert(errorThrown);
    //             },
    //         });
    //     // } else {
    //     //     return false;
    //     // }

    //    // suppression d'un élément du panier
    //         // 1. on décrémente le panier
    //         // 2. on réinjecte la valeur dans le stock
    //     });

    // fonction détail du panier depuis navbar
    $("#showCartDetail").on("click", (e) => {
        // affichage en fenetre pop-up du contenu réduit du panier
        // le contenu est défini dans le htlm.twig
        // la div correspondante prend l'attribut hide ou show
        // 1 click on affiche : toggle
        $("#popupcart").toggle();
        // 1 click on cache : hide
        $("#popupcart").hide();
    });


}); // ligne de fin de code