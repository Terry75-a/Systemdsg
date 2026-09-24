const Translations = {

    es : {
        "language": "Idioma",
        "theme": "Tema",
        "Clear": "Claro",
        "Dark" : "Oscuro",
        "Spanish": "Español",
        "Save changes": "Guardar cambios",
        "Configuration": "Configuracion",
        "Logout": "Cerrar Sesion",
        "Profile": "Perfil",
        "Calendar": "Calendario",
        "Customers": "Clientes",
        "Home": "Inicio",
        "Configuration": "Configuración"
    },
    en :{
        "language": "Language",
        "theme": "Theme",
        "Clear":  "Clear",
        "Dark": "Dark",
        "Spanish": "Spanish",
        "Save changes": "Save changes",
        "Configuration": "Configuration",
        "Logout": "Logout",
        "Profile": "Profile",
        "Calendar": "Calendar",
        "Customers": "Customers",
        "Home": "Home",
        "Configuration": "Configuration"


         
        }
    
};

const selector = document.getElementById('lenguajeSelector');

selector.addEventListener('change', function(){
    const selectedLanguage = selector.value;

    document.querySelectorAll('[data-key]').forEach(function(element){
        const key = element.getAttribute('data-key');
        element.textContent = Translations[selectedLanguage][key];

    });

    document.querySelectorAll('[data-placeholder]').forEach(function(element){
        const key = element.getAttribute('data-placeholder')
        element.setAttribute('placeholder' , Translations[selectedLanguage][key])

});




})