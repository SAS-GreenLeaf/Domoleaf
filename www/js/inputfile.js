// ajout de la classe JS à HTML
document.querySelector("html").classList.add('js');

// initialisation des variables
var fileInput  = document.querySelector(".input-file"),
    button     = document.querySelector(".input-file-trigger"),
    the_return = document.querySelector("#project-file-name");

// action lorsque la "barre d'espace" ou "Entrée" est pressée
button.addEventListener( "keydown", function( event ) {
    if ( event.keyCode == 13 || event.keyCode == 32 ) {
        fileInput.focus();
    }
});

// action lorsque le label est cliqué
button.addEventListener( "click", function( event ) {
   fileInput.focus();
   return false;
});

// affiche un retour visuel dès que input:file change
fileInput.addEventListener( "change", function( event ) {
  // console.log(this);
  the_return.innerHTML = this.value.split('\\')[2];
});
